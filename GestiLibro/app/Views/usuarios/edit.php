<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h4 class="fw-bold mb-3">Editar Usuario</h4>

    <form method="post" action="<?= site_url('usuarios/update/' . $usuario['id_usuario']) ?>" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" value="<?= esc($usuario['nombre']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" value="<?= esc($usuario['apellido']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" value="<?= esc($usuario['correo']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="<?= esc($usuario['username']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" name="contrasena" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="Administrador" <?= $usuario['rol'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                <option value="Bibliotecario" <?= $usuario['rol'] === 'Bibliotecario' ? 'selected' : '' ?>>Bibliotecario</option>
                <option value="Estudiante" <?= $usuario['rol'] === 'Estudiante' ? 'selected' : '' ?>>Estudiante</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">PIN</label>
            <input 
                type="text" 
                name="pin" 
                class="form-control" 
                maxlength="4"
                pattern="\d{4}"
                inputmode="numeric"
                value="<?= esc($usuario['pin']) ?>">
        </div>

        <div class="mb-3 form-check">
            <input 
                type="checkbox" 
                name="active" 
                value="1" 
                class="form-check-input"
                <?= !empty($usuario['active']) ? 'checked' : '' ?>>
            <label class="form-check-label">Activo</label>
        </div>

        <button class="btn btn-success">Actualizar</button>
        <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?= $this->endSection() ?>