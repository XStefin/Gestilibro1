<?php

namespace App\Models\States;

class PrestadoState implements LibroState
{
    public function eliminar(LibroContext $context): void
    {
        throw new \Exception("No se puede eliminar el libro '{$context->getTitulo()}' porque está prestado.");
    }

    public function prestar(LibroContext $context): void
    {
        throw new \Exception("El libro '{$context->getTitulo()}' ya está prestado.");
    }
}
