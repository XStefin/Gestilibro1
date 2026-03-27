<?php
namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'entidad', 
        'entidad_id', 
        'accion', 
        'detalle', 
        'user_id', 
        'fecha'
    ];
}
