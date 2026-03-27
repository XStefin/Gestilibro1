<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends BaseModel
{
    protected $table = 'Rol';
    protected $primaryKey = 'id_rol';
    protected $allowedFields = ['nombre'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
}
