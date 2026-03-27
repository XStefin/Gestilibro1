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
            // Singleton opcional para la petición actual
            $auth = AuthUser::getInstance();
            $auth->setUser($user);

            // Auditoría
            $observer = new \App\Observers\AuditObserver();
            $observer->onUserLoggedIn($user);

            // Sesión persistente entre páginas
            session()->set([
                'isLoggedIn' => true,
                'authUser' => [
                    'id_usuario' => $user['id_usuario'] ?? null,
                    'nombreCompleto' => $user['nombreCompleto'] ?? '',
                    'username' => $user['username'] ?? '',
                    'id_rol' => $user['id_rol'] ?? null
                ]
            ]);

            switch ($user['id_rol']) {
                case 1: // Admin
                    return redirect()->to('/dashboard');

                case 2:
                    return redirect()->to('/libros');

                default:
                    return redirect()->to('/libros');
            }
        } else {
            return redirect()->back()->with('error', 'Credenciales incorrectas');
        }
    }

    public function logout()
    {
        $user = session()->get('authUser');

        if ($user) {
            $observer = new \App\Observers\AuditObserver();
            $observer->onUserLoggedIn($user, true);
        }

        session()->destroy();

        return redirect()->to('/login');
    }
}