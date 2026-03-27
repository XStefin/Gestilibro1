<?php

namespace App\Controllers;

use App\Models\RolModel;

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
        $this->rolModel->insert([
            'nombre' => $this->request->getPost('nombre')
        ]);
        return redirect()->to('/roles');
    }


    public function edit($id)
    {
        $data['rol'] = $this->rolModel->find($id);
        return view('roles/edit', $data);
    }

    public function update($id)
    {
        $this->rolModel->update($id, [
        'nombre' => $this->request->getPost('nombre')
    ]);
        return redirect()->to('/roles');
    }

    public function delete($id)
    {
        $this->rolModel->delete($id);
        return redirect()->to('/roles');
    }
}
