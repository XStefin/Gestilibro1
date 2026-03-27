<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container col-md-6">
    <h4 class="fw-bold mb-3"><i class="fa-solid fa-edit me-2"></i> Editar Rol</h4>

    <form action="<?= site_url('roles/update/'.$rol['id_rol']) ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" value="<?= esc($rol['nombre']) ?>" required>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>
        <a href="<?= site_url('roles') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </form>
</div>

<?= $this->endSection() ?>
