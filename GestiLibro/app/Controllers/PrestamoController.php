<?php


namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use App\Models\PrestamoModel;
use App\Models\LibroModel;


class PrestamoController extends ResourceController
{
    protected $modelName = 'App\Models\PrestamoModel';
    protected $format = 'json';


    private function estadoOcupaStock(string $estado): bool
    {
        $estado = strtolower(trim($estado));


        return in_array($estado, ['prestado', 'atrasado'], true);
    }


    private function actualizarEstadoLibro(int $idLibro): void
    {
        $libroModel = new LibroModel();
        $libro = $libroModel->find($idLibro);


        if (!$libro) {
            return;
        }


        $nuevaDisponibilidad = ((int) $libro['cantidad'] <= 0)
            ? 'no_disponible'
            : 'disponible';


        $libroModel->update($idLibro, [
            'disponibilidad' => $nuevaDisponibilidad
        ]);
    }


    public function index()
    {
        $idUsuario = $this->request->getGet('usuario');


        $db = \Config\Database::connect();


        $builder = $db->table('Prestamo p');
        $builder->select('
            p.id_prestamo,
            p.id_usuario,
            p.id_libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.estado,
            u.nombre as nombre_usuario,
            u.apellido,
            l.titulo as titulo_libro
        ');
        $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');
        $builder->join('Libro l', 'l.id_libro = p.id_libro');


        if (!empty($idUsuario)) {
            $builder->where('p.id_usuario', $idUsuario);
        }


        return $this->respond($builder->get()->getResultArray());
    }


    public function show($id = null)
    {
        $db = \Config\Database::connect();


        $builder = $db->table('Prestamo p');
        $builder->select('
            p.id_prestamo,
            p.id_usuario,
            p.id_libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.estado,
            u.nombre as nombre_usuario,
            u.apellido,
            l.titulo as titulo_libro
        ');
        $builder->join('Usuario u', 'u.id_usuario = p.id_usuario');
        $builder->join('Libro l', 'l.id_libro = p.id_libro');
        $builder->where('p.id_prestamo', $id);


        $prestamo = $builder->get()->getRowArray();


        if (!$prestamo) {
            return $this->failNotFound('Préstamo no encontrado');
        }


        return $this->respond($prestamo);
    }


    public function create()
    {
        $data = $this->request->getJSON(true);


        $libroModel = new LibroModel();


        $estadoNuevo = strtolower(trim($data['estado'] ?? 'prestado'));


        $libroPrestado = $this->model
            ->where('id_usuario', $data['id_usuario'])
            ->where('id_libro', $data['id_libro'])
            ->whereIn('estado', ['prestado', 'atrasado'])
            ->first();


        $libro = $libroModel->find($data['id_libro']);


        if ($libroPrestado) {
            return $this->fail('El usuario ya tiene este libro prestado o atrasado');
        }


        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }


        if ($this->estadoOcupaStock($estadoNuevo) && (int) $libro['cantidad'] <= 0) {
            return $this->fail('No hay stock disponible');
        }


        if ($this->estadoOcupaStock($estadoNuevo)) {
            $libroModel->set('cantidad', 'cantidad - 1', false)
                ->where('id_libro', $data['id_libro'])
                ->update();


            $this->actualizarEstadoLibro((int) $data['id_libro']);
        }


        $this->model->insert($data);


        return $this->respondCreated([
            'message' => 'Préstamo creado correctamente'
        ]);
    }


    public function update($id = null)
    {
        $prestamoActual = $this->model->find($id);


        if (!$prestamoActual) {
            return $this->failNotFound('Préstamo no encontrado');
        }


        $data = $this->request->getJSON(true);


        $libroModel = new LibroModel();
        $libro = $libroModel->find($prestamoActual['id_libro']);


        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }


        $estadoActual = strtolower(trim($prestamoActual['estado'] ?? ''));
        $nuevoEstado = strtolower(trim($data['estado'] ?? $estadoActual));


        $estadoActualOcupaStock = $this->estadoOcupaStock($estadoActual);
        $nuevoEstadoOcupaStock = $this->estadoOcupaStock($nuevoEstado);


        if ($estadoActualOcupaStock && !$nuevoEstadoOcupaStock) {
            $libroModel->set('cantidad', 'cantidad + 1', false)
                ->where('id_libro', $prestamoActual['id_libro'])
                ->update();
        }


        if (!$estadoActualOcupaStock && $nuevoEstadoOcupaStock) {
            if ((int) $libro['cantidad'] <= 0) {
                return $this->fail('No hay stock disponible para volver a prestar este libro');
            }


            $libroModel->set('cantidad', 'cantidad - 1', false)
                ->where('id_libro', $prestamoActual['id_libro'])
                ->update();
        }


        $this->model->update($id, $data);


        $this->actualizarEstadoLibro((int) $prestamoActual['id_libro']);


        return $this->respond([
            'message' => 'Préstamo actualizado'
        ]);
    }


    public function delete($id = null)
    {
        $prestamo = $this->model->find($id);


        if (!$prestamo) {
            return $this->failNotFound('Préstamo no encontrado');
        }


        if ($this->estadoOcupaStock($prestamo['estado'])) {
            return $this->fail([
                'message' => 'No se puede eliminar un préstamo en estado prestado o atrasado'
            ]);
        }


        $idLibro = (int) $prestamo['id_libro'];


        $this->model->delete($id);


        $this->actualizarEstadoLibro($idLibro);


        return $this->respondDeleted([
            'message' => 'Préstamo eliminado'
        ]);
    }
}

