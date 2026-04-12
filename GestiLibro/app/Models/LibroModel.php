<?php

namespace App\Models;

class LibroModel extends BaseModel
{
    protected $table = 'Libro';
    protected $primaryKey = 'id_libro';
    protected $allowedFields = [
        'titulo',
        'autor',
        'editorial',
        'anio',
        'categoria',
        'cantidad',
        'disponibilidad'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    

    public function getLibroConEstado($id)
    {
        $libro = $this->find($id);
        if (!$libro) return null;

        switch ($libro['disponibilidad']) {
            case 'prestado':
                $state = new \App\Models\States\PrestadoState();
                break;
            case 'no_disponible':
                $state = new \App\Models\States\NoDisponibleState();
                break;
            default:
                $state = new \App\Models\States\DisponibleState();
        }

        return new \App\Models\States\LibroContext($this, $libro, $state);
    }

    public function obtenerLibros($disponibilidad = null)
    {
        $query = $this->select('*');

        if (!empty($disponibilidad)) {
            $query->where('disponibilidad', $disponibilidad);
        }

        return $query->findAll();
    }

    public function obtenerCopiasDisponibles(int $idLibro, PrestamoModel $prestamoModel): int
    {
        $libro = $this->find($idLibro);

        if (!$libro) {
            return 0;
        }

        $prestamosActivos = $prestamoModel->contarPrestamosActivosPorLibro($idLibro);

        return max(0, ((int) $libro['cantidad']) - $prestamosActivos);
    }
}