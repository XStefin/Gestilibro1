<?php

namespace App\Models;

use CodeIgniter\Model;

class PrestamoModel extends BaseModel
{
    protected $table = 'Prestamo';
    protected $primaryKey = 'id_prestamo';
    protected $allowedFields = ['id_usuario', 'id_libro', 'fecha_prestamo', 'fecha_devolucion', 'estado'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];

    public function conDetalles()
    {
        return $this->select('Prestamo.*, Usuario.nombre AS nombre_usuario, Libro.titulo AS titulo_libro')
                    ->join('Usuario', 'Usuario.id_usuario = Prestamo.id_usuario')
                    ->join('Libro', 'Libro.id_libro = Prestamo.id_libro')
                    ->orderBy('Prestamo.id_prestamo', 'DESC')
                    ->findAll();
    }

    public function obtenerTodosConUsuarioYLibro()
    {
        return $this->select('Prestamo.*, Usuario.nombre as nombre_usuario, Usuario.apellido, Libro.titulo as titulo_libro')
                    ->join('Usuario', 'Usuario.id_usuario = Prestamo.id_usuario')
                    ->join('Libro', 'Libro.id_libro = Prestamo.id_libro')
                    ->findAll();
    }

    public function obtenerPrestamosPorUsuario($idUsuario)
    {
        return $this->select('Prestamo.*, Usuario.nombre as nombre_usuario, Usuario.apellido, Libro.titulo as titulo_libro')
                    ->join('Usuario', 'Usuario.id_usuario = Prestamo.id_usuario')
                    ->join('Libro', 'Libro.id_libro = Prestamo.id_libro')
                    ->where('Prestamo.id_usuario', $idUsuario)
                    ->findAll();
    }
}


