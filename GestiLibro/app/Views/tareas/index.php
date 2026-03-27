<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-list-check me-2"></i> Gestión de Tareas
    </h4>

    <div class="mb-4">
        <a href="<?= site_url('tareas/create') ?>" class="btn btn-success">
            <i class="fa-solid fa-square-plus me-2"></i> Nueva Tarea
        </a>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha Creación</th>
                    <th>Fecha Vencimiento</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tareas)): ?>
                    <?php foreach ($tareas as $t): ?>
                        <tr>
                            <td><?= esc($t['id_tarea']) ?></td>
                            <td><?= esc($t['usuario']) ?></td>
                            <td><?= esc($t['titulo']) ?></td>
                            <td><?= esc($t['descripcion']) ?></td>
                            <td><?= esc($t['fecha_creacion']) ?></td>
                            <td><?= esc($t['fecha_vencimiento']) ?></td>

                            <td>
                                <span class="badge bg-<?= 
                                    $t['estado'] == 'pendiente' ? 'warning text-dark' : 
                                    ($t['estado'] == 'en progreso' ? 'info text-dark' : 'success') 
                                ?>">
                                    <?= ucfirst($t['estado']) ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-<?= 
                                    $t['prioridad'] == 'alta' ? 'danger' :
                                    ($t['prioridad'] == 'media' ? 'warning text-dark' : 'success')
                                ?>">
                                    <?= ucfirst($t['prioridad']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="<?= site_url('tareas/edit/' . $t['id_tarea']) ?>" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="<?= site_url('tareas/delete/' . $t['id_tarea']) ?>" 
                                   onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')" 
                                   class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">No hay tareas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
