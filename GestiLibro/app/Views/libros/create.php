<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-plus"></i> Añadir Libro</h2>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('libros/store') ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= old('titulo') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" name="autor" class="form-control" value="<?= old('autor') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Editorial</label>
            <input type="text" name="editorial" class="form-control" value="<?= old('editorial') ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Año</label>
                <input type="text" name="anio" class="form-control" maxlength="4" pattern="\d{4}" inputmode="numeric" placeholder="Ej: 2026" value="<?= old('anio') ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>" <?= old('id_categoria') == $c['id_categoria'] ? 'selected' : '' ?>>
                            <?= esc($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="1" step="1" value="<?= old('cantidad', 1) ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="<?= site_url('libros') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>