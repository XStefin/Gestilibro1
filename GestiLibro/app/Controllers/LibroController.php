<?php

namespace App\Controllers;

use App\Repositories\LibroRepository;
use App\Models\CategoriaModel;
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
        $data['libros'] = $this->libros->obtenerTodosConCategoria($disponibilidad);
        $data['categoryExist'] = $this->categorias->findAll();
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
        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'autor' => $this->request->getPost('autor'),
            'editorial' => $this->request->getPost('editorial'),
            'anio' => $this->request->getPost('anio'),
            'disponibilidad' => $this->request->getPost('disponibilidad') ?? 'disponible',
            'id_categoria' => $this->request->getPost('id_categoria'),
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
        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'autor' => $this->request->getPost('autor'),
            'editorial' => $this->request->getPost('editorial'),
            'anio' => $this->request->getPost('anio'),
            'disponibilidad' => $this->request->getPost('disponibilidad'),
            'id_categoria' => $this->request->getPost('id_categoria'),
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
