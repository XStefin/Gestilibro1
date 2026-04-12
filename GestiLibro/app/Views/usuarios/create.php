<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario</h4>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('usuarios/store') ?>" method="post" class="bg-white shadow-sm p-4 rounded" style="max-width: 600px;">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= old('nombre') ?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" value="<?= old('apellido') ?>" required>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" name="correo" id="correo" class="form-control" value="<?= old('correo') ?>" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= old('username') ?>" required>
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="">Seleccione un rol</option>
                <option value="Administrador" <?= old('rol') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                <option value="Bibliotecario" <?= old('rol') === 'Bibliotecario' ? 'selected' : '' ?>>Bibliotecario</option>
                <option value="Estudiante" <?= old('rol') === 'Estudiante' ? 'selected' : '' ?>>Estudiante</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">PIN</label>
            <input type="text" name="pin" class="form-control" maxlength="4" pattern="\d{4}" inputmode="numeric" placeholder="Ej: 1234" value="<?= old('pin') ?>">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="active" value="1" class="form-check-input" <?= old('active', '1') ? 'checked' : '' ?>>
            <label class="form-check-label">Activo</label>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

