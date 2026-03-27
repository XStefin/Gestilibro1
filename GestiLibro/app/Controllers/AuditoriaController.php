<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuditoriaController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $usuario = $this->request->getGet('usuario');
        $accion  = $this->request->getGet('accion');
        $fecha   = $this->request->getGet('fecha');

        $builder = $db->table('auditoria a');
        $builder->select('a.*, u.username, u.nombre, u.apellido');
        $builder->join('usuario u', 'u.id_usuario = a.user_id', 'left');

        if (!empty($usuario)) {
            $builder->where('LOWER(u.username)', strtolower($usuario));
        }

        if (!empty($accion)) {
            $builder->where('a.accion', $accion);
        }

        if (!empty($fecha)) {
            $builder->where('DATE(a.fecha)', $fecha);
        }

        $builder->orderBy('a.fecha', 'DESC');

        $data['auditorias'] = $builder->get()->getResult();
        $data['usuario'] = $usuario;
        $data['accion']  = $accion;
        $data['fecha']   = $fecha;

        return view('auditoria/index', $data);
    }
}
