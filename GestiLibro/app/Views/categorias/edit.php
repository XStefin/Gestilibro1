<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container col-md-6">
    <h2 class="mb-3">Editar Categoría</h2>

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

    <form action="<?= base_url('categorias/update/' . $categoria['id_categoria']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= old('nombre', esc($categoria['nombre'])) ?>" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"><?= old('descripcion', esc($categoria['descripcion'])) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>

        <a href="<?= site_url('categorias') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </form>
</div>

<?= $this->endSection() ?>