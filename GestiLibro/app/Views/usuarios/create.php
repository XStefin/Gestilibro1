<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario</h4>

    <form action="<?= site_url('usuarios/store') ?>" method="post" class="bg-white shadow-sm p-4 rounded" style="max-width: 600px;">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" name="correo" id="correo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select name="id_rol" id="id_rol" class="form-select" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= esc($rol['id_rol']) ?>"><?= esc($rol['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

