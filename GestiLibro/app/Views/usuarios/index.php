<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-users me-2"></i> Gestión de Usuarios
    </h4>

    
    <div class="mb-4">
        <a href="<?= site_url('usuarios/create') ?>" class="btn btn-success">
            <i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario
        </a>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table align-middle">
            <thead class="table-dark">
                <tr>
                     <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= esc($u['id_usuario']) ?></td>
                            <td><?= esc($u['nombre']) ?></td>
                            <td><?= esc($u['apellido']) ?></td>
                            <td><?= esc($u['correo']) ?></td>
                            <td><?= esc($u['contrasena']) ?></td>
                            <td><?= esc($u['rol']) ?></td>
                            <td class="text-center">
                            
                            <a href="<?= site_url('usuarios/edit/' . $u['id_usuario']) ?>" class="btn btn-sm btn-info" >
                                  <i class="fa-solid fa-pen-to-square"></i>
                                    </a>


                                <a href="<?= site_url('usuarios/delete/' . $u['id_usuario']) ?>"
                                   onclick="return confirm('¿Seguro que deseas Eliminar este usuario?')"
                                   class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                        

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
