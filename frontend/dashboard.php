<?php
require_once __DIR__ . "/../backend/auth/require_auth.php";

// Validar token (cualquier usuario logueado puede entrar)
require_auth();

// Obtener datos del usuario actual
$usuarioData = getAuthUser();
$usuario = $usuarioData->nombre ?? 'Desconocido';
$rol     = $usuarioData->rol ?? 'Sin rol';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NexStay Dashboard</title>
    <style>
        body { opacity: 0; transition: opacity 0.1s; }
        body.loaded { opacity: 1; }
    </style>
    <script>
        // Verificar sesión ANTES de cargar el contenido
        if (!localStorage.getItem('token')) {
            window.location.replace('Index.html');
        } else {
            // Si hay sesión, mostrar el body
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('loaded');
            });
        }
    </script>
</head>
<body>

<h1>Bienvenido al sistema NexStay</h1>

<p><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></p>
<p><strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>

<hr>

<h2>Menú Principal</h2>

<?php if ($rol === 'admin'): ?>
    <!-- TODO el sistema -->
    <ul>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="empleados.php">Empleados</a></li>
        <li><a href="habitaciones.php">Habitaciones</a></li>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="mantenimientos.php">Mantenimientos</a></li>
        <li><a href="parqueadero.php">Parqueadero</a></li>
        <li><a href="reservas.php">Reservas</a></li>
        <li><a href="reserva_servicio.php">Reserva Servicios</a></li>
        <li><a href="servicios.php">Servicios</a></li>
        <li><a href="facturas.php">Facturas</a></li>
        <li><a href="usuarios.php">Usuarios</a></li>
    </ul>

<?php elseif ($rol === 'recepcionista'): ?>
    <!-- Menú ampliado para recepcionista -->
    <ul>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="reservas.php">Reservas</a></li>
        <li><a href="reserva_servicio.php">Reserva Servicios</a></li>
        <li><a href="parqueadero.php">Parqueadero</a></li>
        <li><a href="habitaciones.php">Habitaciones</a></li>
        <li><a href="inventario.php">Inventario</a></li>
        <li><a href="mantenimientos.php">Mantenimientos</a></li>
        <li><a href="servicios.php">Servicios</a></li>
        <li><a href="facturas.php">Facturas</a></li>
    </ul>

<?php else: ?>
    <p style="color:red;">Rol desconocido. Contacte al administrador.</p>
<?php endif; ?>

<hr>

<a href="logout.php">Cerrar sesión</a>

<script>
// Prevenir volver a dashboard después de cerrar sesión
window.addEventListener('pageshow', function(event) {
  // Si la página se carga desde el caché del navegador (botón atrás)
  if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
    // Verificar si hay sesión activa
    if (!localStorage.getItem('token')) {
      window.location.replace('Index.html');
    }
  }
});
</script>

</body>
</html>
