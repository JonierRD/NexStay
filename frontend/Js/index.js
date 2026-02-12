/**
 * NEXSTAY - INDEX.JS
 * Script principal para la página de inicio
 */

// Aplicar modo oscuro inmediatamente si está guardado (antes de mostrar splash)
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
  document.body.classList.add('dark-mode');
  document.documentElement.classList.add('dark-mode');
}

// Forzar scroll al inicio al cargar/recargar la página
if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual';
}
window.scrollTo(0, 0);

// ========================================
// SPLASH SCREEN
// ========================================
const splashScreen = document.getElementById('splashScreen');

// Solo mostrar splash si es la primera visita en esta sesión
if (!sessionStorage.getItem('splashShown')) {
  // Marcar que ya se mostró el splash
  sessionStorage.setItem('splashShown', 'true');
  
  // Ocultar splash después de 2.5 segundos
  setTimeout(() => {
    if (splashScreen) {
      splashScreen.classList.remove('active');
    }
  }, 2500);
} else {
  // Si ya se mostró, ocultarlo inmediatamente
  if (splashScreen) {
    splashScreen.classList.remove('active');
  }
}

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
  
// ========================================
// VARIABLES GLOBALES
// ========================================
const body = document.body;
const heroSection = document.querySelector('.hero');
const images = [
  'hotel-room.jpg',
  'hotel-room2.jpg',
  'hotel-room3.jpg',
  'hotel-room4.jpg'
];
let currentImageIndex = 0;
let slider1, slider2;

// ========================================
// NAVBAR UNDERLINE ANIMADA
// ========================================
const navLinks = document.querySelectorAll('.nav-link');
const navUnderline = document.querySelector('.nav-underline');
const navLinksWrapper = document.querySelector('.nav-links-wrapper');
let activeLink = navLinks[0];

function moveUnderlineTo(link) {
  if (!link || !navUnderline || !navLinksWrapper) return;
  const rect = link.getBoundingClientRect();
  const wrapperRect = navLinksWrapper.getBoundingClientRect();
  navUnderline.style.width = rect.width + 'px';
  navUnderline.style.left = (rect.left - wrapperRect.left) + 'px';
}

// Inicializar en el link activo (Inicio)
if (activeLink && navUnderline) {
  moveUnderlineTo(activeLink);
}

