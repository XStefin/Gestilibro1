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
        $email = \Config\Services::email();

        $smtpHost = getenv('EMAIL_SMTP_HOST') ?: getenv('email.SMTPHost');
        $smtpUser = getenv('EMAIL_SMTP_USER') ?: getenv('email.SMTPUser');
        $smtpPass = getenv('EMAIL_SMTP_PASS') ?: getenv('email.SMTPPass');
        $smtpPort = getenv('EMAIL_SMTP_PORT') ?: getenv('email.SMTPPort') ?: 587;
        $smtpCrypto = getenv('EMAIL_SMTP_CRYPTO') ?: getenv('email.SMTPCrypto') ?: 'tls';

        $fromEmail = getenv('EMAIL_FROM') ?: $smtpUser;
        $fromName = getenv('EMAIL_FROM_NAME') ?: 'GestiLibro';

        $variablesFaltantes = [];

        if (!$smtpHost) {
            $variablesFaltantes[] = 'EMAIL_SMTP_HOST';
        }

        if (!$smtpUser) {
            $variablesFaltantes[] = 'EMAIL_SMTP_USER';
        }

        if (!$smtpPass) {
            $variablesFaltantes[] = 'EMAIL_SMTP_PASS';
        }

        if (!$fromEmail) {
            $variablesFaltantes[] = 'EMAIL_FROM';
        }

        if (!empty($variablesFaltantes)) {
            log_message('error', 'Configuración SMTP incompleta. Faltan: ' . implode(', ', $variablesFaltantes));

            return [
                'ok' => false,
                'debug' => 'Configuración SMTP incompleta.',
                'exception' => null,
                'variables_faltantes' => $variablesFaltantes,
                'config_email' => [
                    'EMAIL_SMTP_HOST' => $smtpHost ?: null,
                    'EMAIL_SMTP_USER' => $smtpUser ?: null,
                    'EMAIL_SMTP_PORT' => (int) $smtpPort,
                    'EMAIL_SMTP_CRYPTO' => $smtpCrypto,
                    'EMAIL_FROM' => $fromEmail ?: null,
                    'EMAIL_FROM_NAME' => $fromName,
                    'EMAIL_SMTP_PASS' => $smtpPass ? 'CONFIGURADA' : 'NO CONFIGURADA'
                ]
            ];
        }

        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $smtpHost,
            'SMTPUser' => $smtpUser,
            'SMTPPass' => $smtpPass,
            'SMTPPort' => (int) $smtpPort,
            'SMTPCrypto' => $smtpCrypto,
            'mailType' => 'text',
            'charset' => 'UTF-8',
            'wordWrap' => true,
            'newline' => "\r\n",
            'CRLF' => "\r\n",
            'SMTPTimeout' => 20
        ];

        $email->initialize($config);

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($correoDestino);
        $email->setSubject($asunto);
        $email->setMessage($mensaje);

        try {
            if (!$email->send()) {
                $debugEmail = $email->printDebugger([
                    'headers',
                    'subject',
                    'body',
                    'smtp'
                ]);
                log_message('error', 'Error enviando correo a: ' . $correoDestino);
                log_message('error', 'Debug Email: ' . print_r($debugEmail, true));

                return [
                    'ok' => false,
                    'debug' => $debugEmail,
                    'exception' => null,
                    'variables_faltantes' => [],
                    'config_email' => [
                        'EMAIL_SMTP_HOST' => $smtpHost,
                        'EMAIL_SMTP_USER' => $smtpUser,
                        'EMAIL_SMTP_PORT' => (int) $smtpPort,
                        'EMAIL_SMTP_CRYPTO' => $smtpCrypto,
                        'EMAIL_FROM' => $fromEmail,
                        'EMAIL_FROM_NAME' => $fromName,
                        'EMAIL_SMTP_PASS' => 'CONFIGURADA'
                    ]
                ];
            }

            return [
                'ok' => true,
                'debug' => null,
                'exception' => null,
                'variables_faltantes' => [],
                'config_email' => [
                    'EMAIL_SMTP_HOST' => $smtpHost,
                    'EMAIL_SMTP_USER' => $smtpUser,
                    'EMAIL_SMTP_PORT' => (int) $smtpPort,
                    'EMAIL_SMTP_CRYPTO' => $smtpCrypto,
                    'EMAIL_FROM' => $fromEmail,
                    'EMAIL_FROM_NAME' => $fromName,
                    'EMAIL_SMTP_PASS' => 'CONFIGURADA'
                ]
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Excepción enviando correo a ' . $correoDestino . ': ' . $e->getMessage());

            return [
                'ok' => false,
                'debug' => null,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'variables_faltantes' => [],
                'config_email' => [
                    'EMAIL_SMTP_HOST' => $smtpHost,
                    'EMAIL_SMTP_USER' => $smtpUser,
                    'EMAIL_SMTP_PORT' => (int) $smtpPort,
                    'EMAIL_SMTP_CRYPTO' => $smtpCrypto,
                    'EMAIL_FROM' => $fromEmail,
                    'EMAIL_FROM_NAME' => $fromName,
                    'EMAIL_SMTP_PASS' => 'CONFIGURADA'
                ]
            ];
        }
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

        if ($this->model->where('correo', $correo)->first()) {
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
                        'debug_email' => $resultadoCorreo['debug'],
                        'exception' => $resultadoCorreo['exception'],
                        'variables_faltantes' => $resultadoCorreo['variables_faltantes'],
                        'config_email' => $resultadoCorreo['config_email'],
                        'configuracion_revisar' => [
                            'EMAIL_SMTP_HOST',
                            'EMAIL_SMTP_USER',
                            'EMAIL_SMTP_PASS',
                            'EMAIL_SMTP_PORT',
                            'EMAIL_SMTP_CRYPTO',
                            'EMAIL_FROM',
                            'EMAIL_FROM_NAME'
                        ]
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

        $pinAnterior = $usuario['pin'] ?? '';
        $activeAnterior = (int) ($usuario['active'] ?? 0);

        $nuevoPin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->model->update($usuario['id_usuario'], [
            'pin' => $nuevoPin,
            'active' => 0
        ]);

        $mensajeCorreo = "
Hola {$usuario['username']},

Solicitaste un nuevo PIN de verificación.

Tu nuevo PIN es: {$nuevoPin}

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

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'No se pudo reenviar el PIN. Intenta nuevamente.',
                'correo_destino' => $correo,
                'debug_email' => $resultadoCorreo['debug'],
                'exception' => $resultadoCorreo['exception'],
                'variables_faltantes' => $resultadoCorreo['variables_faltantes'],
                'config_email' => $resultadoCorreo['config_email'],
                'configuracion_revisar' => [
                    'EMAIL_SMTP_HOST',
                    'EMAIL_SMTP_USER',
                    'EMAIL_SMTP_PASS',
                    'EMAIL_SMTP_PORT',
                    'EMAIL_SMTP_CRYPTO',
                    'EMAIL_FROM',
                    'EMAIL_FROM_NAME'
                ]
            ]);
        }

        return $this->respond([
            'message' => 'Código reenviado correctamente'
        ]);
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
