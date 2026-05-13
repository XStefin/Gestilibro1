<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format = 'json';

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
        $rawBody = file_get_contents('php://input');
    
        $data = json_decode($rawBody, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'JSON inválido',
                'detalle' => json_last_error_msg(),
                'body_recibido' => $rawBody,
                'content_type' => $this->request->getHeaderLine('Content-Type')
            ]);
        }
    
        if (!$data || !is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'No se recibieron datos válidos'
            ]);
        }
    
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
    
        $usuarioData = [
            'correo'     => $correo,
            'username'   => $username,
            'contrasena' => password_hash($contrasena, PASSWORD_BCRYPT),
            'pin'        => $pin,
            'active'     => $esRegistro ? 0 : (isset($data['active']) ? (int) $data['active'] : 1),
            'rol'        => $esRegistro ? 'Estudiante' : ($data['rol'] ?? 'Administrador')
        ];
    
        $insertId = $this->model->insert($usuarioData, true);
    
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
                'id_usuario' => $insertId
            ]);
        }
    
        return $this->respondCreated([
            'message' => 'Usuario creado correctamente',
            'id_usuario' => $insertId
        ]);
    }

    public function verifyPin()
    {
        $data = $this->request->getJSON(true);

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
        $data = $this->request->getJSON(true);

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
        if ($id === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El id del usuario es obligatorio'
            ]);
        }
    
        $rawBody = file_get_contents('php://input');
    
        $data = json_decode($rawBody, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'JSON inválido',
                'detalle' => json_last_error_msg(),
                'body_recibido' => $rawBody,
                'content_type' => $this->request->getHeaderLine('Content-Type')
            ]);
        }
    
        if (!$data || !is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'No se recibieron datos válidos'
            ]);
        }
    
        $usuario = $this->model->find($id);
    
        if (!$usuario) {
            return $this->failNotFound('Usuario no encontrado');
        }
    
        if (isset($data['correo'])) {
            $data['correo'] = trim($data['correo']);
    
            if ($data['correo'] === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El correo no puede estar vacío',
                    'field'   => 'correo'
                ]);
            }
    
            $correoExistente = $this->model
                ->where('correo', $data['correo'])
                ->where('id_usuario !=', $id)
                ->first();
    
            if ($correoExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este correo ya está registrado',
                    'field'   => 'correo'
                ]);
            }
        }
    
        if (isset($data['username'])) {
            $data['username'] = trim($data['username']);
    
            if ($data['username'] === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'El username no puede estar vacío',
                    'field'   => 'username'
                ]);
            }
    
            $usernameExistente = $this->model
                ->where('username', $data['username'])
                ->where('id_usuario !=', $id)
                ->first();
    
            if ($usernameExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'message' => 'Este nombre de usuario ya está en uso',
                    'field'   => 'username'
                ]);
            }
        }
    
        if (isset($data['contrasena'])) {
            if (trim($data['contrasena']) === '') {
                unset($data['contrasena']);
            } else {
                $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT);
            }
        }
    
        if (isset($data['active'])) {
            $data['active'] = (int) $data['active'];
        }
    
        unset($data['id_usuario']);
        unset($data['esRegistro']);
    
        $this->model->update($id, $data);
    
        return $this->respond([
            'message' => 'Usuario actualizado correctamente'
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

