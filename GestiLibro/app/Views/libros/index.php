<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    $user1 = session()->get('auth_user');
    $rol = strtolower($user1['rol'] ?? '');
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-book me-2"></i> Gestión de Libros
    </h4>

    <div class="mb-4">
        <?php if ($rol === 'administrador' || $rol === 'bibliotecario'): ?>
            <a href="<?= site_url('libros/create') ?>" class="btn btn-success">
                <i class="fa-solid fa-book-medical me-2"></i> Nuevo Libro
            </a>
        <?php endif; ?>
    </div>

</div>

<form method="get" class="mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <select name="disponibilidad" class="form-select">
                <option value="">-- Filtrar por disponibilidad --</option>
                <option value="disponible" <?= (service('request')->getGet('disponibilidad') === 'disponible') ? 'selected' : '' ?>>Disponible</option>
                <option value="no_disponible" <?= (service('request')->getGet('disponibilidad') === 'no_disponible') ? 'selected' : '' ?>>No disponible</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </div>
</form>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Editorial</th>
            <th>Año</th>
            <th>Categoría</th>
            <th>Cantidad Total</th>
            <th>Copias Disponibles</th>
            <th>Disponibilidad</th>
            <?php if ($rol === 'administrador' || $rol === 'bibliotecario'): ?>
                <th>Acciones</th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($libros)): ?>
            <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?= esc($libro['id_libro']) ?></td>
                    <td><?= esc($libro['titulo']) ?></td>
                    <td><?= esc($libro['autor']) ?></td>
                    <td><?= esc($libro['editorial']) ?></td>
                    <td><?= esc($libro['anio']) ?></td>
                    <td><?= esc($libro['categoria']) ?></td>
                    <td><?= esc($libro['cantidad']) ?></td>
                    <td><?= esc($libro['copias_disponibles']) ?></td>

                    <td>
                        <?php if ($libro['disponibilidad'] == 'disponible'): ?>
                            <span class="badge bg-success">Disponible</span>    
                        <?php elseif($libro['disponibilidad'] == 'no_disponible'): ?>
                            <span class="badge bg-secondary">No disponible</span>
                        <?php endif; ?>
                    </td>

                    <?php if ($rol === 'administrador' || $rol === 'bibliotecario'): ?>
                        <td>
                            <a href="<?= site_url('libros/edit/' . $libro['id_libro']) ?>" class="btn btn-sm btn-info">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="<?= site_url('libros/delete/' . $libro['id_libro']) ?>" 
                               onclick="return confirm('¿Seguro que deseas eliminar este libro?')"
                               class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="10" class="text-center">No hay libros registrados</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>