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

        $data['libros'] = $this->libroModel->findAll();
        $data['usuarioActivo'] = $idUsuario;
        $data['puedeCambiarUsuario'] = ($idRol == 1 || $idRol == 2);

        if ($data['puedeCambiarUsuario']) {
            $data['usuarios'] = $this->usuarioModel->findAll();
        } else {
            $data['usuarios'] = [
                $this->usuarioModel->find($idUsuario)
            ];
        }

        return view('prestamos/create', $data);
    }
    
    public function store()
    {
        $idUsuario = $this->request->getPost('id_usuario');
        $idLibro = $this->request->getPost('id_libro');
        $fechaPrestamo = $this->request->getPost('fecha_prestamo') ?: date('Y-m-d');
        $fechaDevolucion = $this->request->getPost('fecha_devolucion');

        if (empty($idUsuario) || empty($idLibro) || empty($fechaPrestamo)) {
            return redirect()->to(site_url('prestamos/create'))
                ->withInput()
                ->with('warning', 'Todos los campos obligatorios deben estar completos.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $libro = $this->libroModel->getLibroConEstado($idLibro);

            if (!$libro) {
                throw new \Exception('El libro seleccionado no existe.');
            }

            $libro->prestar();

            $this->prestamoModel->insert([
                'id_usuario' => $idUsuario,
                'id_libro' => $idLibro,
                'fecha_prestamo' => $fechaPrestamo,
                'fecha_devolucion' => $fechaDevolucion,
            ]);

            $db->transCommit();

            return redirect()->to(site_url('prestamos'))
                ->with('success', 'Préstamo registrado correctamente.');
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->to(site_url('prestamos/create'))
                ->withInput()
                ->with('warning', $e->getMessage());
        }
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