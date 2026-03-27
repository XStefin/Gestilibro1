<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h4 class="fw-bold mb-4">
        <i class="fa-solid fa-shield-halved me-2"></i> Auditoría del Sistema
    </h4>

    <!-- ✅ Filtros -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-3">
            <input type="text" name="usuario" placeholder="Usuario"
                   value="<?= esc($usuario ?? '') ?>" class="form-control">
        </div>

        <div class="col-md-3">
            <select name="accion" class="form-select">
                <option value="">Todas las acciones</option>
                <option value="insert" <?= ($accion ?? '')=='insert'?'selected':'' ?>>Insertar</option>
                <option value="update" <?= ($accion ?? '')=='update'?'selected':'' ?>>Actualizar</option>
                <option value="delete" <?= ($accion ?? '')=='delete'?'selected':'' ?>>Eliminar</option>
                <option value="login"  <?= ($accion ?? '')=='login'?'selected':'' ?>>Login</option>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="fecha" class="form-control"
                   value="<?= esc($fecha ?? '') ?>">
        </div>

        
        
        <div class="col-md-2 d-grid">
            <button class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i> Filtrar
        </button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="<?= base_url('auditoria') ?>" class="btn btn-secondary">
             <i class="fa-solid fa-rotate-left me-1"></i> Restablecer
         </a>
        </div>

    </form>

    <!-- ✅ Tabla Auditoría -->
    <table class="table table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Usuario</th>
                <th>Entidad</th>
                <th>ID Referencia</th>
                <th>Acción</th>
                <th>Fecha</th>
                <th>Detalle</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($auditorias as $a): ?>
            <?php
                $username = $a->username ?? 'Desconocido';
                $entidad = str_replace('Model', '', basename(str_replace('\\', '/', $a->entidad)));;
                $detalleLimpio = json_encode(json_decode($a->detalle), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

                $badgeClass = [
                    'insert' => 'success',
                    'update' => 'primary',
                    'delete' => 'danger',
                    'login'  => 'warning'
                ][$a->accion] ?? 'secondary';
            ?>
            <tr>
                <td><?= esc($username) ?></td>
                <td><?= esc($entidad) ?></td>
                <td><?= esc($a->entidad_id) ?></td>
                <td><span class="badge bg-<?= $badgeClass ?>"><?= esc($a->accion) ?></span></td>
                <td><?= esc($a->fecha) ?></td>
                <td class="text-start">
                    <pre style="white-space: pre-wrap; font-size:12px; margin:0;">
               <?= esc($detalleLimpio) ?>
                    </pre>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

<?= $this->endSection() ?>
