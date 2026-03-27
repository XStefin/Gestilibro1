<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Editar Categoría</h2>

<form action="<?= base_url('categorias/update/'.$categoria['id_categoria']) ?>" method="post">
    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= esc($categoria['nombre']) ?>" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control"><?= esc($categoria['descripcion']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>
     <a href="<?= site_url('categorias') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
</form>

<?= $this->endSection() ?>
