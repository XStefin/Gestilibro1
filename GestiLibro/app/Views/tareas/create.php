<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h4 class="fw-bold mb-3">
        <i class="fa-solid fa-plus-circle me-2"></i> Nueva Tarea
    </h4>

    <div class="card p-4 shadow-sm">
        <form action="<?= site_url('tareas/store') ?>" method="post">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Usuario Asignado</label>
                    <select name="id_usuario" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id_usuario'] ?>"><?= esc($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="pendiente">Pendiente</option>
                        <option value="en progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="<?= site_url('tareas') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-save me-2"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
