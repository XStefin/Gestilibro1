<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4">Añadir Préstamo</h2>

    <form action="<?= site_url('prestamos/store') ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <select name="id_usuario" class="form-select" required>
                <option value="">Seleccione un usuario</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>"><?= esc($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Libro</label>
            <select name="id_libro" class="form-select" required>
                <option value="">Seleccione un libro</option>
                <?php foreach ($libros as $l): ?>
                    <option value="<?= $l['id_libro'] ?>"><?= esc($l['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de Préstamo</label>
                <input type="date" name="fecha_prestamo" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de Devolución</label>
                <input type="date" name="fecha_devolucion" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="prestado">Prestado</option>
                <option value="devuelto">Devuelto</option>
                <option value="atrasado">Atrasado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= site_url('prestamos') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>
