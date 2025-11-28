/* Archivo: controlador.js */

const Enrutador = {
    // 1. MAPA DE DIRECCIONES
    // Define la relación entre: Código del sitio -> Login -> Admin -> Plantilla Pública
    rutas: {
        'arzopa': {
            login: 'REGISTRO_INICIO/login_cliente_arzopa.HTML',
            admin: 'administracion/admin_arzopa.html',
            publica: 'intermedio/acceso_arzopa.html',
            user: 'admin@arzopa.com',
            pass: '1234'
        },
        'lemuspool': {
            login: 'REGISTRO_INICIO/login_cliente_lemuspool.html',
            admin: 'administracion/admin_lemuspool.html',
            publica: 'intermedio/acceso_lemuspool.html',
            user: 'admin@lemuspool.com',
            pass: '1234'
        },
        'family': {
            login: 'REGISTRO_INICIO/login_cliente_family.html',
            admin: 'administracion/admin_family.html',
            publica: 'intermedio/acceso_family.html',
            user: 'admin@family.com',
            pass: '1234'
        },
        'tropical': {
            login: 'REGISTRO_INICIO/login_cliente_tropical.html',
            admin: 'administracion/admin_tropical.html',
            publica: 'intermedio/acceso_tropical.html',
            user: 'admin@tropical.com',
            pass: '1234'
        },
        'retro': {
            login: 'REGISTRO_INICIO/login_cliente_retro.html',
            admin: 'administracion/admin_retro.html',
            publica: 'intermedio/acceso_retro.html',
            user: 'admin@retro.com',
            pass: '1234'
        },
        'nature': {
            login: 'REGISTRO_INICIO/login_cliente_natural.html',
            admin: 'administracion/admin_nature.html',
            publica: 'intermedio/acceso_nature.html',
            user: 'admin@nature.com',
            pass: '1234'
        },
        'luxury': {
            login: 'REGISTRO_INICIO/login_cliente_luxury.html',
            admin: 'administracion/admin_luxury.html',
            publica: 'intermedio/acceso_luxury.html',
            user: 'admin@luxury.com',
            pass: '1234'
        },
        'minimalist': {
            login: 'REGISTRO_INICIO/login_cliente_minimalist.html',
            admin: 'administracion/admin_minimalist.html',
            publica: 'intermedio/acceso_minimalist.html',
            user: 'admin@minimalist.com',
            pass: '1234'
        },
        'principal': {
            login: 'REGISTRO_INICIO/Principal_login.html',
            admin: 'administracion/admin_principal.html',
            publica: 'pagina_principal.html',
            user: 'admin@aqualink.com',
            pass: '1234'
        }
    },

    // CONFIG: Permite controlar cómo se resuelven las URLs desde fuera
    // - Si `basePath` es null se detecta automáticamente desde `window.location.pathname`
    // - Si `forceAbsolute` es true, `resolveUrl` devolverá `origin + base + relPath`
    config: {
        basePath: null,
        forceAbsolute: true
    },

    /**
     * setBasePath(path)
     * Permite forzar una base personalizada (ej: '/vendedor/')
     */
    setBasePath: function(path) {
        this.config.basePath = path;
    },

    /**
     * setForceAbsolute(flag)
     * Si se pone `false`, `resolveUrl` devolverá la ruta relativa tal cual.
     */
    setForceAbsolute: function(flag) {
        this.config.forceAbsolute = !!flag;
    },

    /**
     * getBasePath()
     * Determina la base usada para construir URLs bajo `/vendedor/`.
     */
    getBasePath: function() {
        if (this.config.basePath) {
            let base = this.config.basePath;
            return base.endsWith('/') ? base : base + '/';
        }
        try {
            const pathname = window.location.pathname || '';
            const idx = pathname.indexOf('/vendedor/');
            return idx !== -1 ? pathname.slice(0, idx) + '/vendedor/' : '/vendedor/';
        } catch (e) {
            return '/vendedor/';
        }
    },

    /**
     * resolveUrl(relPath)
     * Construye una URL (relativa o absoluta) a partir de una ruta relativa bajo la base.
     */
    resolveUrl: function(relPath) {
        if (!relPath) return this.getBasePath();
        // normalizar barras
        relPath = relPath.replace(/^\/+/, '');
        const base = this.getBasePath();
        if (this.config.forceAbsolute) {
            try {
                return window.location.origin + (base.startsWith('/') ? base : '/' + base) + relPath;
            } catch (e) {
                return base + relPath;
            }
        }
        return base + relPath;
    },

    /**
     * openDemo(templateFile, name, site)
     * Abre la página intermedia de acceso (`intermedio/acceso_{site}.html`) en una nueva pestaña.
     * Si no se puede resolver, hace fallback a la propia plantilla.
     */
    openDemo: function(templateFile, name, site) {
        try {
            site = site || '';
            // intentar inferir site desde el nombre del archivo o del nombre legible
            if (!site) {
                const f = (templateFile || '').toLowerCase();
                if (f.indexOf('arzopa') !== -1) site = 'arzopa';
                else if (f.indexOf('lemus') !== -1) site = 'lemuspool';
                else if (f.indexOf('family') !== -1) site = 'family';
                else if (f.indexOf('tropical') !== -1) site = 'tropical';
                else if (f.indexOf('retro') !== -1) site = 'retro';
                else if (f.indexOf('nature') !== -1 || f.indexOf('natural') !== -1) site = 'nature';
                else if (f.indexOf('luxury') !== -1) site = 'luxury';
                else if (f.indexOf('minimalist') !== -1) site = 'minimalist';
                else if ((name || '').toLowerCase().indexOf('lemus') !== -1) site = 'lemuspool';
            }

            // si existe ruta conocida, usar la página intermedia definida en rutas[site].publica
            if (site && this.rutas[site] && this.rutas[site].publica) {
                const url = this.resolveUrl(this.rutas[site].publica);
                window.open(url, '_blank');
                return true;
            }

            // fallback a intermedio/acceso_<site>.html
            if (site) {
                const fallback = this.resolveUrl('intermedio/acceso_' + site + '.html');
                window.open(fallback, '_blank');
                return true;
            }

            // último recurso: abrir la plantilla directamente
            const direct = this.resolveUrl(templateFile || '');
            window.open(direct, '_blank');
            return true;
        } catch (e) {
            try { window.open('/vendedor/' + (templateFile || ''), '_blank'); } catch (er) {}
            return false;
        }
    },

    /**
     * navigateTo(site, type, target)
     * type: 'publica' | 'admin' | 'login'
     * target: '_self' or '_blank'
     */
    navigateTo: function(site, type, target) {
        try {
            site = (site || '').toString();
            type = (type || 'publica').toString();
            target = (target === '_blank') ? '_blank' : '_self';

            if (!site) return false;

            // Buscar en el mapa de rutas
            if (this.rutas[site] && this.rutas[site][type]) {
                const rel = this.rutas[site][type];
                const url = this.resolveUrl(rel);
                if (target === '_blank') window.open(url, '_blank'); else window.location.href = url;
                return true;
            }

            // fallback a intermedio/acceso_<site>.html si piden publica
            if (type === 'publica') {
                const fallback = this.resolveUrl('intermedio/acceso_' + site + '.html');
                if (target === '_blank') window.open(fallback, '_blank'); else window.location.href = fallback;
                return true;
            }

            return false;
        } catch (e) {
            return false;
        }
    },

    /**
     * bindAccessButtons()
     * Busca botones con `data-site` y `data-type` y les asigna comportamiento.
     * - Si el elemento es un enlace (`a`), actualiza su `href` y `target`.
     * - Si no, asigna un listener `click` que llama a `navigateTo`.
     */
    bindAccessButtons: function() {
        try {
            const nodes = document.querySelectorAll('[data-site][data-type]');
            if (!nodes || nodes.length === 0) return;

            nodes.forEach(el => {
                const site = el.getAttribute('data-site');
                const type = el.getAttribute('data-type');
                // Optional explicit target path (overrides `site`+`type`).
                // Puede ser absoluto ('/vendedor/...') o relativo ('intermedio/acceso_arzopa.html').
                // Si no tiene slash inicial se resolverá con `resolveUrl`.
                const explicitHref = el.getAttribute('data-href');
                const openInNew = el.getAttribute('target') === '_blank' || el.classList.contains('btn-admin');
                const target = openInNew ? '_blank' : '_self';

                // Si es un enlace <a>, actualizar href
                if (el.tagName && el.tagName.toLowerCase() === 'a') {
                    // preferir rutas del mapa
                    try {
                        if (explicitHref) {
                            // if explicitHref looks absolute (starts with http or /) use as-is, else resolve
                            if (/^(https?:)?\//i.test(explicitHref)) el.href = explicitHref;
                            else el.href = this.resolveUrl(explicitHref);
                        } else if (this.rutas[site] && this.rutas[site][type]) {
                            el.href = this.resolveUrl(this.rutas[site][type]);
                        } else if (type === 'publica') {
                            el.href = this.resolveUrl('intermedio/acceso_' + site + '.html');
                        } else {
                            // dejar # si no se puede resolver
                            el.href = '#';
                        }
                    } catch (e) {
                        // fallback seguro
                        try {
                            if (explicitHref) el.href = explicitHref; else el.href = this.resolveUrl('intermedio/acceso_' + site + '.html');
                        } catch (er) { el.href = '#'; }
                    }
                    el.target = target;
                } else {
                    // no es enlace: asignar click
                    el.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        try {
                            if (explicitHref) {
                                const toOpen = (/^(https?:)?\//i.test(explicitHref)) ? explicitHref : this.resolveUrl(explicitHref);
                                if (target === '_blank') window.open(toOpen, '_blank'); else window.location.href = toOpen;
                                return;
                            }
                            this.navigateTo(site, type, target);
                        } catch (err) {
                            console.warn('navigateTo failed, fallback to intermedio', err);
                            try {
                                const fallback = this.resolveUrl('intermedio/acceso_' + site + '.html');
                                if (target === '_blank') window.open(fallback, '_blank'); else window.location.href = fallback;
                            } catch (er) {
                                // último recurso: abrir base
                                try { window.location.href = this.getBasePath(); } catch (ee) {}
                            }
                        }
                    });
                }
            });
        } catch (e) {
            console.warn('bindAccessButtons failed', e);
        }
    }
};

// Auto-bind en páginas que carguen este script
if (typeof window !== 'undefined') {
    window.Enrutador = Enrutador;
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Sólo bindear si existen elementos con data-site/data-type
            if (document.querySelector('[data-site][data-type]')) {
                Enrutador.bindAccessButtons();
            }
        } catch (e) {}
    });
}
