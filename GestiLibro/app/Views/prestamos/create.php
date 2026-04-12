<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4">Añadir Préstamo</h2>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('warning')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($libros)): ?>
        <div class="alert alert-warning">
            No hay libros disponibles para préstamo.
        </div>
    <?php endif; ?>

    <form action="<?= site_url('prestamos/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Usuario</label>

            <?php if ($puedeCambiarUsuario): ?>
                <select name="id_usuario" class="form-control" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>"
                            <?= old('id_usuario', $usuarioActivo) == $usuario['id_usuario'] ? 'selected' : '' ?>>
                            <?= esc($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="id_usuario" value="<?= esc($usuarioActivo) ?>">

                <select class="form-control" disabled>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>" selected>
                            <?= esc($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Libro</label>
            <select name="id_libro" class="form-select" required>
                <option value="">Seleccione un libro</option>
                <?php foreach ($libros as $l): ?>
                    <option value="<?= $l['id_libro'] ?>"
                        <?= old('id_libro') == $l['id_libro'] ? 'selected' : '' ?>>
                        <?= esc($l['titulo']) ?> (Disponibles: <?= esc($l['copias_disponibles']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
        $tz = new DateTimeZone('America/Bogota');
        $hoy = new DateTime('now', $tz);
        $fechaMinima = $hoy->format('Y-m-d');
        $fechaMaxima = (clone $hoy)->modify('+3 weeks')->format('Y-m-d');
        ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de Préstamo</label>
                <input type="date"
                       name="fecha_prestamo"
                       min="<?= $fechaMinima ?>"
                       max="<?= $fechaMaxima ?>"
                       class="form-control"
                       value="<?= old('fecha_prestamo', $fechaMinima) ?>"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de Devolución</label>
                <input type="date"
                       name="fecha_devolucion"
                       min="<?= $fechaMinima ?>"
                       max="<?= $fechaMaxima ?>"
                       class="form-control"
                       value="<?= old('fecha_devolucion') ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-success" <?= empty($libros) ? 'disabled' : '' ?>>Guardar</button>
        <a href="<?= site_url('prestamos') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>