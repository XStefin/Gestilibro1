<?php

namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends BaseModel
{
    protected $table = 'Libro';
    protected $primaryKey = 'id_libro';
    protected $allowedFields = ['titulo', 'autor', 'editorial', 'anio', 'disponibilidad', 'id_categoria'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
    public function getLibroConEstado($id)
    {
        $libro = $this->find($id);
        if (!$libro) return null;
        // Determina el estado actual del libro
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

        // Retorna un objeto que combina el modelo con su estado
        return new \App\Models\States\LibroContext($this, $libro, $state);
    }
    
    // Obtener libros con su categoría
    public function conCategoria($disponibilidad = null)
    {
        $query = $this->select('Libro.*, Categoria.nombre AS nombre_categoria')
                      ->join('Categoria', 'Categoria.id_categoria = Libro.id_categoria', 'left');

        if (!empty($disponibilidad)) {
            $query->where('Libro.disponibilidad', $disponibilidad);
        }

        return $query->findAll();
    }
}
