<?php


namespace App\Models;


use CodeIgniter\Model;


class UsuarioModel extends BaseModel
{
    protected $table = 'Usuario';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'username',
        'rol',
        'pin',
        'active'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
}
