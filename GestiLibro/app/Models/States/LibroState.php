<?php

namespace App\Models\States;

interface LibroState
{
    public function eliminar(LibroContext $context): void;
    public function prestar(LibroContext $context): void;
}
