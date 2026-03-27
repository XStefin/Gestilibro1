
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
    $user1 = session()->get('authUser');
?>
<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-tags me-2"></i> Gestión de Categorías
    </h4>
    
 <div class="mb-4">
    <a href="<?= site_url('categorias/create') ?>" class="btn btn-success">
        <i class="fa-solid fa-tag me-2"></i> Nueva Categoría
    </a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $categoria): ?>
            <tr>
                <td><?= esc($categoria['id_categoria']) ?></td>
                <td><?= esc($categoria['nombre']) ?></td>
                <td><?= esc($categoria['descripcion']) ?></td>
                <td>
                    <a href="<?= site_url('categorias/edit/'.$categoria['id_categoria']) ?>" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <a href="<?= site_url('categorias/delete/'.$categoria['id_categoria']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar categoría?')">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
