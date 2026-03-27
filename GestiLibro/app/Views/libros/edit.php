<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-pen-to-square"></i> Editar Libro</h2>

    <form action="<?= site_url('libros/update/' . $libro['id_libro']) ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= esc($libro['titulo']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" name="autor" class="form-control" value="<?= esc($libro['autor']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Editorial</label>
            <input type="text" name="editorial" class="form-control" value="<?= esc($libro['editorial']) ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Año</label>
                <input type="number" name="anio" class="form-control" value="<?= esc($libro['anio']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select">
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>" 
                            <?= $libro['id_categoria'] == $c['id_categoria'] ? 'selected' : '' ?>>
                            <?= esc($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Disponibilidad</label>
            <select name="disponibilidad" class="form-select">
                <option value="1" <?= $libro['disponibilidad'] ? 'selected' : '' ?>>Disponible</option>
                <option value="0" <?= !$libro['disponibilidad'] ? 'selected' : '' ?>>No disponible</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="<?= site_url('libros') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>
