# 🌊 Módulo Vendedor - AquaLink Devs Platform

Sistema completo de desarrollo y gestión web para piscinas y negocios relacionados. Este módulo proporciona una plataforma integral con múltiples plantillas temáticas, panel de administración y sistema de autenticación.

---

## 📋 Tabla de Contenidos

- [Descripción General](#-descripción-general)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Plantillas Disponibles](#-plantillas-disponibles)
- [Características Principales](#-características-principales)
- [Tecnologías Utilizadas](#-tecnologías-utilizadas)
- [Base de Datos](#-base-de-datos)
- [Sistema de Autenticación](#-sistema-de-autenticación)
- [Configuración](#-configuración)
- [Uso](#-uso)

---

## 🎯 Descripción General

**AquaLink Devs** es una plataforma profesional de desarrollo web especializada en sitios para piscinas, resorts y negocios acuáticos. El módulo vendedor incluye:

- 🎨 **8 plantillas temáticas** completamente funcionales
- 🔐 **Sistema de autenticación** multi-usuario
- 📊 **Paneles de administración** personalizados
- 💬 **Gestión de mensajes y reservas**
- 💰 **Control de transacciones** (ingresos/gastos)
- 📅 **Calendario de reservas** interactivo
- 📧 **Formularios de contacto** con envío de email

---

## 📁 Estructura del Proyecto

```
vendedor/
│
├── 📄 pagina_principal.html          # Página principal (28KB) - Catálogo de plantillas
├── 📄 cuentas_new.txt                # Base de datos de cuentas de usuario
│
├── 📂 Direccion/                     # Sistema de enrutamiento
│   └── controlador.js                # Controlador de rutas y navegación entre plantillas
│
├── 📂 REGISTRO_INICIO/               # Sistema de autenticación
│   ├── Principal_login.html          # Login principal de AquaLink
│   ├── login_cliente_arzopa.HTML     # Login Arzopa Aqua
│   ├── login_cliente_lemuspool.html  # Login LemusPool
│   ├── login_cliente_family.html     # Login Family
│   ├── login_cliente_luxury.html     # Login Luxury
│   ├── login_cliente_natural.html    # Login Nature
│   ├── login_cliente_retro.html      # Login Retro
│   ├── login_cliente_tropical.html   # Login Tropical
│   └── login_cliente_minimalist.html # Login Minimalist
│
├── 📂 administracion/                # Paneles de administración
│   ├── admin_principal.html          # Panel admin principal (113 líneas)
│   ├── admin_arzopa.html             # Panel Arzopa (51 líneas)
│   ├── admin_lemuspool.html          # Panel LemusPool (42 líneas)
│   ├── admin_family.html             # Panel Family (48 líneas)
│   ├── admin_luxury.html             # Panel Luxury (69 líneas)
│   ├── admin_nature.html             # Panel Nature (94 líneas)
│   ├── admin_retro.html              # Panel Retro (96 líneas)
│   ├── admin_tropical.html           # Panel Tropical (90 líneas)
│   └── admin_minimalist.html         # Panel Minimalist (66 líneas)
│
├── 📂 intermedio/                    # Páginas de selección de demo
│   ├── acceso_arzopa.html
│   ├── acceso_lemuspool.html
│   ├── acceso_family.html
│   ├── acceso_luxury.html
│   ├── acceso_nature.html
│   ├── acceso_retro.html
│   ├── acceso_tropical.html
│   └── acceso_minimalist.html
│
├── 📂 css/                           # Hojas de estilo
│   ├── principal_page.css            # Estilos página principal (121 líneas)
│   ├── admin.css                     # Estilos admin generales (114 líneas)
│   ├── admin_principal.css           # Estilos admin principal (115 líneas)
│   ├── family.css                    # Estilos plantilla Family (47 líneas)
│   ├── Page_P.css                    # Estilos página pública (117 líneas)
│   └── [otros estilos por plantilla]
│
├── 📂 js/                            # Scripts JavaScript
│   ├── prinsipal_page.js             # JS página principal (28 líneas)
│   ├── admin_principal.js            # JS admin principal (47 líneas)
│   └── [otros scripts]
│
├── 📂 php/                           # Backend PHP
│   ├── db.php                        # Conexión a base de datos MySQL (24 líneas)
│   ├── handle_login.php              # Autenticación de usuarios (75 líneas)
│   ├── send_mail.php                 # Envío de formularios de contacto (58 líneas)
│   ├── save_submission.php           # Guardar envíos de formularios (31 líneas)
│   ├── save_templates.php            # Guardar/actualizar plantillas (62 líneas)
│   └── schema.sql                    # Esquema de base de datos (86 líneas)
│
├── 📂 img/                           # Imágenes del sistema
├── 📂 Image/                         # Recursos de imágenes adicionales
│
└── 📂 Plantillas HTML/               # 7 plantillas temáticas
    ├── templateLemuspool.html        # Plantilla LemusPool (19KB)
    ├── template_arzopa_aqua.html     # Plantilla Arzopa (14KB)
    ├── template_family.html          # Plantilla Family (26KB)
    ├── template_luxury.html          # Plantilla Luxury (13KB)
    ├── template_minimalist.html      # Plantilla Minimalist (8KB)
    ├── template_nature.html          # Plantilla Nature (26KB)
    ├── template_retro.html           # Plantilla Retro (17KB)
    └── template_tropical.html        # Plantilla Tropical (18KB)
```

---

## 🎨 Plantillas Disponibles

### 1. **LemusPool** - Elegante y Completo
- **Descripción**: Diseño elegante ideal para mostrar instalaciones, servicios y reservas online
- **Características**: Galería de imágenes, calendario integrado, formularios de contacto
- **Login**: `admin@lemuspool.com` / `1234`
- **Archivo**: `templateLemuspool.html` (19,315 bytes)

### 2. **Arzopa Aqua** - Vibrante y Moderno
- **Descripción**: Diseño inspirado en la naturaleza con secciones alternadas
- **Características**: Gran impacto visual, diseño responsive
- **Login**: `admin@arzopa.com` / `1234` o `adminAqua@correolink.es` / `123456`
- **Archivo**: `template_arzopa_aqua.html` (14,793 bytes)

### 3. **Minimalist** - Limpio y Moderno
- **Descripción**: Diseño limpio con mucho espacio en blanco
- **Características**: Enfoque en tipografía y simplicidad
- **Login**: `admin@minimalist.com` / `1234`
- **Archivo**: `template_minimalist.html` (8,527 bytes)

### 4. **Tropical** - Vibrante y Divertido
- **Descripción**: Colores vivos y diseño orgánico redondeado
- **Características**: Ambiente de paraíso y diversión
- **Login**: `admin@tropical.com` / `1234`
- **Archivo**: `template_tropical.html` (18,164 bytes)

### 5. **Luxury** - Exclusividad y Confort
- **Descripción**: Tema oscuro y sofisticado con detalles dorados
- **Características**: Máxima sensación de exclusividad
- **Login**: `admin@luxury.com` / `1234`
- **Archivo**: `template_luxury.html` (13,218 bytes)

### 6. **Family** - Alegre y Familiar
- **Descripción**: Diseño alegre con colores primarios e íconos
- **Características**: Atractivo para padres y niños
- **Login**: `admin@family.com` / `1234`
- **Archivo**: `template_family.html` (26,804 bytes)

### 7. **Retro** - Nostálgico y Único
- **Descripción**: Diseño nostálgico con tipografía pixelada
- **Características**: Colores vibrantes que evocan los años 80
- **Login**: `admin@retro.com` / `1234`
- **Archivo**: `template_retro.html` (17,120 bytes)

### 8. **Nature** - Natural y Orgánico
- **Descripción**: Diseño inspirado en la naturaleza
- **Características**: Colores tierra y elementos orgánicos
- **Login**: `admin@nature.com` / `1234`
- **Archivo**: `template_nature.html` (26,837 bytes)

---

## ✨ Características Principales

### 🏠 Página Principal (`pagina_principal.html`)
- **Navbar fijo** con navegación suave
- **Hero section** con gradiente profesional
- **Sección de servicios** (3 tarjetas):
  - Optimización SEO
  - Diseños personalizados
  - Soluciones empresariales
- **Planes de precios**:
  - **Plan Estándar**: $20,000/año (1 página)
  - **Plan Emprendimiento**: $80,000/año (hasta 5 páginas)
  - **Plan Empresa**: $500,000/año (páginas ilimitadas)
- **Catálogo de plantillas** con preview
- **Formulario de contacto** integrado (envía a `php/send_mail.php`)

### 🔐 Sistema de Autenticación
- **Multi-usuario** con diferentes roles
- **Login personalizado** por plantilla
- **Recuperación de contraseña**
- **Sesiones** con localStorage y PHP
- **Validación** de credenciales en frontend y backend
- **Redireccionamiento** automático al panel correspondiente

### 📊 Panel de Administración
Cada plantilla incluye su propio panel con:
- **Dashboard** con estadísticas
- **Calendario de reservas** interactivo con disponibilidad
- **Gestión de mensajes** con marcado de leídos/no leídos
- **Control de transacciones** (ingresos/gastos)
- **Gestión de galería** de imágenes
- **Editor de contenido** dinámico
- **Configuración** de plantilla

### 🗓️ Sistema de Reservas
- **Calendario interactivo** con grid de 7 columnas
- **Días disponibles/ocupados** con código de colores
- **Click para marcar** disponibilidad
- **Contador de reservas** por día
- **Guardar/resetear** cambios

### 💬 Gestión de Mensajes
- **Bandeja de entrada** con nuevos mensajes destacados
- **Filtro** de leídos/no leídos
- **Respuesta** a clientes
- **Archivo** de mensajes antiguos

### 💰 Control Financiero
- **Registro de ingresos** y gastos
- **Categorización** de transacciones
- **Gráficas** de estadísticas (próximamente)
- **Exportación** de datos

---

## 🛠️ Tecnologías Utilizadas

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos avanzados con gradientes y animaciones
  - Variables CSS (`:root`)
  - Flexbox y CSS Grid
  - Media queries responsive
- **JavaScript (ES6+)**:
  - DOM manipulation
  - LocalStorage API
  - Fetch API
  - Event handling
- **Bootstrap 5.3.2** - Framework CSS

### Backend
- **PHP 8.x**:
  - MySQLi para base de datos
  - Sesiones
  - Validación de formularios
  - Prepared statements (seguridad)
- **MySQL / MariaDB**:
  - Base de datos relacional
  - 8 tablas principales

### Herramientas
- **XAMPP** - Entorno de desarrollo local
- **Font Awesome** - Iconos
- **Google Fonts** - Tipografías personalizadas
  - Inter
  - Poppins
  - Montserrat
  - Playfair Display
  - Nunito

---

## 🗄️ Base de Datos

### Configuración (`php/db.php`)
```php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'AquaLink_db';
```

### Esquema de Base de Datos (`php/schema.sql`)

#### Tabla: `usuarios`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- username (VARCHAR(255), UNIQUE)
- password (VARCHAR(255))
- template_id (VARCHAR(50))
- is_admin (BOOLEAN, DEFAULT FALSE)
```

#### Tabla: `plantillas`
```sql
- id (VARCHAR(50), PRIMARY KEY)
- nombre (VARCHAR(255))
- img_url (VARCHAR(255))
```

#### Tabla: `solicitudes`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- nombre (VARCHAR(255))
- email (VARCHAR(255))
- plantilla_interes (VARCHAR(50))
- mensaje (TEXT)
- fecha (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- leido (BOOLEAN, DEFAULT FALSE)
```

#### Tabla: `reservas`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- template_id (VARCHAR(50), FOREIGN KEY)
- nombre_cliente (VARCHAR(255))
- fecha_reserva (DATE)
- personas (INT)
- estado (VARCHAR(50), DEFAULT 'Pendiente')
```

#### Tabla: `mensajes`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- template_id (VARCHAR(50), FOREIGN KEY)
- nombre (VARCHAR(255))
- email (VARCHAR(255))
- telefono (VARCHAR(50))
- mensaje (TEXT)
- fecha (TIMESTAMP)
- leido (BOOLEAN, DEFAULT FALSE)
```

#### Tabla: `transacciones`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- template_id (VARCHAR(50), FOREIGN KEY)
- tipo (ENUM: 'ingreso', 'gasto')
- descripcion (VARCHAR(255))
- monto (DECIMAL(10,2))
- categoria (VARCHAR(100))
- fecha (TIMESTAMP)
```

#### Tabla: `form_submissions`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- page (VARCHAR(255))
- data (JSON)
- ip (VARCHAR(50))
- fecha (TIMESTAMP)
```

#### Tabla: `login_attempts`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- email (VARCHAR(255))
- ip (VARCHAR(50))
- user_agent (TEXT)
- success (BOOLEAN)
- payload (JSON)
- fecha (TIMESTAMP)
```

---

## 🔐 Sistema de Autenticación

### Credenciales de Usuario

| Usuario | Contraseña | Plantilla | Admin |
|---------|------------|-----------|-------|
| `admin@aqualink.com` | `123456` | principal | ✅ |
| `admin@lemuspool.com` | `1234` | lemuspool | ❌ |
| `admin@arzopa.com` | `1234` | arzopa | ❌ |
| `adminAqua@correolink.es` | `123456` | arzopa | ✅ |
| `admin@family.com` | `1234` | family | ❌ |
| `admin@luxury.com` | `1234` | luxury | ❌ |
| `admin@nature.com` | `1234` | nature | ❌ |
| `admin@retro.com` | `1234` | retro | ❌ |
| `admin@tropical.com` | `1234` | tropical | ❌ |
| `admin@minimalist.com` | `1234` | minimalist | ❌ |

### Flujo de Autenticación

1. **Usuario ingresa credenciales** en formulario de login
2. **JavaScript valida** el formulario (`signInForm`)
3. **POST a `php/handle_login.php`** con email y password
4. **Backend verifica** contra tabla `usuarios`:
   - Soporta bcrypt hash y plaintext
   - Registra intento en `login_attempts`
5. **Si éxito**:
   - Crea sesión PHP (`$_SESSION`)
   - Devuelve JSON con `redirect` al admin
   - Frontend redirige automáticamente
6. **Si falla**:
   - Muestra error en `#signin-error`
   - Registra intento fallido

### Sistema de Enrutamiento (`Direccion/controlador.js`)

```javascript
const Enrutador = {
    rutas: {
        'arzopa': {
            login: 'REGISTRO_INICIO/login_cliente_arzopa.HTML',
            admin: 'administracion/admin_arzopa.html',
            publica: 'intermedio/acceso_arzopa.html',
            user: 'admin@arzopa.com',
            pass: '1234'
        },
        // ... más rutas
    }
}
```

**Funcionalidades**:
- Resolución automática de rutas
- Detección de base path
- Navegación entre login/admin/pública
- Soporte para múltiples credenciales por plantilla

---

## ⚙️ Configuración

### Requisitos Previos
- **XAMPP** (o similar con Apache + MySQL)
- **PHP 8.0+**
- **MySQL 5.7+** o **MariaDB 10.3+**
- Navegador moderno (Chrome, Firefox, Edge)

### Instalación

1. **Clonar/Descargar** el proyecto en `htdocs/vendedor/`

2. **Iniciar XAMPP**:
   - Apache
   - MySQL

3. **Crear base de datos**:
   ```bash
   # Acceder a phpMyAdmin: http://localhost/phpmyadmin
   # Importar: vendedor/php/schema.sql
   ```

4. **Configurar conexión** (si es necesario):
   ```php
   // Editar vendedor/php/db.php
   $DB_HOST = '127.0.0.1';
   $DB_USER = 'root';
   $DB_PASS = 'tu_password';
   $DB_NAME = 'AquaLink_db';
   ```

5. **Acceder a la aplicación**:
   ```
   http://localhost/vendedor/pagina_principal.html
   ```

---

## 🚀 Uso

### Flujo de Usuario Público

1. **Visitar página principal**: `http://localhost/vendedor/pagina_principal.html`
2. **Explorar plantillas** en la sección "Nuestras Plantillas"
3. **Click en "Ver Demo"** → Accede a página intermedia
4. **Seleccionar vista**:
   - **Página Principal**: Vista del sitio público
   - **Administración**: Panel de control (requiere login)
   - **Sesión**: Login del cliente

### Flujo de Administrador

1. **Acceder al login** (ej: `REGISTRO_INICIO/Principal_login.html`)
2. **Ingresar credenciales**:
   - Email: `admin@aqualink.com`
   - Password: `123456`
3. **Redirigido automáticamente** a `administracion/admin_principal.html`
4. **Gestionar**:
   - Reservas en el calendario
   - Mensajes de contacto
   - Plantillas y clientes
   - Configuración del sitio

### Formulario de Contacto

**Frontend** (`pagina_principal.html`):
```html
<form action="php/send_mail.php" method="POST">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <input type="tel" name="phone">
    <select name="template">
        <option value="LemusPool">Plantilla LemusPool</option>
        <!-- más opciones -->
    </select>
    <textarea name="message" required></textarea>
    <button type="submit">Enviar Solicitud</button>
</form>
```

**Backend** (`php/send_mail.php`):
- Inserta en tabla `solicitudes`
- Guarda JSON completo en `form_submissions`
- Redirige con parámetro `?sent=1` o `?sent=0`

---

## 📝 Notas Adicionales

### Seguridad
- ✅ **Prepared statements** en todas las consultas SQL
- ✅ **Validación** de entrada en frontend y backend
- ✅ **Sanitización** de datos con `trim()`
- ✅ **Registro de intentos** de login fallidos
- ⚠️ **Passwords en plaintext** (migrar a bcrypt en producción)
- ⚠️ **Sin HTTPS** (configurar SSL en producción)

### Responsive Design
- ✅ Mobile-first con media queries
- ✅ Navegación hamburger en móviles
- ✅ Grids adaptativos (7 columnas → stackeable)
- ✅ Imágenes responsive

### Extensibilidad
- Fácil agregar nuevas plantillas
- Sistema modular de componentes
- Estilos centralizados por plantilla
- API REST lista para ampliar

### Performance
- Carga asíncrona de scripts
- CSS/JS minificables
- Imágenes optimizables (WebP)
- LocalStorage para cache

---

## 📧 Contacto

**Desarrollado por**: AquaLink Devs  
**Email**: admin@aqualink.com  
**Sitio**: [AquaLink Devs](#)

---

## 📄 Licencia

© 2025 AquaLink Devs - Todos los derechos reservados.

---

## 🔄 Historial de Versiones

### v1.0.0 (2025)
- ✅ 8 plantillas temáticas completas
- ✅ Sistema de autenticación multi-usuario
- ✅ Panel de administración por plantilla
- ✅ Base de datos MySQL completa
- ✅ Formularios de contacto funcionales
- ✅ Sistema de reservas con calendario

---

**Nota**: Los resultados de búsqueda pueden estar limitados. Para ver más archivos, visita el [repositorio en GitHub](https://github.com/Stromjold/Piscina_LemusPool/tree/main/vendedor).
