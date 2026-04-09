document.addEventListener('DOMContentLoaded', function () {
    // Actieve sidebar link markeren
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        if (link.getAttribute('href') === path) link.classList.add('active');
    });
});