<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fa-solid fa-pen-to-square"></i> Editar Libro</h2>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

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
                <input 
                    type="text" 
                    name="anio" 
                    class="form-control" 
                    maxlength="4" 
                    pattern="\d{4}" 
                    inputmode="numeric"
                    value="<?= esc($libro['anio']) ?>"
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Categoría</label>
                <input 
                    type="text" 
                    name="categoria" 
                    class="form-control" 
                    value="<?= esc($libro['categoria']) ?>" 
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Cantidad</label>
                <input 
                    type="number" 
                    name="cantidad" 
                    class="form-control" 
                    min="1" 
                    step="1"
                    value="<?= esc($libro['cantidad']) ?>" 
                    required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Disponibilidad</label>
            <select name="disponibilidad" class="form-select">
                <option value="disponible" <?= $libro['disponibilidad'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="no_disponible" <?= $libro['disponibilidad'] === 'no_disponible' ? 'selected' : '' ?>>No disponible</option>
                <option value="prestado" <?= $libro['disponibilidad'] === 'prestado' ? 'selected' : '' ?>>Prestado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="<?= site_url('libros') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>