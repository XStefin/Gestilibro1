<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container col-md-6">
    <h4 class="fw-bold mb-3"><i class="fa-solid fa-plus me-2"></i> Crear Rol</h4>

    <form action="<?= site_url('roles/store') ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <a href="<?= site_url('roles') ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>

<?= $this->endSection() ?>