navLinks.forEach((link, index) => {
  link.addEventListener('mouseenter', () => moveUnderlineTo(link));
  link.addEventListener('focus', () => moveUnderlineTo(link));
  link.addEventListener('click', (e) => {
    e.preventDefault();
    activeLink = link;
    moveUnderlineTo(link);
    
    // Scroll a la sección
    const targetId = link.getAttribute('href');
    const targetSection = document.querySelector(targetId);
    if (targetSection) {
      targetSection.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// Volver al link activo cuando el mouse sale del nav
if (navLinksWrapper) {
  navLinksWrapper.addEventListener('mouseleave', () => moveUnderlineTo(activeLink));
}

// Cambiar subrayado al hacer scroll a la sección correspondiente
const sectionIds = Array.from(navLinks).map(link => link.getAttribute('href').replace('#',''));
const sectionElements = sectionIds.map(id => document.getElementById(id));

window.addEventListener('scroll', () => {
  const scrollPosition = window.scrollY + 150; // Offset para el header fijo
  const windowHeight = window.innerHeight;
  const documentHeight = document.documentElement.scrollHeight;
  
  // Si estamos cerca del final de la página (últimos 200px), mostrar contacto
  if (window.scrollY + windowHeight >= documentHeight - 200) {
    activeLink = navLinks[navLinks.length - 1]; // Último link (Contacto)
    moveUnderlineTo(activeLink);
    return;
  }
  
  let currentSectionIndex = 0;
  
  // Encontrar la sección actual basándose en la posición de scroll
  for (let i = 0; i < sectionElements.length; i++) {
    const section = sectionElements[i];
    if (section && scrollPosition >= section.offsetTop) {
      currentSectionIndex = i;
    }
  }
  
  // Actualizar el link activo
  activeLink = navLinks[currentSectionIndex];
  moveUnderlineTo(activeLink);
});

// ========================================
// MODO OSCURO / CLARO
// ========================================

// Ya se aplicó el tema guardado al inicio del script
const themeToggle = document.getElementById('themeToggle');

if (savedTheme === 'dark') {
  themeToggle.textContent = '☀️';
} else {
  themeToggle.textContent = '🌙';
}

// Función para cambiar entre modo claro y oscuro
themeToggle.addEventListener('click', () => {
  body.classList.toggle('dark-mode');
  document.documentElement.classList.toggle('dark-mode');
  
  if (body.classList.contains('dark-mode')) {
    themeToggle.textContent = '☀️';
    localStorage.setItem('theme', 'dark');
  } else {
    themeToggle.textContent = '🌙';
    localStorage.setItem('theme', 'light');
  }
});

// ========================================
// SLIDER AUTOMÁTICO DE IMÁGENES DE FONDO
// ========================================

// Crear capas del slider
function createSliderLayers() {
  // Crear dos capas para el efecto de deslizamiento
  slider1 = document.createElement('div');
  slider2 = document.createElement('div');
  
  slider1.className = 'hero-slider active';
  slider2.className = 'hero-slider';
  
  heroSection.appendChild(slider1);
  heroSection.appendChild(slider2);
  
  // Establecer primera imagen
  updateSliderImage(slider1, images[0]);
}

// Actualizar imagen de un slider
function updateSliderImage(sliderElement, imageName) {
  sliderElement.style.backgroundImage = `url('Img/${imageName}')`;
}

// Cambiar imagen con efecto de deslizamiento
function slideToNext() {
  currentImageIndex = (currentImageIndex + 1) % images.length;
  const nextIndex = (currentImageIndex + 1) % images.length;
  const currentImage = images[currentImageIndex];
  const nextImage = images[nextIndex];
  
  // Determinar qué capa está activa
  if (slider1.classList.contains('active')) {
    // Preparar slider2 con la nueva imagen (fuera de vista a la derecha)
    updateSliderImage(slider2, currentImage);
    
    // Esperar un frame para que el navegador aplique el estado inicial
    requestAnimationFrame(() => {
      // Slider1 sale hacia la izquierda, Slider2 entra desde la derecha
      slider1.classList.add('sliding-out');
      slider2.classList.add('active');
      
      // Después de la transición, limpiar clases y preparar siguiente imagen
      setTimeout(() => {
        slider1.classList.remove('active', 'sliding-out');
        updateSliderImage(slider1, nextImage);
      }, 1500);
    });
  } else {
    // Preparar slider1 con la nueva imagen (fuera de vista a la derecha)
    updateSliderImage(slider1, currentImage);
    
    // Esperar un frame para que el navegador aplique el estado inicial
    requestAnimationFrame(() => {
      // Slider2 sale hacia la izquierda, Slider1 entra desde la derecha
      slider2.classList.add('sliding-out');
      slider1.classList.add('active');
      
      // Después de la transición, limpiar clases y preparar siguiente imagen
      setTimeout(() => {
        slider2.classList.remove('active', 'sliding-out');
        updateSliderImage(slider2, nextImage);
      }, 1500);
    });
  }
}

// Precargar todas las imágenes
function preloadImages() {
  images.forEach((imageName) => {
    const img = new Image();
    img.src = `Img/${imageName}`;
  });
}

// Iniciar slider
preloadImages();
createSliderLayers();

// Cambiar imagen cada 5 segundos
setInterval(slideToNext, 5000);

// ========================================
// ANIMACIONES AL SCROLL
// ========================================
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, observerOptions);

// Observar las tarjetas para animaciones
const cards = document.querySelectorAll('.feature-card, .module-item');
cards.forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(20px)';
  card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(card);
});

// ========================================
// LOADER DE TRANSICIÓN A LOGIN
// ========================================
const pageLoader = document.getElementById('pageLoader');
const btnAccessSystem = document.getElementById('btnAccessSystem');
const btnStartNow = document.getElementById('btnStartNow');
const btnIngresarSistema = document.getElementById('btnIngresarSistema');

// Resetear loader al cargar la página
if (pageLoader) {
  pageLoader.classList.remove('active');
}

// Función para verificar si hay sesión activa
const hasActiveSession = () => localStorage.getItem('token') && localStorage.getItem('remember') === 'true';

function showLoaderAndNavigate(url) {
  const loaderText = pageLoader.querySelector('p');
  if (loaderText && url.includes('dashboard')) {
    loaderText.textContent = 'Cargando sistema...';
  }
  pageLoader.classList.add('active');
  setTimeout(() => {
    window.location.href = url;
  }, 2000);
}

// Agregar evento a todos los botones de acceso
[btnAccessSystem, btnStartNow, btnIngresarSistema].forEach(btn => {
  if (btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const url = hasActiveSession() ? 'dashboard.php' : this.href;
      showLoaderAndNavigate(url);
    });
  }
});

// Cerrar el DOMContentLoaded
});

// ========================================
// RESETEAR LOADER AL VOLVER CON BOTÓN ATRÁS
// ========================================
window.addEventListener('pageshow', function(event) {
  const pageLoader = document.getElementById('pageLoader');
  if (pageLoader) {
    pageLoader.classList.remove('active');
  }
});
