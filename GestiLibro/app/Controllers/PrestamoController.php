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
        $authUser = session('auth_user');

        $idUsuario = $authUser['id_usuario'] ?? null;
        $idRol = $authUser['id_rol'] ?? null;

        if ($idRol == 1 || $idRol == 2) {
            $data['prestamos'] = $this->prestamoModel->obtenerTodosConUsuarioYLibro();
        } else {
            $data['prestamos'] = $this->prestamoModel->obtenerPrestamosPorUsuario($idUsuario);
        }

        return view('prestamos/index', $data);
    }

    public function create()
    {
        $authUser = session('auth_user');

        $idUsuario = $authUser['id_usuario'] ?? null;
        $idRol = $authUser['id_rol'] ?? null;

        // Libros (esto lo dejas igual)
        $data['libros'] = $this->libroModel->findAll();

        // 🔥 CONTROL DE USUARIOS
        if ($idRol == 1 || $idRol == 2) {
            // Admin o Bibliotecario → todos los usuarios
            $data['usuarios'] = $this->usuarioModel->findAll();
        } else {
            // Estudiante → solo él mismo
            $data['usuarios'] = [
                $this->usuarioModel->find($idUsuario)
            ];
        }

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
        $idUsuario = $this->request->getPost('id_usuario');
        $idLibro = $this->request->getPost('id_libro');
        $fechaPrestamo = $this->request->getPost('fecha_prestamo');
        $fechaDevolucion = $this->request->getPost('fecha_devolucion');
        $estado = $this->request->getPost('estado');

        $this->prestamoModel->update($id, [
            'id_usuario' => $idUsuario,
            'id_libro' => $idLibro,
            'fecha_prestamo' => $fechaPrestamo,
            'fecha_devolucion' => $fechaDevolucion,
            'estado' => $estado
        ]);

        if ($estado === 'devuelto') {
            $this->libroModel->update($idLibro, [
                'disponibilidad' => 'disponible'
            ]);
        } else {
            $this->libroModel->update($idLibro, [
                'disponibilidad' => 'prestado'
            ]);
        }

        return redirect()->to(site_url('prestamos'));
    }

    public function delete($id)
    {
        $this->prestamoModel->delete($id);
        return redirect()->to(site_url('prestamos'));
    }
}