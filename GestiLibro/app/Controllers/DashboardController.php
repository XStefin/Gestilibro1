<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LibroModel;
use App\Models\PrestamoModel;
use App\Models\UsuarioModel;

class DashboardController extends ResourceController
{
    protected $format = 'json';

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
        $data = [
            "libros"     => $this->libroModel->countAllResults(),
            "prestamos"  => $this->prestamoModel->countAllResults(),
            "usuarios"   => $this->usuarioModel->countAllResults()
        ];

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }
}