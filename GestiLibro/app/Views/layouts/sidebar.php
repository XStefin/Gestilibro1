<?php
    $authUser = session()->get('auth_user');

    $usuarioBD = null;
    $nombreCompleto = 'Usuario';
    $nombreRol = 'Sin rol';
    $rol = '';

    if ($authUser && !empty($authUser['id_usuario'])) {
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuarioBD = $usuarioModel->find($authUser['id_usuario']);

        if ($usuarioBD) {
            $nombreCompleto = trim(($usuarioBD['nombre'] ?? '') . ' ' . ($usuarioBD['apellido'] ?? ''));
            $nombreRol = $usuarioBD['rol'] ?? 'Sin rol';
            $rol = strtolower($usuarioBD['rol'] ?? '');
        } else {
            $nombreCompleto = $authUser['nombreCompleto'] ?? 'Usuario';
            $nombreRol = $authUser['rol'] ?? 'Sin rol';
            $rol = strtolower($authUser['rol'] ?? '');
        }
    }
?>

<div class="sidebar d-flex flex-column justify-content-between p-3"
     style="width: 260px; background-color: #fff4e6; min-height: 100vh;">
    
    <div>
        <h4 class="fw-bold mb-4 text-center">
            <i class="fa-solid fa-book me-2"></i><?= esc(APP_NAME) ?>
        </h4>

        <div class="text-center mb-4">
            <img src="<?= base_url('images/usuario.jpg') ?>" class="rounded-circle mb-2" alt="User" width="80" height="80">
            <h6 class="m-0"><?= esc($nombreCompleto) ?></h6>
            <small class="text-warning"><?= esc($nombreRol) ?></small>
        </div>

        <ul class="nav flex-column">
            <?php if ($rol === 'administrador'): ?>
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
            <?php endif; ?>

            <li class="nav-item mb-2">
                <a href="<?= base_url('libros') ?>" class="nav-link d-flex align-items-center">
                    <i class="fa-solid fa-book-open me-2"></i> <span>Libros</span>
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= base_url('prestamos') ?>" class="nav-link d-flex align-items-center">
                    <i class="fa-solid fa-handshake me-2"></i> <span>Préstamos</span>
                </a>
            </li>
        </ul>
    </div>

    <a href="<?= site_url('login/logout') ?>" class="nav-link text-danger mt-auto d-flex align-items-center">
        <i class="fa-solid fa-right-from-bracket me-2"></i> <span>Cerrar Sesión</span>
    </a>
</div>