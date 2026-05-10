<?php

namespace App\Controllers;

use App\Models\PrestamoModel;
use CodeIgniter\RESTful\ResourceController;

class LibroController extends ResourceController
{
    protected $modelName = 'App\Models\LibroModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);

        if (!$data) {
            return $this->failNotFound('Libro no encontrado');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->fail('No se recibieron datos');
        }

        if (isset($data['cantidad'])) {
            $data['cantidad'] = (int) $data['cantidad'];
            $data['disponibilidad'] = $data['cantidad'] > 0
                ? 'disponible'
                : 'no_disponible';
        }

        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Libro creado correctamente'
        ]);
    }

    public function update($id = null)
    {
        $libro = $this->model->find($id);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->fail('No se recibieron datos');
        }

        if (isset($data['cantidad'])) {
            $data['cantidad'] = (int) $data['cantidad'];
            $data['disponibilidad'] = $data['cantidad'] > 0
                ? 'disponible'
                : 'no_disponible';
        }

        $this->model->update($id, $data);

        return $this->respond([
            'message' => 'Libro actualizado'
        ]);
    }

    public function delete($id = null)
    {
        $prestamoModel = new PrestamoModel();
        $prestamo = $prestamoModel->where('id_libro', $id)->first();

        if ($prestamo) {
            return $this->fail([
                'error' => 'Existe un préstamo asociado a este libro, no se puede eliminar'
            ]);
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => 'Libro eliminado'
        ]);
    }
}

