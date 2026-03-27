<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;

class TareaController extends BaseController
{
    protected $tareaModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->tareaModel = new TareaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data['tareas'] = $this->tareaModel->conUsuario();
        return view('tareas/index', $data);
    }

    public function create()
    {
        $data['usuarios'] = $this->usuarioModel->findAll();
        return view('tareas/create', $data);
    }

    public function store()
    {
        $this->tareaModel->insert([
            'id_usuario' => $this->request->getPost('id_usuario'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_creacion' => date('Y-m-d'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'estado' => 'pendiente',
            'prioridad' => $this->request->getPost('prioridad'),
        ]);
        return redirect()->to('/tareas');
    }


    public function edit($id)
    {
        $data['tarea'] = $this->tareaModel->find($id);
        $data['usuarios'] = $this->usuarioModel->findAll();

        if (!$data['tarea']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tarea no encontrada");
        }

        return view('tareas/edit', $data);
    }

 
    public function update($id)
    {
        $this->tareaModel->update($id, [
            'id_usuario' => $this->request->getPost('id_usuario'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'estado' => $this->request->getPost('estado'),
            'prioridad' => $this->request->getPost('prioridad'),
        ]);

        return redirect()->to('/tareas');
    }

    public function delete($id)
    {
        $this->tareaModel->delete($id);
        return redirect()->to('/tareas');
    }
}
