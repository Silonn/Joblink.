document.addEventListener('DOMContentLoaded', () => {

    // --- LÓGICA PARA EL MENÚ MÓVIL (sin cambios) ---
    const menuToggleBtn = document.getElementById('menu-toggle-btn');
    const closeMenuBtn = document.getElementById('close-menu-btn');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

    if (menuToggleBtn) {
        menuToggleBtn.addEventListener('click', () => {
            mobileMenuOverlay.classList.add('active');
        });
    }

    if (closeMenuBtn) {
        closeMenuBtn.addEventListener('click', () => {
            mobileMenuOverlay.classList.remove('active');
        });
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', (event) => {
            if (event.target === mobileMenuOverlay) {
                mobileMenuOverlay.classList.remove('active');
            }
        });
    }

    // --- LÓGICA PARA LA BÚSQUEDA EN VIVO (sin cambios) ---
    // ... (tu código de búsqueda en vivo va aquí) ...

    // --- NUEVO: LÓGICA PARA ANIMAR TARJETAS AL HACER SCROLL ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Si la tarjeta está visible en la pantalla, le añadimos la clase 'visible'
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1 // La animación se dispara cuando el 10% de la tarjeta es visible
    });

    // Seleccionamos todas las tarjetas de empleo que tengan la clase 'hidden'
    const hiddenCards = document.querySelectorAll('.job-card.hidden');
    // Le decimos al observador que vigile cada una de estas tarjetas
    hiddenCards.forEach((card) => observer.observe(card));

});