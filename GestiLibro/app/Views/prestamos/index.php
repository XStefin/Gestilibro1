<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
    $user1 = session()->get('authUser');
?>
<div class="container ">
    <h4 class="fw-bold mb-3"><i class="fa-solid fa-users me-2"></i> Gestión de Préstamos</h4>

        <div class="mb-4">
            <a href="<?= site_url('prestamos/create') ?>" class="btn btn-success">
                <i class="fa-solid fa-handshake me-2"></i> Nuevo Préstamo
            </a>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Libro</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <?php if ($user1['id_rol']==1 or  $user1['id_rol']==2) : ?>
                <th>Acciones</th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prestamos)): ?>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?= esc($prestamo['id_prestamo']) ?></td>
                        <td><?= esc($prestamo['nombre_usuario']) ?></td>
                        <td><?= esc($prestamo['titulo_libro']) ?></td>
                        <td><?= esc($prestamo['fecha_prestamo']) ?></td>
                        <td><?= esc($prestamo['fecha_devolucion'] ?? '-') ?></td>
                        <td>
                            <?php if ($prestamo['estado'] === 'prestado'): ?>
                                <span class="badge bg-warning text-dark">Prestado</span>
                            <?php elseif ($prestamo['estado'] === 'devuelto'): ?>
                                <span class="badge bg-success">Devuelto</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Atrasado</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($user1['id_rol']==1 or $user1['id_rol']==2) : ?>
                        <td>
                            <a href="<?= site_url('prestamos/edit/' . $prestamo['id_prestamo']) ?>" 
                               class="btn btn-sm btn-info">
                               <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="<?= site_url('prestamos/delete/' . $prestamo['id_prestamo']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Seguro que deseas eliminar este préstamo?')">
                               <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                        <?php endif ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No hay préstamos registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
