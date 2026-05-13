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

        $correo = trim($data['correo'] ?? '');
        $username = trim($data['username'] ?? '');
        $contrasena = $data['contrasena'] ?? '';

        if ($correo === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El correo es obligatorio',
                'field'   => 'correo',
            ]);
        }

        if ($username === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El username es obligatorio',
                'field'   => 'username',
            ]);
        }

        if ($contrasena === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'La contraseña es obligatoria',
                'field'   => 'contrasena',
            ]);
        }

        if ($this->model->where('correo', $correo)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Este correo ya está registrado',
                'field'   => 'correo',
            ]);
        }

        if ($this->model->where('username', $username)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Este nombre de usuario ya está en uso',
                'field'   => 'username',
            ]);
        }

        $esRegistro = isset($data['esRegistro']) ? (bool) $data['esRegistro'] : false;

        $pin = $esRegistro
            ? str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)
            : trim($data['pin'] ?? '');

        $datosInsertar = [
            'correo'     => $correo,
            'username'   => $username,
            'contrasena' => password_hash($contrasena, PASSWORD_BCRYPT),
            'pin'        => $pin,
            'active'     => $esRegistro ? 0 : (isset($data['active']) ? (int) $data['active'] : 1),
            'rol'        => $esRegistro ? 'Estudiante' : ($data['rol'] ?? 'Administrador')
        ];

        $insertId = $this->model->insert($datosInsertar, true);

        if (!$insertId) {
            return $this->failServerError('No fue posible crear el usuario');
        }

        if ($esRegistro) {
            $email = \Config\Services::email();

            $email->setTo($correo);
            $email->setSubject('PIN de verificación de tu cuenta');
            $email->setMessage("
Hola {$username},

Tu cuenta fue creada correctamente.

Tu PIN de verificación es: {$pin}

Ingresa este PIN para activar tu cuenta.

Si no realizaste este registro, puedes ignorar este mensaje.
            ");

            if (!$email->send()) {
                log_message('error', $email->printDebugger(['headers']));
                $this->model->delete($insertId);

                return $this->failServerError(
                    'No se pudo enviar el correo con el PIN. Intenta nuevamente.'
                );
            }

            return $this->respondCreated([
                'message' => 'Usuario registrado correctamente. Revisa tu correo para el PIN de verificación.',
                'data' => [
                    'id_usuario' => $insertId,
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
                'correo' => $correo,
                'username' => $username,
                'rol' => $datosInsertar['rol'],
                'active' => $datosInsertar['active']
            ]
        ]);
    }

    public function verifyPin()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $correo = trim($data['correo'] ?? '');
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
            'pin'    => ''
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

        $correo = trim($data['correo'] ?? '');

        if ($correo === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El correo es obligatorio'
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

        $nuevoPin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->model->update($usuario['id_usuario'], [
            'pin'    => $nuevoPin,
            'active' => 0
        ]);

        $email = \Config\Services::email();

        $email->setTo($correo);
        $email->setSubject('Reenvío de PIN de verificación');
        $email->setMessage("
Hola {$usuario['username']},

Solicitaste un nuevo PIN de verificación.

Tu nuevo PIN es: {$nuevoPin}

Ingresa este PIN para activar tu cuenta.
        ");

        if (!$email->send()) {
            log_message('error', $email->printDebugger(['headers']));

            return $this->failServerError('No se pudo reenviar el PIN. Intenta nuevamente.');
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

        if (array_key_exists('correo', $data)) {
            $correo = trim($data['correo']);

            if ($correo === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El correo no puede estar vacío',
                    'field'   => 'correo'
                ]);
            }

            $correoExistente = $this->model
                ->where('correo', $correo)
                ->where('id_usuario !=', $id)
                ->first();

            if ($correoExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este correo ya está registrado',
                    'field'   => 'correo'
                ]);
            }

            $datosActualizar['correo'] = $correo;
        }

        if (array_key_exists('username', $data)) {
            $username = trim($data['username']);

            if ($username === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El username no puede estar vacío',
                    'field'   => 'username'
                ]);
            }

            $usernameExistente = $this->model
                ->where('username', $username)
                ->where('id_usuario !=', $id)
                ->first();

            if ($usernameExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este nombre de usuario ya está en uso',
                    'field'   => 'username'
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
            'message' => 'Usuario eliminado'
        ]);
    }
}
