<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestibiblio - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Font Awesome para el icono -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #d4145a, #3d5afe);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      width: 350px;
      text-align: center;
    }
    .login-container h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px; /* espacio entre icono y texto */
    }
    .login-container h1 i {
      color: #673ab7;
    }
    .login-container p {
      font-size: 14px;
      color: #777;
      margin-bottom: 25px;
    }
    .login-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    .login-container button {
      width: 100%;
      padding: 12px;
      background: #673ab7;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .login-container button:hover {
      background: #512da8;
    }
    .login-container a {
      display: block;
      margin-top: 15px;
      font-size: 13px;
      color: #3d5afe;
      text-decoration: none;
    }
    .login-container a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1><i class="fa-solid fa-book"></i><?php echo APP_NAME ?></h1>
    <p>Ingresa tus credenciales</p>
    <form action="<?= base_url('login/auth') ?>" method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">INICIAR SESIÓN</button>
    </form>
    <a href="#">¿Olvidaste tu contraseña?</a>
  </div>
</body>
</html>
