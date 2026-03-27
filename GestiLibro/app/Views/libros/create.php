<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-plus"></i> Añadir Libro</h2>

    <form action="<?= site_url('libros/store') ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" name="autor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Editorial</label>
            <input type="text" name="editorial" class="form-control">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Año</label>
                <input type="number" name="anio" class="form-control" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select">
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>"><?= esc($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= site_url('libros') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>
