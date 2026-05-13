<?php

namespace App\Controllers;

use App\Models\PrestamoModel;
use CodeIgniter\RESTful\ResourceController;

class LibroController extends ResourceController
{
    protected $modelName = 'App\Models\LibroModel';
    protected $format = 'json';

    /**
     * Método estándar para leer JSON desde Postman o desde el frontend.
     * Se usa en create() y update().
     */
    private function leerJson()
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false) {
            $rawBody = '';
        }

        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return [
                'ok' => false,
                'response' => $this->response->setStatusCode(400)->setJSON([
                    'error' => 'JSON inválido',
                    'detalle' => json_last_error_msg(),
                    'body_recibido' => $rawBody,
                    'content_type' => $this->request->getHeaderLine('Content-Type')
                ])
            ];
        }

        return [
            'ok' => true,
            'data' => $data
        ];
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $libro = $this->model->find($id);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        return $this->respond($libro);
    }

    public function create()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se recibieron datos'
            ]);
        }

        if (isset($data['cantidad'])) {
            $data['cantidad'] = (int) $data['cantidad'];
            $data['disponibilidad'] = $data['cantidad'] > 0
                ? 'disponible'
                : 'no_disponible';
        }

        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Libro creado correctamente',
            'data' => [
                'id_libro' => $this->model->getInsertID(),
                'libro' => $data
            ]
        ]);
    }

    public function update($id = null)
    {
        $libro = $this->model->find($id);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se recibieron datos para actualizar'
            ]);
        }

        if (isset($data['cantidad'])) {
            $data['cantidad'] = (int) $data['cantidad'];
            $data['disponibilidad'] = $data['cantidad'] > 0
                ? 'disponible'
                : 'no_disponible';
        }

        $this->model->update($id, $data);

        $libroActualizado = $this->model->find($id);

        return $this->respond([
            'message' => 'Libro actualizado correctamente',
            'data' => $libroActualizado
        ]);
    }

    public function delete($id = null)
    {
        $libro = $this->model->find($id);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        $prestamoModel = new PrestamoModel();

        $prestamo = $prestamoModel->where('id_libro', $id)->first();

        if ($prestamo) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Existe un préstamo asociado a este libro, no se puede eliminar'
            ]);
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => 'Libro eliminado correctamente'
        ]);
    }
}
