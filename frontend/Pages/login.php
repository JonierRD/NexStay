<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NexStay - Sistema de Gestión Hotelera</title>
  <link rel="icon" href="../LogoApp.ico" type="image/x-icon">
  <script>
    // Aplicar tema inmediatamente según lo guardado en localStorage
    (function() {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#2563eb",
            "background-light": "#f8fafc",
            "background-dark": "#0f172a",
            "surface-dark": "#1e293b",
          },
          fontFamily: {
            display: ["Outfit", "sans-serif"],
            sans: ["Inter", "sans-serif"],
          },
          borderRadius: {
            DEFAULT: "0.75rem",
          },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="../css/pages/login.css?v=2">
  <script defer src="../Js/pages/login.js?v=2"></script>
</head>
<body class="bg-background-dark font-sans text-slate-100 min-h-screen">
  <script>
    // Aplicar dark a body inmediatamente si está guardado
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark');
    }
  </script>
  <div class="flex min-h-screen">
    
    <!-- Columna Izquierda: Formulario -->
    <div class="w-full lg:w-[450px] xl:w-[500px] flex flex-col justify-center px-8 sm:px-12 lg:px-16 bg-surface-dark z-10">
      <div class="w-full max-w-sm mx-auto">
        
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-10">
          <div class="w-10 h-10 bg-primary flex items-center justify-center rounded-xl shadow-lg shadow-primary/30 p-1.5">
            <img src="../Img/LogoApp.jpg" alt="NexStay Logo" class="w-full h-full object-contain">
          </div>
          <span class="text-2xl font-display font-bold tracking-tight text-white">NexStay</span>
        </div>

        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-display font-bold mb-2 text-white">Bienvenido de vuelta</h1>
          <p class="text-slate-400">Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <!-- Mensaje de error/éxito -->
        <div id="message" class="message px-4 py-3 rounded-xl mb-6 text-sm font-medium"></div>

        <!-- Formulario -->
        <form id="loginForm" class="space-y-6">
          
          <!-- Campo Usuario -->
          <div>
            <label class="flex items-center gap-2 text-sm font-medium mb-2 text-slate-300" for="usuario">
              <span class="material-icons-outlined text-base">person</span>
              Usuario
            </label>
            <div class="relative">
              <input 
                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-white placeholder:text-slate-600" 
                id="usuario" 
                name="usuario" 
                placeholder="Ingresa tu usuario" 
                required 
                type="text"
                autocomplete="username"
              >
            </div>
          </div>

          <!-- Campo Contraseña -->
          <div>
            <label class="flex items-center gap-2 text-sm font-medium mb-2 text-slate-300" for="password">
              <span class="material-icons-outlined text-base">lock</span>
              Contraseña
            </label>
            <div class="relative">
              <input 
                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-3 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-white placeholder:text-slate-600" 
                id="password" 
                name="password" 
                placeholder="Ingresa tu contraseña" 
                required 
                type="password"
                autocomplete="current-password"
              >
              <button 
                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors z-10 cursor-pointer" 
                type="button"
                id="togglePassword"
              >
                <span id="toggleIcon" class="material-icons-outlined text-xl">visibility</span>
              </button>
            </div>
          </div>

          <!-- Opciones -->
          <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer group">
              <input 
                id="remember" 
                class="w-5 h-5 rounded border-slate-700 text-primary focus:ring-primary/30 transition-all bg-slate-800" 
                type="checkbox"
              >
              <span class="ml-2 text-sm text-slate-400 group-hover:text-slate-200 transition-colors">Recordar sesión</span>
            </label>
            <a class="text-sm font-medium text-primary hover:underline underline-offset-4" href="#">¿Olvidaste tu contraseña?</a>
          </div>

          <!-- Botón de login -->
          <button 
            id="btnLogin"
            class="w-full bg-primary hover:bg-blue-600 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-[0.98] flex items-center justify-center gap-2" 
            type="submit"
          >
            <span class="btn-text">Iniciar Sesión</span>
            <span class="btn-loader hidden">
              <svg class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/>
                <path d="M12 2C6.48 2 2 6.48 2 12" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
              </svg>
            </span>
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-10 text-center">
          <p class="text-sm text-slate-400">
            ¿No tienes cuenta? <a id="btnBackToIndex" class="text-primary font-semibold hover:underline" href="../Index.html">Volver al inicio</a>
          </p>
          <p class="mt-8 text-xs text-slate-600">
            NexStay v2.0 - Sistema de Gestión Hotelera
          </p>
        </div>
      </div>
    </div>

    <!-- Columna Derecha: Hero Image -->
    <div class="hidden lg:flex flex-1 relative bg-slate-900 overflow-hidden">
      <img 
        alt="Hotel balcony at sunset" 
        class="absolute inset-0 w-full h-full object-cover opacity-80 scale-105 blur-[1px]" 
        src="../Img/fondo.jpg"
      >
      <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900/60 to-transparent"></div>
      
      <div class="relative z-10 flex flex-col justify-end p-20 w-full h-full">
        <div class="max-w-xl">
          <div class="w-16 h-1 bg-primary mb-6"></div>
          <h2 class="text-4xl xl:text-5xl font-display font-bold text-white mb-6 leading-tight">
            La excelencia en la gestión hotelera comienza aquí.
          </h2>
          <p class="text-lg text-slate-300 font-light leading-relaxed">
            Administre reservas, huéspedes y servicios con la plataforma más avanzada del mercado. Diseñada para hoteles que buscan la perfección en cada detalle.
          </p>
        </div>
      </div>

      <div class="absolute top-12 right-12 flex gap-4">
        <div class="bg-glass border border-white/10 px-4 py-2 rounded-full flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
          <span class="text-xs font-medium text-white tracking-wide uppercase">Sistema Operativo</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Loader de transición -->
  <div id="pageLoader" class="page-loader">
    <div class="loader-content">
      <div class="loader"></div>
      <p id="loaderText">Iniciando sesión...</p>
    </div>
  </div>

</body>
</html>