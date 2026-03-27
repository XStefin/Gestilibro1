<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use CodeIgniter\Controller;
use App\Libraries\AuthUser;

class CategoriaController extends Controller
{
    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

     public function index()
    {
        $model = new CategoriaModel();
        $data['categorias'] = $model->findAll();
        $data['user'] = AuthUser::getInstance();
        return view('categorias/index', $data);
    }

    public function create()
    {
        return view('categorias/create');
    }

    public function store()
    {
        $this->categoriaModel->insert([
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
        ]);

        return redirect()->to('/categorias');
    }

    public function edit($id)
    {
        $data['categoria'] = $this->categoriaModel->find($id);
        return view('categorias/edit', $data);
    }

    public function update($id)
    {
        $this->categoriaModel->update($id, [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
        ]);

        return redirect()->to('/categorias');
    }

    public function delete($id)
    {
        $this->categoriaModel->delete($id);
        return redirect()->to('/categorias');
    }
}
