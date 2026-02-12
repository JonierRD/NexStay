<?php
// Eliminar el token del navegador borrando la cookie
setcookie("token", "", time() - 3600, "/", "", false, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cerrando sesión...</title>
</head>
<body>
  <script>
    // Limpiar localStorage
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    localStorage.removeItem('nombre');
    localStorage.removeItem('rol');
    localStorage.removeItem('remember');
    
    // Redirigir al login y reemplazar historial para evitar volver atrás
    window.location.replace('Pages/login.php');
  </script>
</body>
</html>
