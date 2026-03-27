<?php
    $user = session()->get('authUser');
?>
<div class="sidebar d-flex flex-column justify-content-between p-3"
     style="width: 260px; background-color: #fff4e6; min-height: 100vh;">
    <div>
        <h4 class="fw-bold mb-4 text-center">
            <i class="fa-solid fa-book me-2"></i><?= esc(APP_NAME) ?>
        </h4>

        <div class="text-center mb-4">
            <img src="<?= base_url('public/images/usuario.jpg') ?>" class="rounded-circle mb-2" alt="User" width="80" height="80">
            <h6 class="m-0"><?= esc($user['nombreCompleto'] ?? 'SApo Culiao') ?></h6>
            <?php if ($user && $user['id_rol'] == 1): ?>
                <small class="text-warning">Admin</small>
            <?php elseif ($user && $user['id_rol'] == 2) :  ?>
                <small class="text-warning">Usuario</small>
            <?php endif ?> 
        </div>

        <ul class="nav flex-column">
            <?php if ($user && $user['id_rol'] == 1): ?>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-house me-2"></i> <span>Home</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('usuarios') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-user me-2"></i> <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('roles') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-user-gear me-2"></i> <span>Roles</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item mb-2">
                <a href="<?= base_url('libros') ?>" class="nav-link d-flex align-items-center">
                    <i class="fa-solid fa-book-open me-2"></i> <span>Libros</span>
                </a>
            </li>

            <?php if ($user && $user['id_rol'] == 1): ?>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('categorias') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-tags me-2"></i> <span>Categorías</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item mb-2">
                <a href="<?= base_url('prestamos') ?>" class="nav-link d-flex align-items-center">
                    <i class="fa-solid fa-handshake me-2"></i> <span>Préstamos</span>
                </a>
            </li>

            <?php if ($user && $user['id_rol'] == 1): ?>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('auditoria') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-shield-halved me-2"></i> <span>Auditoría</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="<?= base_url('tareas') ?>" class="nav-link d-flex align-items-center">
                        <i class="fa-solid fa-list-check me-2"></i> <span>Gestión de Tareas</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <a href="<?= site_url('login/logout') ?>" class="nav-link text-danger mt-auto d-flex align-items-center">
        <i class="fa-solid fa-right-from-bracket me-2"></i> <span>Cerrar Sesión</span>
    </a>
</div>
