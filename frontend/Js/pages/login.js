document.addEventListener('DOMContentLoaded', function() {
  
  // ========================================
  // RESETEAR LOADER AL CARGAR
  // ========================================
  var pageLoader = document.getElementById('pageLoader');
  if (pageLoader) {
    pageLoader.classList.remove('active');
  }
  
  // ========================================
  // TOGGLE PASSWORD
  // ========================================
  var btn = document.getElementById('togglePassword');
  var pwd = document.getElementById('password');
  var icn = document.getElementById('toggleIcon');
  
  if (btn && pwd && icn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      if (pwd.type === 'password') {
        pwd.type = 'text';
        icn.textContent = 'visibility_off';
      } else {
        pwd.type = 'password';
        icn.textContent = 'visibility';
      }
    });
  }
  
  // Login form
  var form = document.getElementById('loginForm');
  var btnLogin = document.getElementById('btnLogin');
  var btnText = btnLogin.querySelector('.btn-text');
  var btnLoader = btnLogin.querySelector('.btn-loader');
  var messageDiv = document.getElementById('message');
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    var usuario = document.getElementById('usuario').value.trim();
    var password = document.getElementById('password').value;
    
    if (!usuario || !password) {
      mostrarMensaje('Por favor completa todos los campos', 'error');
      return;
    }
    
    btnLogin.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';
    btnLoader.classList.remove('hidden');
    ocultarMensaje();
    
    try {
      var response = await fetch('/NexStay/backend/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario: usuario, password: password })
      });
      
      var data = await response.json();
      
      if (data.success) {
        mostrarMensaje('¡Bienvenido! Redirigiendo...', 'success');
        
        var expirationDays = document.getElementById('remember').checked ? 30 : 1;
        var expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + expirationDays);
        document.cookie = 'token=' + data.token + '; path=/; expires=' + expirationDate.toUTCString();
        
        localStorage.setItem('token', data.token);
        localStorage.setItem('usuario', data.usuario);
        localStorage.setItem('nombre', data.nombre);
        localStorage.setItem('rol', data.rol);
        
        if (document.getElementById('remember').checked) {
          localStorage.setItem('remember', 'true');
        }
        
        // Mostrar loader y redirigir
        setTimeout(function() {
          if (pageLoader) {
            pageLoader.classList.add('active');
          }
        }, 500);
        
        setTimeout(function() {
          window.location.replace('../dashboard.php');
        }, 2000);
        
      } else {
        mostrarMensaje(data.message || 'Usuario o contraseña incorrectos', 'error');
        btnLogin.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';
        btnLoader.classList.add('hidden');
      }
      
    } catch (error) {
      console.error('Error:', error);
      mostrarMensaje('Error de conexión. Verifica que el servidor esté activo.', 'error');
      btnLogin.disabled = false;
      btnText.style.display = 'inline-block';
      btnLoader.style.display = 'none';
      btnLoader.classList.add('hidden');
    }
  });
  
  function mostrarMensaje(text, type) {
    messageDiv.textContent = text;
    messageDiv.className = 'message ' + type;
    messageDiv.style.display = 'block';
  }
  
  function ocultarMensaje() {
    messageDiv.style.display = 'none';
    messageDiv.className = 'message';
  }
  
  // Verificar sesión
  var token = localStorage.getItem('token');
  var remember = localStorage.getItem('remember');
  
  if (token && remember === 'true') {
    var cookieExists = document.cookie.split(';').some(function(c) {
      return c.trim().startsWith('token=');
    });
    
    if (cookieExists) {
      // Si ya hay sesión activa, redirigir al dashboard sin mostrar el login
      window.location.replace('../dashboard.php');
    } else {
      localStorage.removeItem('token');
      localStorage.removeItem('usuario');
      localStorage.removeItem('nombre');
      localStorage.removeItem('rol');
      localStorage.removeItem('remember');
    }
  }
  
  // ========================================
  // NAVEGACIÓN CON LOADER
  // ========================================
  var btnBackToIndex = document.getElementById('btnBackToIndex');
  
  if (btnBackToIndex) {
    btnBackToIndex.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Cambiar texto del loader
      var loaderText = document.getElementById('loaderText');
      if (loaderText) {
        loaderText.textContent = 'Volviendo al inicio...';
      }
      
      // Aplicar tema al loader según localStorage
      if (pageLoader) {
        if (localStorage.getItem('theme') === 'dark') {
          pageLoader.classList.add('dark');
        }
        pageLoader.classList.add('active');
      }
      
      // Redirigir después de 2 segundos
      setTimeout(function() {
        window.location.href = '../Index.html';
      }, 2000);
    });
  }
});

// ========================================
// RESETEAR LOADER AL VOLVER CON BOTÓN ATRÁS
// ========================================
window.addEventListener('pageshow', function(event) {
  var pageLoader = document.getElementById('pageLoader');
  if (pageLoader) {
    pageLoader.classList.remove('active');
  }
});
