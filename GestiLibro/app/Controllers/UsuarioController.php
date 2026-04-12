<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data['usuarios'] = $this->usuarioModel->findAll();
        return view('usuarios/index', $data);
    }

    public function create()
    {
        return view('usuarios/create');
    }

    public function store()
    {
        $correo = $this->request->getPost('correo');
        $username = $this->request->getPost('username');
        $pin = $this->request->getPost('pin');

        if (!empty($correo) && $this->usuarioModel->where('correo', $correo)->first()) {
            return redirect()->back()->withInput()->with('error', 'El correo ya está registrado.');
        }

        if (!empty($username) && $this->usuarioModel->where('username', $username)->first()) {
            return redirect()->back()->withInput()->with('error', 'El username ya está registrado.');
        }

        if (!empty($pin) && $this->usuarioModel->where('pin', $pin)->first()) {
            return redirect()->back()->withInput()->with('error', 'El PIN ya está registrado.');
        }

        $this->usuarioModel->insert([
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'username' => $this->request->getPost('username'),
            'contrasena' => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'rol' => $this->request->getPost('rol'),
            'pin' => $pin,
            'active' => $this->request->getPost('active') ? 1 : 0,
        ]);

        return redirect()->to('/usuarios')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit($id)
    {
        $data['usuario'] = $this->usuarioModel->find($id);
        return view('usuarios/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'username' => $this->request->getPost('username'),
            'rol' => $this->request->getPost('rol'),
            'pin' => $this->request->getPost('pin'),
            'active' => $this->request->getPost('active') ? 1 : 0,
        ];

        $contrasena = $this->request->getPost('contrasena');
        if (!empty($contrasena)) {
            $data['contrasena'] = password_hash($contrasena, PASSWORD_DEFAULT);
        }

        $this->usuarioModel->update($id, $data);

        return redirect()->to('/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function delete($id)
    {
        $this->usuarioModel->delete($id);
        return redirect()->to('/usuarios');
    }
}