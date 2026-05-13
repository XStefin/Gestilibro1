<?php

namespace App\Controllers;

use App\Models\LibroModel;
use CodeIgniter\RESTful\ResourceController;

class CategoriaController extends ResourceController
{
    protected $modelName = 'App\Models\CategoriaModel';
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
        $categoria = $this->model->find($id);

        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }

        return $this->respond($categoria);
    }

    public function create()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        if (!isset($data['nombre']) || trim($data['nombre']) === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'El campo nombre es obligatorio'
            ]);
        }

        $nombre = trim($data['nombre']);

        if ($this->model->where('nombre', $nombre)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Esta categoría ya está en uso'
            ]);
        }

        $datosInsertar = [
            'nombre' => $nombre,
            'descripcion' => $data['descripcion'] ?? null
        ];

        $this->model->insert($datosInsertar);

        return $this->respondCreated([
            'message' => 'Categoría creada correctamente',
            'data' => [
                'id_categoria' => $this->model->getInsertID(),
                'nombre' => $datosInsertar['nombre'],
                'descripcion' => $datosInsertar['descripcion']
            ]
        ]);
    }

    public function update($id = null)
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        $categoria = $this->model->find($id);

        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se recibieron datos para actualizar'
            ]);
        }

        if (array_key_exists('nombre', $data) && trim($data['nombre']) === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'El campo nombre no puede estar vacío'
            ]);
        }

        if (isset($data['nombre']) && trim($data['nombre']) !== '') {
            $nombre = trim($data['nombre']);

            $categoriaExistente = $this->model
                ->where('nombre', $nombre)
                ->where('id_categoria !=', $id)
                ->first();

            if ($categoriaExistente) {
                return $this->response->setStatusCode(409)->setJSON([
                    'error' => 'Esta categoría ya está en uso'
                ]);
            }
        }

        $datosActualizar = [];

        if (array_key_exists('nombre', $data)) {
            $datosActualizar['nombre'] = trim($data['nombre']);
        }

        if (array_key_exists('descripcion', $data)) {
            $datosActualizar['descripcion'] = $data['descripcion'];
        }

        if (empty($datosActualizar)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No hay campos válidos para actualizar'
            ]);
        }

        $this->model->update($id, $datosActualizar);

        return $this->respond([
            'message' => 'Categoría actualizada correctamente',
            'data' => [
                'id_categoria' => $id,
                'nombre' => $datosActualizar['nombre'] ?? $categoria['nombre'],
                'descripcion' => $datosActualizar['descripcion'] ?? $categoria['descripcion']
            ]
        ]);
    }

    public function delete($id = null)
    {
        $categoria = $this->model->find($id);

        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }

        $LibroModel = new LibroModel();

        $Libro = $LibroModel->where('id_categoria', $id)->first();

        if ($Libro) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Existe un libro asociado a esta categoría, no se puede eliminar'
            ]);
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => 'Categoría eliminada correctamente'
        ]);
    }
}
