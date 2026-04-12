<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Libraries\AuthUser;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function auth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UsuarioModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['contrasena'])) {
            if (!(bool) $user['active']) {
                return redirect()->back()->with('error', 'Usuario inactivo');
            }

            $auth = AuthUser::getInstance();
            $auth->setUser($user);

            session()->set('isLoggedIn', true);

            if (strtolower($user['rol']) === 'administrador') {
                return redirect()->to('/dashboard');
            }

            return redirect()->to('/libros');
        }

        return redirect()->back()->with('error', 'Credenciales incorrectas');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}