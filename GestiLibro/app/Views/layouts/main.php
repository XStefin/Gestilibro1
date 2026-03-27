<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= APP_NAME ?? 'Biblioteca' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="d-flex">
        <!-- 🔹 Sidebar -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- 🔹 Contenido dinámico -->
        <div class="content flex-grow-1 p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</body>
</html>
