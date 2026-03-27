<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolModel;

class UsuarioController extends BaseController
{
    protected $usuarioModel;
    protected $rolModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->rolModel = new RolModel();
    }

    public function index()
    {
        $data['usuarios'] = $this->usuarioModel->obtenerRol();
        return view('usuarios/index', $data);
    }

    public function create()
    {
        $data['roles'] = $this->rolModel->findAll();
        return view('usuarios/create', $data);
    }

    public function store()
    {
        $this->usuarioModel->insert([
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'contrasena' => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'id_rol' => $this->request->getPost('id_rol'),
            'username'=> $this->request->getPost('username'),
        ]);
        return redirect()->to('/usuarios')->with('success', 'Usuario registrado correctamente.');
    }

     public function edit($id)
    {
        $data['usuario'] = $this->usuarioModel->find($id);
        $data['roles'] = $this->rolModel->findAll();
        return view('usuarios/edit', $data);
    }

    public function update($id)
{
    $data = [
        'nombre' => $this->request->getPost('nombre'),
        'apellido' => $this->request->getPost('apellido'),
        'correo' => $this->request->getPost('correo'),
        'id_rol' => $this->request->getPost('id_rol'),
    ];

    // Solo actualizar la contraseña si se envió
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
