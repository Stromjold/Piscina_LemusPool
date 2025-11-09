// Este script se ejecuta cuando la página carga
        window.onload = function() {
            // 1. Obtiene los parámetros de la URL (ej: ?principal=...&nombre=...)
            const params = new URLSearchParams(window.location.search);
            
            // 2. Lee los valores que le pasamos
            const principalUrl = params.get('principal');
            const nombre = params.get('nombre');

            // 3. Define la URL de admin (basado en tu HTML, esta es fija)
            const adminUrl = 'administracion/admin_principal.html';

            // 4. Actualiza la página con la información correcta
            
            // 5. Asigna las URLs correctas a los botones
            
            // === CAMBIO REALIZADO ===
            // Ya no ocultamos el botón. Si la URL 'principal' existe,
            // se la ponemos. Si no, el botón igual se ve (pero su link es '#').
            if (principalUrl) {
                document.getElementById('linkPrincipal').href = principalUrl;
            }
            
            document.getElementById('linkAdmin').href = adminUrl;
        };