<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <h4 class="fw-bold mb-3"><i class="fa-solid fa-user-gear me-2"></i> Gestión de Roles</h4>

    <a href="<?= site_url('roles/create')  ?>" class="btn btn-success">
        <i class="fa-solid fa-square-plus me-2"></i> Nuevo Rol
    </a>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th style="width: 120px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($roles)): ?>
                <?php foreach ($roles as $rol): ?>
                    <tr>
                        <td class="text-center"><?= $rol['id_rol'] ?></td>
                        <td><?= esc($rol['nombre']) ?></td>
                        <td class="text-center">
                            <a href="<?= site_url('roles/edit/'.$rol['id_rol']) ?>" class="btn btn-sm btn-info">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="<?= site_url('roles/delete/'.$rol['id_rol']) ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Seguro que deseas eliminar este rol?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">No hay roles registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
