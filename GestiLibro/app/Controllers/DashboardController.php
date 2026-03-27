<?php

namespace App\Controllers;
use App\Models\LibroModel;
use App\Models\PrestamoModel;
use App\Models\UsuarioModel;

class DashboardController extends BaseController
{
    protected $libroModel;
    protected $prestamoModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->libroModel = new LibroModel();
        $this->prestamoModel = new PrestamoModel();
        $this->usuarioModel = new UsuarioModel();
    }
    public function index()
    {
        $libros = $this->libroModel->findAll();
        $prestamos = $this->prestamoModel->findAll();
        $usuarios = $this->usuarioModel->findAll();

        $data = [
            "libros" => count($libros),
            "prestamos" => count($prestamos),
            "usuarios"=> count($usuarios)
        ];
        // Aquí podrías pasar datos del modelo, por ahora solo la vista
        return view('dashboard',$data);
    }
}
