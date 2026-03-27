<?php

namespace App\Models\States;

class NoDisponibleState implements LibroState
{
    public function eliminar(LibroContext $context): void
    {
        throw new \Exception("El libro '{$context->getTitulo()}' ya está marcado como no disponible.");
    }

    public function prestar(LibroContext $context): void
    {
        throw new \Exception("El libro '{$context->getTitulo()}' no está disponible para préstamo.");
    }
}
