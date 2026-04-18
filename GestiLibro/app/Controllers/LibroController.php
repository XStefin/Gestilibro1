<?php

namespace App\Controllers;

use App\Repositories\LibroRepository;
use App\Models\CategoriaModel;
use App\Models\PrestamoModel;
use App\Libraries\AuthUser;

class LibroController extends BaseController
{
    protected $libros;
    protected $categorias;

    public function __construct()
    {
        $this->libros = new LibroRepository();
        $this->categorias = new CategoriaModel();
    }

    public function index()
    {
        $disponibilidad = $this->request->getGet('disponibilidad');
        $libros = $this->libros->obtenerTodos($disponibilidad);

        $prestamoModel = new PrestamoModel();

        foreach ($libros as &$libro) {
            $prestamosActivos = $prestamoModel->contarPrestamosActivosPorLibro((int) $libro['id_libro']);
            $libro['copias_disponibles'] = max(0, (int) $libro['cantidad'] - $prestamosActivos);
        }

        $data['libros'] = $libros;
        $data['user'] = AuthUser::getInstance();

        return view('libros/index', $data);
    }

    public function create()
    {
        $data['categorias'] = $this->categorias->findAll();
        return view('libros/create', $data);
    }

    public function store()
    {
        $anio = $this->request->getPost('anio');
        $anioActual = (int) date('Y');
        $anioMinimo = 1900;

        if (!preg_match('/^\d{4}$/', $anio)) {
            return redirect()->back()->withInput()->with('error', 'El año debe contener exactamente 4 dígitos.');
        }

        $anio = (int) $anio;

        if ($anio > $anioActual) {
            return redirect()->back()->withInput()->with('error', 'El año no puede ser mayor al año actual.');
        }

        if ($anio < $anioMinimo) {
            return redirect()->back()->withInput()->with('error', 'El año no puede ser menor a ' . $anioMinimo . '.');
        }

        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'autor' => $this->request->getPost('autor'),
            'editorial' => $this->request->getPost('editorial'),
            'anio' => $anio,
            'id_categoria' => $this->request->getPost('id_categoria'),
            'cantidad' => $this->request->getPost('cantidad') ?: 1,
            'disponibilidad' => $this->request->getPost('disponibilidad') ?? 'disponible',
        ];

        $this->libros->crear($data);

        return redirect()->to('/libros')->with('success', 'Libro agregado correctamente.');
    }

    public function edit($id)
    {
        $data['libro'] = $this->libros->obtenerPorId($id);
        $data['categorias'] = $this->categorias->findAll();
        return view('libros/edit', $data);
    }

    public function update($id)
    {
        $anio = $this->request->getPost('anio');
        $anioActual = (int) date('Y');
        $anioMinimo = 1900;

        if (!preg_match('/^\d{4}$/', $anio)) {
            return redirect()->back()->withInput()->with('error', 'El año debe contener exactamente 4 dígitos.');
        }

        $anio = (int) $anio;

        if ($anio > $anioActual) {
            return redirect()->back()->withInput()->with('error', 'El año no puede ser mayor al año actual.');
        }

        if ($anio < $anioMinimo) {
            return redirect()->back()->withInput()->with('error', 'El año no puede ser menor a ' . $anioMinimo . '.');
        }

        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'autor' => $this->request->getPost('autor'),
            'editorial' => $this->request->getPost('editorial'),
            'anio' => $anio,
            'id_categoria' => $this->request->getPost('id_categoria'),
            'cantidad' => $this->request->getPost('cantidad'),
            'disponibilidad' => $this->request->getPost('disponibilidad'),
        ];

        $this->libros->actualizar($id, $data);

        return redirect()->to('/libros')->with('success', 'Libro actualizado correctamente.');
    }

    public function delete($id)
    {
        if ($this->libros->eliminarLogico($id)) {
            return redirect()->to('/libros')->with('success', 'Libro marcado como no disponible.');
        }

        return redirect()->to('/libros')->with('error', 'No se pudo eliminar el libro.');
    }
}