<?php

namespace App\Controllers;

use App\Models\PrestamoModel;
use App\Models\UsuarioModel;
use App\Models\LibroModel;
use App\Libraries\AuthUser;

class PrestamoController extends BaseController
{
    protected $prestamoModel;
    protected $usuarioModel;
    protected $libroModel;

    public function __construct()
    {
        $this->prestamoModel = new PrestamoModel();
        $this->usuarioModel = new UsuarioModel();
        $this->libroModel = new LibroModel();
    }

    public function index()
    {
        $data['prestamos'] = $this->prestamoModel->conDetalles();
        $data['librosDisponibles'] = $this->libroModel
            ->where('disponibilidad', 'disponible')
            ->findAll();
        $data['user'] = AuthUser::getInstance();
        return view('prestamos/index', $data);
    }

    public function create()
    {
        $data['usuarios'] = $this->usuarioModel->findAll();
        $data['libros'] = $this->libroModel->findAll();
        return view('prestamos/create', $data);
    }

    public function store()
    {
        $this->prestamoModel->insert([
            'id_usuario' => $this->request->getPost('id_usuario'),
            'id_libro' => $this->request->getPost('id_libro'),
            'fecha_prestamo' => $this->request->getPost('fecha_prestamo') ?? date('Y-m-d'),
            'fecha_devolucion' => $this->request->getPost('fecha_devolucion'),
        ]);
        $libro = $this->libroModel->getLibroConEstado($this->request->getPost('id_libro'));
        $libro->prestar();

        return redirect()->to(site_url('prestamos'));
    }

    public function edit($id)
    {
        $data['prestamo'] = $this->prestamoModel->find($id);
        $data['usuarios'] = $this->usuarioModel->findAll();
        $data['libros'] = $this->libroModel->findAll();

        return view('prestamos/edit', $data);
    }

    public function update($id)
    {
        $this->prestamoModel->update($id, [
            'id_usuario' => $this->request->getPost('id_usuario'),
            'id_libro' => $this->request->getPost('id_libro'),
            'fecha_prestamo' => $this->request->getPost('fecha_prestamo'),
            'fecha_devolucion' => $this->request->getPost('fecha_devolucion'),
            'estado' => $this->request->getPost('estado')
        ]);

        return redirect()->to(site_url('prestamos'));
    }

    public function delete($id)
    {
        $this->prestamoModel->delete($id);
        return redirect()->to(site_url('prestamos'));
    }
}