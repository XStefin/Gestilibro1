<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container col-md-6">
    <h2 class="mb-3">Añadir Categoría</h2>

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

    <form action="<?= base_url('categorias/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= old('nombre') ?>" required>
        </div>

        <div class="form-group mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"><?= old('descripcion') ?></textarea>
        </div>

        <a href="<?= base_url('categorias') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>

<?= $this->endSection() ?>