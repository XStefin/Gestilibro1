<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container col-md-6">
    <h4 class="fw-bold mb-3"><i class="fa-solid fa-plus me-2"></i> Crear Rol</h4>

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

    <form action="<?= site_url('roles/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" value="<?= old('nombre') ?>" required>
        </div>

        <a href="<?= site_url('roles') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>

<?= $this->endSection() ?>