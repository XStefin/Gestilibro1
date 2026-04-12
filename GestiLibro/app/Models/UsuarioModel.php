<?php

namespace App\Models;

class UsuarioModel extends BaseModel
{
    protected $table = 'Usuario';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['nombre', 'apellido', 'correo', 'username', 'contrasena', 'rol', 'pin', 'active'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

  
}