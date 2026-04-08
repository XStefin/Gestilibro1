<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException;
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
        $nombre = trim($this->request->getPost('nombre'));
        $descripcion = trim($this->request->getPost('descripcion'));

        if (empty($nombre)) {
            return redirect()->to(site_url('categorias/create'))
                ->withInput()
                ->with('warning', 'El nombre de la categoría es obligatorio.');
        }

        $categoriaExistente = $this->categoriaModel
            ->where('nombre', $nombre)
            ->first();

        if ($categoriaExistente) {
            return redirect()->to(site_url('categorias/create'))
                ->withInput()
                ->with('warning', 'Ya existe una categoría con ese nombre.');
        }

        try {
            $this->categoriaModel->insert([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
            ]);

            return redirect()->to('/categorias')
                ->with('success', 'Categoría creada correctamente.');
        } catch (DatabaseException $e) {
            return redirect()->to(site_url('categorias/create'))
                ->withInput()
                ->with('warning', 'Ya existe una categoría con ese nombre.');
        }
    }

    public function edit($id)
    {
        $data['categoria'] = $this->categoriaModel->find($id);
        return view('categorias/edit', $data);
    }

    public function update($id)
    {
        $nombre = trim($this->request->getPost('nombre'));
        $descripcion = trim($this->request->getPost('descripcion'));

        if (empty($nombre)) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'El nombre de la categoría es obligatorio.');
        }

        $categoriaExistente = $this->categoriaModel
            ->where('nombre', $nombre)
            ->where('id_categoria !=', $id)
            ->first();

        if ($categoriaExistente) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe otra categoría con ese nombre.');
        }

        try {
            $this->categoriaModel->update($id, [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
            ]);

            return redirect()->to('/categorias')
                ->with('success', 'Categoría actualizada correctamente.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe otra categoría con ese nombre.');
        }
    }

    public function delete($id)
    {
        $this->categoriaModel->delete($id);

        return redirect()->to('/categorias')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}