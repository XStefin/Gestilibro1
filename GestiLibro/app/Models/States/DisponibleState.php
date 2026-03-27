<?php

namespace App\Models\States;

class DisponibleState implements LibroState
{
    public function eliminar(LibroContext $context): void
    {
        // Cambia el estado en la BD
        $context->setEstado(new NoDisponibleState(), 'no_disponible');
    }

    public function prestar(LibroContext $context): void
    {
        $context->setEstado(new PrestadoState(), 'prestado');
    }
}
