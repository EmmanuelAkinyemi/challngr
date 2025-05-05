document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('user-menu-button');
    const menu = document.getElementById('user-menu');

    // Toggle menu on avatar click
    btn.addEventListener('click', (e) => {
        e.stopPropagation();              // don’t bubble up to document
        menu.classList.toggle('hidden');
    });

    // Hide if clicking outside
    document.addEventListener('click', () => {
        if (!menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
})


document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');
    const [openIcon, closeIcon] = btn.querySelectorAll('svg');

    function isMobile() {
        return window.innerWidth < 768; // Tailwind “md” breakpoint
    }

    btn.addEventListener('click', (e) => {
        if (!isMobile()) return;        // ignore on desktop
        e.stopPropagation();
        menu.classList.toggle('hidden');
        openIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
    });

    // Close when clicking outside, but only on mobile
    document.addEventListener('click', (e) => {
        if (!isMobile()) return;
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                openIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Optional: close menu on window‑resize (desktop→mobile and back)
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            menu.classList.add('hidden');
            openIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
});


