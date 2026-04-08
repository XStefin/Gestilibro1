<?php

namespace App\Controllers;

use App\Models\RolModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class RolController extends BaseController
{
    protected $rolModel;

    public function __construct()
    {
        $this->rolModel = new RolModel();
    }

    public function index()
    {
        $data['roles'] = $this->rolModel->findAll();
        return view('roles/index', $data);
    }

    public function create()
    {
        return view('roles/create');
    }

    public function store()
    {
        $nombre = trim($this->request->getPost('nombre'));

        $rolExistente = $this->rolModel
            ->where('nombre', $nombre)
            ->first();

        if ($rolExistente) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe un rol con ese nombre.');
        }

        try {
            $this->rolModel->insert([
                'nombre' => $nombre
            ]);

            return redirect()->to('/roles')
                ->with('success', 'Rol creado correctamente.');
        } catch (DatabaseException $e) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe un rol con ese nombre.');
        }
    }

    public function edit($id)
    {
        $data['rol'] = $this->rolModel->find($id);
        return view('roles/edit', $data);
    }

    public function update($id)
    {
        $nombre = trim($this->request->getPost('nombre'));

        $rolExistente = $this->rolModel
            ->where('nombre', $nombre)
            ->where('id !=', $id)
            ->first();

        if ($rolExistente) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe otro rol con ese nombre.');
        }

        try {
            $this->rolModel->update($id, [
                'nombre' => $nombre
            ]);

            return redirect()->to('/roles')
                ->with('success', 'Rol actualizado correctamente.');
        } catch (DatabaseException $e) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Ya existe otro rol con ese nombre.');
        }
    }

    public function delete($id)
    {
        $this->rolModel->delete($id);

        return redirect()->to('/roles')
            ->with('success', 'Rol eliminado correctamente.');
    }
}