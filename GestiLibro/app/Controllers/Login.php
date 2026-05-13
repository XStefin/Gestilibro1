<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;

class Login extends ResourceController
{
    protected $format = 'json';

    /**
     * Método estándar para leer JSON desde Postman o desde el frontend.
     */
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

    public function auth()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Username y password son requeridos'
            ]);
        }

        $userModel = new UsuarioModel();

        $user = $userModel->where('username', trim($username))->first();

        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Credenciales incorrectas'
            ]);
        }

        if (!password_verify($password, $user['contrasena'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Credenciales incorrectas'
            ]);
        }

        $token = bin2hex(random_bytes(32));

        return $this->respond([
            'status' => 'success',
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id_usuario' => $user['id_usuario'] ?? null,
                'nombre' => $user['nombre'] ?? '',
                'apellido' => $user['apellido'] ?? '',
                'username' => $user['username'] ?? '',
                'rol' => $user['rol'] ?? '',
                'pin' => $user['pin'] ?? '',
                'active' => $user['active'] ?? 0
            ]
        ]);
    }

    public function logout()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'Logout exitoso'
        ]);
    }
}
