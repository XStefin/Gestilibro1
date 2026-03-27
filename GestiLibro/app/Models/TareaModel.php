<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends BaseModel
{
    protected $table = 'Tarea';
    protected $primaryKey = 'id_tarea';
    protected $allowedFields = [
        'id_usuario', 'titulo', 'descripcion', 
        'fecha_creacion', 'fecha_vencimiento', 
        'estado', 'prioridad'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
    // 🔹 Obtener tareas con info del usuario
    public function conUsuario()
    {
        return $this->select('Tarea.*, Usuario.nombre as usuario')
                    ->join('Usuario', 'Usuario.id_usuario = Tarea.id_usuario')
                    ->findAll();
    }
}
