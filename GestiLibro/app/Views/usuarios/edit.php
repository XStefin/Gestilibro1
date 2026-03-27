<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h4 class="fw-bold mb-3">Editar Usuario</h4>

    <form method="post" action="<?= site_url('usuarios/update/'.$usuario['id_usuario']) ?>" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= esc($usuario['nombre']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Apellido</label>
            <input type="text" name="apellido" value="<?= esc($usuario['apellido']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Correo</label>
            <input type="email" name="correo" value="<?= esc($usuario['correo']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nueva Contraseña (opcional)</label>
            <input type="password" name="contrasena" class="form-control">
        </div>
        <div class="mb-3">
            <label>Rol</label>
            <select name="id_rol" class="form-select" required>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id_rol'] ?>" <?= $rol['id_rol'] == $usuario['id_rol'] ? 'selected' : '' ?>>
                        <?= esc($rol['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-success">Actualizar</button>
        <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
