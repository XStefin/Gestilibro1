<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends BaseModel
{
    protected $table = 'Categoria';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $returnType = 'array';
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
}
