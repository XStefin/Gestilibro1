<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format = 'json';

    private function leerJson()
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false) {
            $rawBody = '';
        }

        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return [
                'ok' => false,
                'response' => $this->response->setStatusCode(400)->setJSON([
                    'error' => 'JSON inválido',
                    'detalle' => json_last_error_msg(),
                    'body_recibido' => $rawBody,
                    'content_type' => $this->request->getHeaderLine('Content-Type')
                ])
            ];
        }

        return [
            'ok' => true,
            'data' => $data
        ];
    }

    private function enviarCorreo($correoDestino, $asunto, $mensaje)
    {
        $apiKey = getenv('RESEND_API_KEY');
        $fromEmail = getenv('RESEND_FROM_EMAIL') ?: 'GestiLibro <onboarding@resend.dev>';

        if (!$apiKey) {
            return [
                'ok' => false,
                'debug' => 'Falta configurar RESEND_API_KEY.',
                'exception' => null,
                'variables_faltantes' => ['RESEND_API_KEY'],
                'connection_test' => null,
                'config_email' => [
                    'provider' => 'resend',
                    'RESEND_API_KEY' => 'NO CONFIGURADA',
                    'RESEND_FROM_EMAIL' => $fromEmail
                ]
            ];
        }

        $htmlMensaje = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

        $payload = [
            'from' => $fromEmail,
            'to' => [$correoDestino],
            'subject' => $asunto,
            'html' => "
                <div style='font-family: Arial, sans-serif; line-height: 1.5; color: #222;'>
                    {$htmlMensaje}
                </div>
            ",
            'text' => $mensaje
        ];

        try {
            $ch = curl_init('https://api.resend.com/emails');

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($response === false) {
                return [
                    'ok' => false,
                    'debug' => 'Error cURL al conectar con Resend.',
                    'exception' => [
                        'message' => $curlError
                    ],
                    'variables_faltantes' => [],
                    'connection_test' => [
                        'provider' => 'resend',
                        'connected' => false,
                        'error' => $curlError
                    ],
                    'config_email' => [
                        'provider' => 'resend',
                        'RESEND_API_KEY' => 'CONFIGURADA',
                        'RESEND_FROM_EMAIL' => $fromEmail
                    ]
                ];
            }

            $decodedResponse = json_decode($response, true);

            if ($httpCode < 200 || $httpCode >= 300) {
                log_message('error', 'Error Resend HTTP ' . $httpCode . ': ' . $response);

                return [
                    'ok' => false,
                    'debug' => $decodedResponse ?: $response,
                    'exception' => null,
                    'variables_faltantes' => [],
                    'connection_test' => [
                        'provider' => 'resend',
                        'connected' => true,
                        'http_code' => $httpCode
                    ],
                    'config_email' => [
                        'provider' => 'resend',
                        'RESEND_API_KEY' => 'CONFIGURADA',
                        'RESEND_FROM_EMAIL' => $fromEmail
                    ]
                ];
            }

            return [
                'ok' => true,
                'debug' => $decodedResponse,
                'exception' => null,
                'variables_faltantes' => [],
                'connection_test' => [
                    'provider' => 'resend',
                    'connected' => true,
                    'http_code' => $httpCode
                ],
                'config_email' => [
                    'provider' => 'resend',
                    'RESEND_API_KEY' => 'CONFIGURADA',
                    'RESEND_FROM_EMAIL' => $fromEmail
                ]
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Excepción enviando correo con Resend: ' . $e->getMessage());

            return [
                'ok' => false,
                'debug' => null,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'variables_faltantes' => [],
                'connection_test' => [
                    'provider' => 'resend',
                    'connected' => false
                ],
                'config_email' => [
                    'provider' => 'resend',
                    'RESEND_API_KEY' => 'CONFIGURADA',
                    'RESEND_FROM_EMAIL' => $fromEmail
                ]
            ];
        }
    }

    private function reenviarPinUsuario(array $usuario, string $correo)
    {
        $pinAnterior = $usuario['pin'] ?? '';
        $activeAnterior = (int) ($usuario['active'] ?? 0);

        $nuevoPin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->model->update($usuario['id_usuario'], [
            'pin' => $nuevoPin,
            'active' => 0
        ]);

        $nombreUsuario = $usuario['nombre'] ?? $usuario['username'] ?? 'Usuario';

        $mensajeCorreo = "
Hola {$nombreUsuario},

Ya existe una cuenta registrada con este correo, pero aún no ha sido verificada.

Tu nuevo PIN de verificación es: {$nuevoPin}

Ingresa este PIN para activar tu cuenta.
        ";

        $resultadoCorreo = $this->enviarCorreo(
            $correo,
            'Reenvío de PIN de verificación',
            $mensajeCorreo
        );

        if (!$resultadoCorreo['ok']) {
            $this->model->update($usuario['id_usuario'], [
                'pin' => $pinAnterior,
                'active' => $activeAnterior
            ]);

            return [
                'ok' => false,
                'response' => $this->response->setStatusCode(500)->setJSON([
                    'message' => 'El correo ya está registrado, pero no se pudo reenviar el PIN. Intenta nuevamente.',
                    'correo_destino' => $correo,
                    'debug_email' => $resultadoCorreo['debug'] ?? null,
                    'exception' => $resultadoCorreo['exception'] ?? null,
                    'variables_faltantes' => $resultadoCorreo['variables_faltantes'] ?? [],
                    'connection_test' => $resultadoCorreo['connection_test'] ?? null,
                    'config_email' => $resultadoCorreo['config_email'] ?? null
                ])
            ];
        }

        return [
            'ok' => true,
            'response' => $this->response->setStatusCode(200)->setJSON([
                'message' => 'Este correo ya estaba registrado, pero la cuenta no estaba activa. Se reenvió un nuevo PIN de verificación.',
                'requiresVerification' => true,
                'correo' => $correo
            ])
        ];
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);

        if (!$data) {
            return $this->failNotFound('Usuario no encontrado');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $nombre = trim($data['nombre'] ?? '');
        $apellido = trim($data['apellido'] ?? '');
        $correo = strtolower(trim($data['correo'] ?? $data['email'] ?? ''));
        $username = trim($data['username'] ?? '');
        $contrasena = $data['contrasena'] ?? '';

        if ($nombre === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El nombre es obligatorio',
                'field' => 'nombre'
            ]);
        }

        if ($apellido === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El apellido es obligatorio',
                'field' => 'apellido'
            ]);
        }

        if ($correo === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El correo es obligatorio',
                'field' => 'correo'
            ]);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El formato del correo no es válido',
                'field' => 'correo'
            ]);
        }

        if ($username === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El username es obligatorio',
                'field' => 'username'
            ]);
        }

        if ($contrasena === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'La contraseña es obligatoria',
                'field' => 'contrasena'
            ]);
        }

        $usuarioExistente = $this->model->where('correo', $correo)->first();

        if ($usuarioExistente) {
            if ((int) ($usuarioExistente['active'] ?? 0) === 0) {
                $resultadoReenvio = $this->reenviarPinUsuario($usuarioExistente, $correo);
                return $resultadoReenvio['response'];
            }

            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Este correo ya está registrado',
                'field' => 'correo'
            ]);
        }

        if ($this->model->where('username', $username)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Este nombre de usuario ya está en uso',
                'field' => 'username'
            ]);
        }

        $esRegistro = isset($data['esRegistro']) ? (bool) $data['esRegistro'] : false;

        $pin = $esRegistro
            ? str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)
            : trim($data['pin'] ?? '');

        $datosInsertar = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'username' => $username,
            'contrasena' => password_hash($contrasena, PASSWORD_BCRYPT),
            'pin' => $pin,
            'active' => $esRegistro ? 0 : (isset($data['active']) ? (int) $data['active'] : 1),
            'rol' => $esRegistro ? 'Estudiante' : ($data['rol'] ?? 'Administrador')
        ];

        try {
            $insertId = $this->model->insert($datosInsertar, true);

            if (!$insertId) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'No fue posible crear el usuario',
                    'errors' => $this->model->errors()
                ]);
            }

            if ($esRegistro) {
                $mensajeCorreo = "
Hola {$nombre},

Tu cuenta fue creada correctamente.

Tu PIN de verificación es: {$pin}

Ingresa este PIN para activar tu cuenta.

Si no realizaste este registro, puedes ignorar este mensaje.
                ";

                $resultadoCorreo = $this->enviarCorreo(
                    $correo,
                    'PIN de verificación de tu cuenta',
                    $mensajeCorreo
                );

                if (!$resultadoCorreo['ok']) {
                    $this->model->delete($insertId);

                    return $this->response->setStatusCode(500)->setJSON([
                        'message' => 'No se pudo enviar el correo con el PIN. Intenta nuevamente.',
                        'correo_destino' => $correo,
                        'debug_email' => $resultadoCorreo['debug'] ?? null,
                        'exception' => $resultadoCorreo['exception'] ?? null,
                        'variables_faltantes' => $resultadoCorreo['variables_faltantes'] ?? [],
                        'connection_test' => $resultadoCorreo['connection_test'] ?? null,
                        'config_email' => $resultadoCorreo['config_email'] ?? null
                    ]);
                }

                return $this->respondCreated([
                    'message' => 'Usuario registrado correctamente. Revisa tu correo para el PIN de verificación.',
                    'data' => [
                        'id_usuario' => $insertId,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'correo' => $correo,
                        'username' => $username,
                        'rol' => $datosInsertar['rol'],
                        'active' => $datosInsertar['active']
                    ]
                ]);
            }

            return $this->respondCreated([
                'message' => 'Usuario creado correctamente',
                'data' => [
                    'id_usuario' => $insertId,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'correo' => $correo,
                    'username' => $username,
                    'rol' => $datosInsertar['rol'],
                    'active' => $datosInsertar['active']
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Error creando usuario: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Error interno al crear el usuario',
                'error' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine()
            ]);
        }
    }

    public function verifyPin()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $correo = strtolower(trim($data['correo'] ?? $data['email'] ?? ''));
        $pin = trim($data['pin'] ?? '');

        if ($correo === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El correo es obligatorio'
            ]);
        }

        if ($pin === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El PIN es obligatorio'
            ]);
        }

        $usuario = $this->model->where('correo', $correo)->first();

        if (!$usuario) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'No se encontró un usuario con ese correo'
            ]);
        }

        if ((int) ($usuario['active'] ?? 0) === 1) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'La cuenta ya se encuentra activa'
            ]);
        }

        if ((string) ($usuario['pin'] ?? '') !== $pin) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'PIN incorrecto'
            ]);
        }

        $this->model->update($usuario['id_usuario'], [
            'active' => 1,
            'pin' => ''
        ]);

        return $this->respond([
            'message' => 'Cuenta verificada correctamente'
        ]);
    }

    public function resendPin()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $correo = strtolower(trim($data['correo'] ?? $data['email'] ?? ''));

        if ($correo === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El correo es obligatorio'
            ]);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El formato del correo no es válido'
            ]);
        }

        $usuario = $this->model->where('correo', $correo)->first();

        if (!$usuario) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'No se encontró un usuario con ese correo'
            ]);
        }

        if ((int) ($usuario['active'] ?? 0) === 1) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'La cuenta ya se encuentra activa'
            ]);
        }

        $resultadoReenvio = $this->reenviarPinUsuario($usuario, $correo);
        return $resultadoReenvio['response'];
    }

    public function update($id = null)
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $usuario = $this->model->find($id);

        if (!$usuario) {
            return $this->failNotFound('Usuario no encontrado');
        }

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se recibieron datos para actualizar'
            ]);
        }

        $datosActualizar = [];

        if (array_key_exists('nombre', $data)) {
            $nombre = trim($data['nombre']);

            if ($nombre === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El nombre no puede estar vacío',
                    'field' => 'nombre'
                ]);
            }

            $datosActualizar['nombre'] = $nombre;
        }

        if (array_key_exists('apellido', $data)) {
            $apellido = trim($data['apellido']);

            if ($apellido === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El apellido no puede estar vacío',
                    'field' => 'apellido'
                ]);
            }

            $datosActualizar['apellido'] = $apellido;
        }

        if (array_key_exists('correo', $data) || array_key_exists('email', $data)) {
            $correo = strtolower(trim($data['correo'] ?? $data['email'] ?? ''));

            if ($correo === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El correo no puede estar vacío',
                    'field' => 'correo'
                ]);
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El formato del correo no es válido',
                    'field' => 'correo'
                ]);
            }

            $correoExistente = $this->model
                ->where('correo', $correo)
                ->where('id_usuario !=', $id)
                ->first();

            if ($correoExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este correo ya está registrado',
                    'field' => 'correo'
                ]);
            }

            $datosActualizar['correo'] = $correo;
        }

        if (array_key_exists('username', $data)) {
            $username = trim($data['username']);

            if ($username === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El username no puede estar vacío',
                    'field' => 'username'
                ]);
            }

            $usernameExistente = $this->model
                ->where('username', $username)
                ->where('id_usuario !=', $id)
                ->first();

            if ($usernameExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este nombre de usuario ya está en uso',
                    'field' => 'username'
                ]);
            }

            $datosActualizar['username'] = $username;
        }

        if (array_key_exists('contrasena', $data)) {
            if (trim($data['contrasena']) !== '') {
                $datosActualizar['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT);
            }
        }

        if (array_key_exists('rol', $data)) {
            $datosActualizar['rol'] = $data['rol'];
        }

        if (array_key_exists('active', $data)) {
            $datosActualizar['active'] = (int) $data['active'];
        }

        if (array_key_exists('pin', $data)) {
            $datosActualizar['pin'] = trim($data['pin']);
        }

        if (empty($datosActualizar)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No hay campos válidos para actualizar'
            ]);
        }

        $this->model->update($id, $datosActualizar);

        return $this->respond([
            'message' => 'Usuario actualizado correctamente',
            'data' => [
                'id_usuario' => $id,
                'nombre' => $datosActualizar['nombre'] ?? $usuario['nombre'],
                'apellido' => $datosActualizar['apellido'] ?? $usuario['apellido'],
                'correo' => $datosActualizar['correo'] ?? $usuario['correo'],
                'username' => $datosActualizar['username'] ?? $usuario['username'],
                'rol' => $datosActualizar['rol'] ?? $usuario['rol'],
                'active' => $datosActualizar['active'] ?? $usuario['active']
            ]
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuario no encontrado');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
