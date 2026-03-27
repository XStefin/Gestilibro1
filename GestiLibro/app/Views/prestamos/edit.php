<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-pen-to-square"></i> Editar Préstamo</h2>

    <form action="<?= site_url('prestamos/update/' . $prestamo['id_prestamo']) ?>" method="post">
        
        <!-- Usuario -->
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Usuario</label>
            <select name="id_usuario" id="id_usuario" class="form-select" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= esc($usuario['id_usuario']) ?>" 
                        <?= $prestamo['id_usuario'] == $usuario['id_usuario'] ? 'selected' : '' ?>>
                        <?= esc($usuario['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Libro -->
        <div class="mb-3">
            <label for="id_libro" class="form-label">Libro</label>
            <select name="id_libro" id="id_libro" class="form-select" required>
                <?php foreach ($libros as $libro): ?>
                    <option value="<?= esc($libro['id_libro']) ?>" 
                        <?= $prestamo['id_libro'] == $libro['id_libro'] ? 'selected' : '' ?>>
                        <?= esc($libro['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Fecha de préstamo -->
        <div class="mb-3">
            <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
            <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control" value="<?= esc($prestamo['fecha_prestamo']) ?>" required>
        </div>
        <!-- Fecha de devolución -->
        <div class="mb-3">
            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
            <input type="date" 
                   name="fecha_devolucion" 
                   id="fecha_devolucion" 
                   class="form-control" 
                   value="<?= esc($prestamo['fecha_devolucion']) ?>">
        </div>

        <!-- Estado -->
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="prestado" <?= $prestamo['estado'] == 'prestado' ? 'selected' : '' ?>>Prestado</option>
                <option value="devuelto" <?= $prestamo['estado'] == 'devuelto' ? 'selected' : '' ?>>Devuelto</option>
                <option value="atrasado" <?= $prestamo['estado'] == 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
            </select>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>
        <a href="<?= site_url('prestamos') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </form>
</div>

<?= $this->endSection() ?>
