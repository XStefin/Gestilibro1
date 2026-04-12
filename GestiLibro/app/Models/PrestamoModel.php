<?php

namespace App\Models;

class PrestamoModel extends BaseModel
{
    protected $table = 'Prestamo';
    protected $primaryKey = 'id_prestamo';
    protected $allowedFields = ['id_usuario', 'id_libro', 'fecha_prestamo', 'fecha_devolucion', 'estado'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

   

    public function obtenerTodosConUsuarioYLibro()
    {
        return $this->select('Prestamo.*, Usuario.nombre AS nombre_usuario, Usuario.apellido, Libro.titulo AS titulo_libro')
                    ->join('Usuario', 'Usuario.id_usuario = Prestamo.id_usuario')
                    ->join('Libro', 'Libro.id_libro = Prestamo.id_libro')
                    ->orderBy('Prestamo.id_prestamo', 'DESC')
                    ->findAll();
    }

    public function obtenerPrestamosPorUsuario($idUsuario)
    {
        return $this->select('Prestamo.*, Usuario.nombre AS nombre_usuario, Usuario.apellido, Libro.titulo AS titulo_libro')
                    ->join('Usuario', 'Usuario.id_usuario = Prestamo.id_usuario')
                    ->join('Libro', 'Libro.id_libro = Prestamo.id_libro')
                    ->where('Prestamo.id_usuario', $idUsuario)
                    ->orderBy('Prestamo.id_prestamo', 'DESC')
                    ->findAll();
    }

    public function contarPrestamosActivosPorLibro(int $idLibro): int
    {
        return $this->where('id_libro', $idLibro)
                    ->whereIn('estado', ['prestado', 'atrasado'])
                    ->countAllResults();
    }

    public function obtenerPrestamosActivosPorLibro(int $idLibro): array
    {
        return $this->where('id_libro', $idLibro)
                    ->whereIn('estado', ['prestado', 'atrasado'])
                    ->findAll();
    }
}