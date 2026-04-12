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
        $authUser = AuthUser::getInstance()->getUser();
        $rol = strtolower($authUser['rol'] ?? '');

        if (in_array($rol, ['administrador', 'bibliotecario'])) {
            $data['prestamos'] = $this->prestamoModel->obtenerTodosConUsuarioYLibro();
        } else {
            $data['prestamos'] = $this->prestamoModel->obtenerPrestamosPorUsuario($authUser['id_usuario']);
        }

        $data['librosDisponibles'] = $this->libroModel
            ->where('disponibilidad', 'disponible')
            ->findAll();

        $data['user'] = AuthUser::getInstance();

        return view('prestamos/index', $data);
    }

    public function create()
    {
        $authUser = AuthUser::getInstance()->getUser();
        $rol = strtolower($authUser['rol'] ?? '');

        $puedeCambiarUsuario = in_array($rol, ['administrador', 'bibliotecario']);

        if ($puedeCambiarUsuario) {
            $data['usuarios'] = $this->usuarioModel->where('active', 1)->findAll();
            $data['usuarioActivo'] = old('id_usuario');
        } else {
            $usuarioBD = $this->usuarioModel->find($authUser['id_usuario']);
            $data['usuarios'] = [$usuarioBD];
            $data['usuarioActivo'] = $authUser['id_usuario'];
        }

        $data['puedeCambiarUsuario'] = $puedeCambiarUsuario;

        $todosLosLibros = $this->libroModel->findAll();
        $librosDisponibles = [];

        foreach ($todosLosLibros as $libro) {
            $prestamosActivos = $this->prestamoModel->contarPrestamosActivosPorLibro((int) $libro['id_libro']);
            $copiasDisponibles = (int) $libro['cantidad'] - $prestamosActivos;

            if ($copiasDisponibles > 0) {
                $libro['copias_disponibles'] = $copiasDisponibles;
                $librosDisponibles[] = $libro;
            }
        }

        $data['libros'] = $librosDisponibles;

        return view('prestamos/create', $data);
    }
    
    public function store()
    {
        $authUser = AuthUser::getInstance()->getUser();
        $rol = strtolower($authUser['rol'] ?? '');

        $idUsuario = in_array($rol, ['administrador', 'bibliotecario'])
            ? $this->request->getPost('id_usuario')
            : $authUser['id_usuario'];

        $idLibro = (int) $this->request->getPost('id_libro');

        $libro = $this->libroModel->find($idLibro);

        if (!$libro) {
            return redirect()->back()->withInput()->with('error', 'El libro seleccionado no existe.');
        }

        $prestamosActivos = $this->prestamoModel->contarPrestamosActivosPorLibro($idLibro);
        $copiasDisponibles = (int) $libro['cantidad'] - $prestamosActivos;

        if ($copiasDisponibles <= 0) {
            return redirect()->back()->withInput()->with('error', 'No hay copias disponibles para préstamo.');
        }

        $this->prestamoModel->insert([
            'id_usuario' => $idUsuario,
            'id_libro' => $idLibro,
            'fecha_prestamo' => $this->request->getPost('fecha_prestamo') ?? date('Y-m-d'),
            'fecha_devolucion' => $this->request->getPost('fecha_devolucion'),
            'estado' => 'prestado'
        ]);

        $prestamosActivosActualizados = $this->prestamoModel->contarPrestamosActivosPorLibro($idLibro);
        $copiasDisponiblesActualizadas = (int) $libro['cantidad'] - $prestamosActivosActualizados;

        $this->libroModel->update($idLibro, [
            'disponibilidad' => $copiasDisponiblesActualizadas > 0 ? 'disponible' : 'no_disponible'
        ]);

        return redirect()->to(site_url('prestamos'))->with('success', 'Préstamo registrado correctamente.');
    }

    public function edit($id)
    {
        $authUser = AuthUser::getInstance()->getUser();
        $rol = strtolower($authUser['rol'] ?? '');

        $data['prestamo'] = $this->prestamoModel->find($id);

        if (in_array($rol, ['administrador', 'bibliotecario'])) {
            $data['usuarios'] = $this->usuarioModel->where('active', 1)->findAll();
        } else {
            $data['usuarios'] = [$authUser];
        }

        $data['libros'] = $this->libroModel->findAll();

        return view('prestamos/edit', $data);
    }

    public function update($id)
    {
        $authUser = AuthUser::getInstance()->getUser();
        $rol = strtolower($authUser['rol'] ?? '');

        $idUsuario = in_array($rol, ['administrador', 'bibliotecario'])
            ? $this->request->getPost('id_usuario')
            : $authUser['id_usuario'];

        $idLibro = (int) $this->request->getPost('id_libro');
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

        $libro = $this->libroModel->find($idLibro);

        if ($libro) {
            $prestamosActivos = $this->prestamoModel->contarPrestamosActivosPorLibro($idLibro);
            $copiasDisponibles = (int) $libro['cantidad'] - $prestamosActivos;

            $this->libroModel->update($idLibro, [
                'disponibilidad' => $copiasDisponibles > 0 ? 'disponible' : 'no_disponible'
            ]);
        }

        return redirect()->to(site_url('prestamos'))->with('success', 'Préstamo actualizado correctamente.');
    }

    public function delete($id)
    {
        $this->prestamoModel->delete($id);
        return redirect()->to(site_url('prestamos'));
    }
}