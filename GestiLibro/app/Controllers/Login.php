<?php


namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;


class Login extends ResourceController
{
    protected $format = 'json';


    public function auth()
    {
        $data = $this->request->getJSON(true);


        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;


        if (!$username || !$password) {
            return $this->failValidationErrors('Username y password son requeridos');
        }


        $userModel = new UsuarioModel();
        $user = $userModel->where('username', $username)->first();


        if (!$user || !password_verify($password, $user['contrasena'])) {
            return $this->failUnauthorized('Credenciales incorrectas');
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
