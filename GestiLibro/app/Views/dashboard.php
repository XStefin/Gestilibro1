<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            <?= $this->include('layouts/sidebar') ?>
        </div>

        <!-- Main content -->
        <div class="content flex-grow-1 p-4">
            <h5 class="mb-4 fw-bold">Dashboard</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-center bg-light-blue">
                        <div class="card-body">
                            <h6>Libros Totales</h6>
                            <h3><?php echo $libros ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center bg-light-pink">
                        <div class="card-body">
                            <h6>Préstamos activos</h6>
                            <h3><?php echo $prestamos ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center bg-light-purple text-white">
                        <div class="card-body">
                            <h6>Usuarios</h6>
                            <h3><?php echo $usuarios ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>