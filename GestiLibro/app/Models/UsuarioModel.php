<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends BaseModel
{
    protected $table = 'Usuario';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['nombre', 'apellido', 'correo', 'contrasena', 'id_rol','username'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $observers = [
        \App\Observers\AuditObserver::class,
    ];
    // Obtener el rol del usuario
    public function obtenerRol()
    {
        return $this->join('Rol', 'Rol.id_rol = Usuario.id_rol')
                    ->select('Usuario.*, Rol.nombre as rol')
                    ->orderBy('Usuario.id_usuario', 'DESC')
                    ->findAll();
    }
    public function getUsuarioConRol($idUsuario)
    {
        return $this->db->table('Usuario u')
            ->select('u.id_usuario, u.nombre, u.apellido, u.id_rol, r.nombre AS nombre_rol')
            ->join('Rol r', 'r.id_rol = u.id_rol')
            ->where('u.id_usuario', $idUsuario)
            ->get()
            ->getRowArray();
    }
}
