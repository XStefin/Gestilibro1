<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Tarea
    </h4>

    <div class="card p-4 shadow-sm">
        <form action="<?= site_url('tareas/update/' . $tarea['id_tarea']) ?>" method="post">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Usuario Asignado</label>
                    <select name="id_usuario" class="form-select" required>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id_usuario'] ?>" 
                                <?= $u['id_usuario'] == $tarea['id_usuario'] ? 'selected' : '' ?>>
                                <?= esc($u['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" 
                           value="<?= esc($tarea['titulo']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= esc($tarea['descripcion']) ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" 
                           value="<?= esc($tarea['fecha_vencimiento']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="baja" <?= $tarea['prioridad'] == 'baja' ? 'selected' : '' ?>>Baja</option>
                        <option value="media" <?= $tarea['prioridad'] == 'media' ? 'selected' : '' ?>>Media</option>
                        <option value="alta" <?= $tarea['prioridad'] == 'alta' ? 'selected' : '' ?>>Alta</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="pendiente" <?= $tarea['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en progreso" <?= $tarea['estado'] == 'en progreso' ? 'selected' : '' ?>>En Progreso</option>
                        <option value="completada" <?= $tarea['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-save"></i> Actualizar
        </button>
        <a href="<?= site_url('prestamos') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
