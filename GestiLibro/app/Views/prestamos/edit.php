<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    $authUser = session()->get('auth_user');
    $rol = strtolower($authUser['rol'] ?? '');
    $puedeCambiarUsuario = in_array($rol, ['administrador', 'bibliotecario']);
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-pen-to-square"></i> Editar Préstamo</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('prestamos/update/' . $prestamo['id_prestamo']) ?>" method="post">
        
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Usuario</label>

            <?php if ($puedeCambiarUsuario): ?>
                <select name="id_usuario" id="id_usuario" class="form-select" required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= esc($usuario['id_usuario']) ?>" 
                            <?= old('id_usuario', $prestamo['id_usuario']) == $usuario['id_usuario'] ? 'selected' : '' ?>>
                            <?= esc($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="id_usuario" value="<?= esc($prestamo['id_usuario']) ?>">

                <select class="form-select" disabled>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= esc($usuario['id_usuario']) ?>" selected>
                            <?= esc($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="id_libro" class="form-label">Libro</label>
            <select name="id_libro" id="id_libro" class="form-select" required>
                <?php foreach ($libros as $libro): ?>
                    <option value="<?= esc($libro['id_libro']) ?>" 
                        <?= old('id_libro', $prestamo['id_libro']) == $libro['id_libro'] ? 'selected' : '' ?>>
                        <?= esc($libro['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
            <input type="date" 
                   name="fecha_prestamo" 
                   id="fecha_prestamo" 
                   class="form-control" 
                   value="<?= old('fecha_prestamo', $prestamo['fecha_prestamo']) ?>" 
                   required>
        </div>

        <div class="mb-3">
            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
            <input type="date" 
                   name="fecha_devolucion" 
                   id="fecha_devolucion" 
                   class="form-control" 
                   value="<?= old('fecha_devolucion', $prestamo['fecha_devolucion']) ?>">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="prestado" <?= old('estado', $prestamo['estado']) == 'prestado' ? 'selected' : '' ?>>Prestado</option>
                <option value="devuelto" <?= old('estado', $prestamo['estado']) == 'devuelto' ? 'selected' : '' ?>>Devuelto</option>
                <option value="atrasado" <?= old('estado', $prestamo['estado']) == 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>
        <a href="<?= site_url('prestamos') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </form>
</div>

<?= $this->endSection() ?>