function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            // Ignorar anchors que sean simplemente "#" (botones con onclick, placeholders, etc.)
            const href = anchor.getAttribute('href');
            if (!href || href.trim() === '#' ) return;

            anchor.addEventListener('click', function (e) {
                // prevenir comportamiento por defecto solo cuando existe un target válido
                try {
                    const selector = href;
                    const target = document.querySelector(selector);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                } catch (err) {
                    // selector no válido o error en querySelector: ignorar
                }
            });
        });