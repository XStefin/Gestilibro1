<?php

namespace App\Repositories;

use App\Models\LibroModel;

class LibroRepository
{
    protected LibroModel $model;

    public function __construct()
    {
        $this->model = new LibroModel();
    }

    public function obtenerTodosConCategoria($disponibilidad): array
    {
        return $this->model->conCategoria($disponibilidad);
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function obtenerLibroConEstado(int $id)
    {
        return $this->model->getLibroConEstado($id);
    }

    public function crear(array $data): bool
    {
        return $this->model->insert($data);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function eliminarLogico(int $id): bool
    {
        $libro = $this->model->getLibroConEstado($id);
        if (!$libro) return false;

        // Esto usa tu patrón State internamente
        $libro->eliminar();
        return true;
    }
}
