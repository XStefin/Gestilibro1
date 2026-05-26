<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LibroModel;

class PrestamoController extends ResourceController
{
    protected $modelName = 'App\Models\PrestamoModel';
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
    $idUsuario = $this->request->getGet('id_usuario') 
        ?: $this->request->getGet('usuario');

    $rol = strtolower(trim($this->request->getGet('rol') ?? ''));

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
    if ($rol === 'estudiante') {
        if (empty($idUsuario)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El id_usuario es obligatorio para consultar préstamos como estudiante'
            ]);
        }

        $builder->where('p.id_usuario', (int) $idUsuario);
    }
    if ($rol !== 'estudiante' && !empty($idUsuario)) {
        $builder->where('p.id_usuario', (int) $idUsuario);
    }

    $prestamos = $builder->get()->getResultArray();

    return $this->respond($prestamos);
}

    public function show($id = null)
    {
    $idUsuario = $this->request->getGet('id_usuario') 
        ?: $this->request->getGet('usuario');

    $rol = strtolower(trim($this->request->getGet('rol') ?? ''));

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

    if ($rol === 'estudiante') {
        if (empty($idUsuario)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El id_usuario es obligatorio para consultar préstamos como estudiante'
            ]);
        }

        $builder->where('p.id_usuario', (int) $idUsuario);
    }

    $prestamo = $builder->get()->getRowArray();

    if (!$prestamo) {
        return $this->failNotFound('Préstamo no encontrado');
    }

    return $this->respond($prestamo);
    }

    public function create()
    {
        $json = $this->leerJson();

        if (!$json['ok']) {
            return $json['response'];
        }

        $data = $json['data'];

        if (!isset($data['id_usuario']) || trim((string) $data['id_usuario']) === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'El campo id_usuario es obligatorio'
            ]);
        }

        if (!isset($data['id_libro']) || trim((string) $data['id_libro']) === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'El campo id_libro es obligatorio'
            ]);
        }

        $idUsuario = (int) $data['id_usuario'];
        $idLibro = (int) $data['id_libro'];
        $estadoNuevo = strtolower(trim($data['estado'] ?? 'prestado'));

        $libroModel = new LibroModel();

        $libroPrestado = $this->model
            ->where('id_usuario', $idUsuario)
            ->where('id_libro', $idLibro)
            ->whereIn('estado', ['prestado', 'atrasado'])
            ->first();

        if ($libroPrestado) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'El usuario ya tiene este libro prestado o atrasado'
            ]);
        }

        $libro = $libroModel->find($idLibro);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        if ($this->estadoOcupaStock($estadoNuevo) && (int) $libro['cantidad'] <= 0) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'No hay stock disponible'
            ]);
        }

        if ($this->estadoOcupaStock($estadoNuevo)) {
            $libroModel->set('cantidad', 'cantidad - 1', false)
                ->where('id_libro', $idLibro)
                ->update();

            $this->actualizarEstadoLibro($idLibro);
        }

        $datosInsertar = [
            'id_usuario' => $idUsuario,
            'id_libro' => $idLibro,
            'fecha_prestamo' => $data['fecha_prestamo'] ?? date('Y-m-d'),
            'fecha_devolucion' => $data['fecha_devolucion'] ?? null,
            'estado' => $estadoNuevo
        ];

        $insertId = $this->model->insert($datosInsertar, true);

        if (!$insertId) {
            return $this->failServerError('No fue posible crear el préstamo');
        }

        return $this->respondCreated([
            'message' => 'Préstamo creado correctamente',
            'data' => [
                'id_prestamo' => $insertId,
                'id_usuario' => $datosInsertar['id_usuario'],
                'id_libro' => $datosInsertar['id_libro'],
                'fecha_prestamo' => $datosInsertar['fecha_prestamo'],
                'fecha_devolucion' => $datosInsertar['fecha_devolucion'],
                'estado' => $datosInsertar['estado']
            ]
        ]);
    }

    public function update($id = null)
    {
        $prestamoActual = $this->model->find($id);

        if (!$prestamoActual) {
            return $this->failNotFound('Préstamo no encontrado');
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

        if (
            array_key_exists('id_libro', $data) &&
            (int) $data['id_libro'] !== (int) $prestamoActual['id_libro']
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No se permite cambiar el libro de un préstamo existente'
            ]);
        }

        $libroModel = new LibroModel();
        $libro = $libroModel->find($prestamoActual['id_libro']);

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        $estadoActual = strtolower(trim($prestamoActual['estado'] ?? ''));
        $nuevoEstado = strtolower(trim($data['estado'] ?? $estadoActual));

        if ($nuevoEstado === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'El campo estado no puede estar vacío'
            ]);
        }

        $estadoActualOcupaStock = $this->estadoOcupaStock($estadoActual);
        $nuevoEstadoOcupaStock = $this->estadoOcupaStock($nuevoEstado);

        if ($estadoActualOcupaStock && !$nuevoEstadoOcupaStock) {
            $libroModel->set('cantidad', 'cantidad + 1', false)
                ->where('id_libro', $prestamoActual['id_libro'])
                ->update();
        }

        if (!$estadoActualOcupaStock && $nuevoEstadoOcupaStock) {
            if ((int) $libro['cantidad'] <= 0) {
                return $this->response->setStatusCode(409)->setJSON([
                    'error' => 'No hay stock disponible para volver a prestar este libro'
                ]);
            }

            $libroModel->set('cantidad', 'cantidad - 1', false)
                ->where('id_libro', $prestamoActual['id_libro'])
                ->update();
        }

        $datosActualizar = [];

        if (array_key_exists('id_usuario', $data)) {
            $datosActualizar['id_usuario'] = (int) $data['id_usuario'];
        }

        if (array_key_exists('fecha_prestamo', $data)) {
            $datosActualizar['fecha_prestamo'] = $data['fecha_prestamo'];
        }

        if (array_key_exists('fecha_devolucion', $data)) {
            $datosActualizar['fecha_devolucion'] = $data['fecha_devolucion'];
        }

        if (array_key_exists('estado', $data)) {
            $datosActualizar['estado'] = $nuevoEstado;
        }

        if (empty($datosActualizar)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'No hay campos válidos para actualizar'
            ]);
        }

        $this->model->update($id, $datosActualizar);

        $this->actualizarEstadoLibro((int) $prestamoActual['id_libro']);

        return $this->respond([
            'message' => 'Préstamo actualizado correctamente',
            'data' => [
                'id_prestamo' => $id,
                'id_usuario' => $datosActualizar['id_usuario'] ?? $prestamoActual['id_usuario'],
                'id_libro' => $prestamoActual['id_libro'],
                'fecha_prestamo' => $datosActualizar['fecha_prestamo'] ?? $prestamoActual['fecha_prestamo'],
                'fecha_devolucion' => $datosActualizar['fecha_devolucion'] ?? $prestamoActual['fecha_devolucion'],
                'estado' => $datosActualizar['estado'] ?? $prestamoActual['estado']
            ]
        ]);
    }

    public function delete($id = null)
    {
        $prestamo = $this->model->find($id);

        if (!$prestamo) {
            return $this->failNotFound('Préstamo no encontrado');
        }

        if ($this->estadoOcupaStock($prestamo['estado'])) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'No se puede eliminar un préstamo en estado prestado o atrasado'
            ]);
        }

        $idLibro = (int) $prestamo['id_libro'];

        $this->model->delete($id);

        $this->actualizarEstadoLibro($idLibro);

        return $this->respondDeleted([
            'message' => 'Préstamo eliminado correctamente'
        ]);
    }
}
