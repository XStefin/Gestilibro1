<?php

namespace App\Models\States;

use App\Models\LibroModel;

class LibroContext
{
    private LibroModel $model;
    private array $data;
    private LibroState $estado;

    public function __construct(LibroModel $model, array $data, LibroState $estado)
    {
        $this->model = $model;
        $this->data = $data;
        $this->estado = $estado;
    }

    public function getTitulo(): string
    {
        return $this->data['titulo'];
    }

    public function getId(): int
    {
        return $this->data['id_libro'];
    }

    public function getEstado(): string
    {
        return $this->data['estado'];
    }

    public function setEstado(LibroState $estado, string $nombreEstado)
    {
        $this->estado = $estado;
        $this->data['disponibilidad'] = $nombreEstado;
        // Actualiza en base de datos
        $this->model->update($this->getId(), ['disponibilidad' => $nombreEstado]);
    }

    public function eliminar()
    {
        $this->estado->eliminar($this);
    }

    public function prestar()
    {
        $this->estado->prestar($this);
    }
}
