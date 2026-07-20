<div align="center">

<img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
<img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white"/>
<img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
<img src="https://img.shields.io/badge/Status-Production-22c55e?style=for-the-badge"/>

<br/><br/>

# 🎛️ Tu Código — Panel de Gestión SaaS

### Sistema multi-tenant de gestión de suscripciones de streaming

*Administra franquicias, revendedores, clientes, plataformas y correos desde un solo panel centralizado.*

---

</div>

## 📋 Tabla de Contenidos

- [Descripción General](#-descripción-general)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Roles y Permisos](#-roles-y-permisos)
- [Módulos del Sistema](#-módulos-del-sistema)
- [Requisitos Técnicos](#-requisitos-técnicos)
- [Instalación](#-instalación)
- [Configuración del .env](#-configuración-del-env)
- [Seguridad](#-seguridad)
- [Historial de Versiones](#-historial-de-versiones)

---

## 🌐 Descripción General

**Tu Código** es una plataforma SaaS (Software as a Service) multi-tenant diseñada para la gestión y distribución de suscripciones de plataformas de streaming. Permite a operadores del negocio administrar una red jerárquica de franquicias, revendedores y clientes finales, todo desde un panel centralizado con aislamiento total de datos entre inquilinos.

```
┌─────────────────────────────────────────────┐
│           tu-codigo.com (VPS)               │
│                                             │
│  Panel Admin ──► API Pública ──► Clientes   │
│  (Laravel)       (Consultas)    (Portal)    │
└─────────────────────────────────────────────┘
```

---

## 🏗️ Arquitectura del Sistema

```
tu-codigo_super_admin/
│
├── 📁 app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Login inteligente (Admin + Cliente)
│   │   │   └── Admin/
│   │   │       ├── UserController.php       # Gestión de Franquicias/Revendedores
│   │   │       ├── ClientController.php     # Gestión de Clientes Finales
│   │   │       ├── PlatformController.php   # Gestión de Plataformas
│   │   │       ├── EmailAccountController.php # Cuentas IMAP
│   │   │       ├── AllowedEmailController.php # Perfiles/Correos de Plataformas
│   │   │       ├── WarrantyController.php   # Sistema de Garantías
│   │   │       ├── SettingsController.php   # Configuración Global
│   │   │       └── IpBanController.php      # Sistema Anti-Spam
│   │   └── Middleware/
│   │       ├── CheckIpBan.php              # Bloqueo de IPs abusivas
│   │       ├── CheckInstallation.php       # Verificación de instalación
│   │       ├── CheckUserRole.php           # Control de acceso por rol
│   │       └── CheckSuperAdmin.php         # Rutas exclusivas del Super Admin
│   │
│   ├── Livewire/Admin/
│   │   ├── PlatformList.php                # Listado reactivo de plataformas
│   │   ├── EmailAccountList.php            # Listado de cuentas IMAP
│   │   └── ...                            # Otros componentes Livewire
│   │
│   └── Models/
│       ├── User.php                        # Super Admin / Admin / Revendedor
│       ├── Client.php                      # Cliente Final
│       ├── Platform.php                    # Plataforma de streaming
│       ├── AllowedEmail.php                # Perfil/correo vendible
│       ├── EmailAccount.php                # Buzón IMAP maestro
│       └── ...
│
├── 📁 resources/views/
│   ├── auth/                              # Login, 2FA
│   ├── admin/                             # Panel administrativo
│   └── client/                            # Portal del cliente
│
└── 📁 database/migrations/                # Migraciones de BD
```

---

## 👥 Roles y Permisos

El sistema maneja **4 niveles jerárquicos** con aislamiento estricto de datos:

```
                    ┌─────────────────┐
                    │   SUPER ADMIN   │  ← Dueño del sistema (ID=1)
                    │  (Tú / Dueño)   │    Ve y gestiona TODO
                    └────────┬────────┘
                             │ crea
              ┌──────────────┴──────────────┐
              │                             │
     ┌────────▼────────┐           ┌────────▼────────┐
     │  ADMINISTRADOR  │           │  ADMINISTRADOR  │
     │  (Franquicia A) │           │  (Franquicia B) │
     │  role = admin   │           │  role = admin   │
     └────────┬────────┘           └────────┬────────┘
              │ crea                        │ crea
     ┌────────▼────────┐           ┌────────▼────────┐
     │   REVENDEDOR    │           │   REVENDEDOR    │
     │  role = user    │           │  role = user    │
     └────────┬────────┘           └────────┬────────┘
              │ crea                        │ crea
     ┌────────▼────────┐           ┌────────▼────────┐
     │    CLIENTE      │           │    CLIENTE      │
     │  (tabla propia) │           │  (tabla propia) │
     └─────────────────┘           └─────────────────┘
```

| Rol | Tabla | Puede crear | Ve datos de otros |
|-----|-------|-------------|-------------------|
| Super Admin | `users` (id=1) | Todo | ✅ (global) |
| Admin/Franquicia | `users` (role=admin) | Revendedores + Clientes | ❌ (solo los suyos) |
| Revendedor | `users` (role=user) | Clientes | ❌ (solo los suyos) |
| Cliente Final | `clients` | Nada | ❌ (su portal) |

> 🔒 **Aislamiento estricto:** Cada inquilino ve y gestiona ÚNICAMENTE lo que él mismo creó. Ni el Super Admin puede ver plataformas o correos creados por otros (por diseño de negocio).

---

## 🧩 Módulos del Sistema

### 🔐 Login Inteligente
- Detecta automáticamente si el usuario es **Cliente** o **Admin/Franquicia/Revendedor**
- Busca en ambas tablas (`clients` y `users`) de forma transparente
- Normalización de email a minúsculas para compatibilidad con teclados móviles
- Soporte para autenticación de dos factores (2FA)

### 🏢 Gestión de Usuarios (Franquicias y Revendedores)
- Creación, edición y desactivación de cuentas
- Sistema jerárquico con `parent_id` para saber quién creó a quién
- Control de suscripciones con fecha de vencimiento y días de gracia
- Asignación de planes de franquicia con límites configurables

### 👤 Gestión de Clientes Finales
- Portal de acceso exclusivo para clientes (`/client/dashboard`)
- Control de consultas diarias por cliente (`max_queries_per_day`)
- Modos de acceso: `all` (todas las plataformas) o `selective` (solo las asignadas)
- Fechas de vencimiento por perfil asignado

### 📺 Gestión de Plataformas
- CRUD completo con logo personalizado por plataforma
- Imágenes guardadas en `public/platforms_logos` (compatible con CPanel sin symlinks)
- Asuntos/perfiles configurables por plataforma
- Aislamiento total: cada quien ve solo sus plataformas

### 📧 Cuentas de Correo IMAP
- Conexión real a buzones IMAP para leer códigos de verificación
- Contraseñas cifradas con `Crypt::encryptString` (AES-256)
- Test de conexión en tiempo real desde el panel
- Asignación de cuentas a múltiples usuarios

### 🔑 Perfiles / Correos Permitidos
- Registro de correos/perfiles vendibles por plataforma
- Estados dinámicos: **Libre**, **Ocupado**, **Vencido**
- Carga masiva de perfiles (Mass Upload)
- Sistema de vencimientos individuales por asignación cliente-perfil

### 🛡️ Sistema de Garantías
- Clientes pueden reportar incidencias desde su portal
- Admins procesan y resuelven garantías
- Estados: Pendiente, Aprobada, Rechazada, Resuelta

### 🚫 Sistema Anti-Spam (IP Ban)
- Detecta más de 2 solicitudes en 5 segundos desde la misma IP
- Baneo automático por 1 hora con registro en BD
- Administración manual de IPs baneadas desde el panel

---

## 💻 Requisitos Técnicos

| Componente | Versión Mínima |
|-----------|---------------|
| PHP | **8.5.0** |
| Laravel | 12.x |
| Livewire | 3.x |
| MySQL / MariaDB | 8.0+ |
| Composer | 2.x |
| Node.js | 18+ |
| Servidor | Apache / Nginx |

---

## 🚀 Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/streamvzla/webBot.git
cd webBot

# 2. Instalar dependencias PHP
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. Instalar dependencias JS
npm install && npm run build

# 4. Copiar y configurar el archivo de entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar la base de datos en .env y ejecutar migraciones
php artisan migrate --seed

# 6. Crear enlace simbólico de almacenamiento
php artisan storage:link

# 7. Ajustar permisos (Linux/VPS)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ⚙️ Configuración del .env

```env
APP_NAME="Tu Código"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_DOMAIN=.tu-dominio.com

MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-servidor.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=correo@tu-dominio.com
MAIL_PASSWORD=tu_contraseña_smtp

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🔐 Seguridad

El sistema implementa múltiples capas de seguridad:

- ✅ **Autenticación dual** — Tablas separadas para admins y clientes
- ✅ **Hashing de contraseñas** — Bcrypt automático via Laravel
- ✅ **Cifrado AES-256** — Contraseñas IMAP cifradas con `Crypt`
- ✅ **2FA** — Autenticación de dos factores opcional
- ✅ **CSRF Protection** — Tokens en todos los formularios
- ✅ **IDOR Protection** — Verificación de propiedad en cada acción
- ✅ **Email normalizado** — Login case-insensitive (compatible con móviles)
- ✅ **IP Ban** — Sistema anti-spam automático
- ✅ **Multi-Tenancy** — Aislamiento estricto de datos por usuario
- ✅ **Middleware por rol** — Acceso granular por nivel jerárquico

---

## 👁️ El Centinela IMAP

El **Centinela** es un proceso que corre 24/7 en el servidor, escuchando los buzones IMAP configurados y procesando los correos entrantes (códigos de verificación) en tiempo real.

### ¿Qué hace?
```
[Plataforma de Streaming] ──► [Correo IMAP] ──► [Centinela] ──► [Panel] ──► [Cliente]
       Envía código               Llega al         Lee y         Guarda       Puede
       por email                  buzón            procesa       en BD        consultarlo
```

### 🚀 Guía de Rescate del Servidor

Si el VPS se reinicia o el Centinela se apaga, sigue estos pasos en orden desde la consola SSH:

#### Paso 1 — Entrar a la Sala de Control
```bash
cd /var/www/mi-panel
```

#### Paso 2 — Despertar Sail, PHP y la Base de Datos
```bash
./vendor/bin/sail up -d
```
> La bandera `-d` significa **Detached** (segundo plano). Devuelve el control de la consola inmediatamente mientras todo arranca. Si configuraste el alias global de Sail, también puedes usar `sail up -d`.

#### Paso 3 — Asegurar Permisos (Las Puertas)
```bash
docker exec -u root mi-panel-laravel.test-1 chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
```
> Desbloquea los directorios vitales para que PHP pueda escribir sin errores tras un reinicio.

#### Paso 4 — Aplicar Migraciones (Solo si subiste archivos nuevos)
```bash
docker exec -u root mi-panel-laravel.test-1 php artisan migrate
```
> ⚠️ Solo ejecuta este paso si subiste actualizaciones que incluyan cambios en la base de datos. Si no subiste nada, omítelo.

#### Paso 5 — Despertar al Centinela (Modo Fantasma) 👻
```bash
nohup docker exec -u sail mi-panel-laravel.test-1 php artisan imap:sentinel > centinela.log 2>&1 &
```
> `nohup` asegura que el Centinela **NO muera** al cerrar la consola SSH. Corre en las sombras indefinidamente procesando correos entrantes.

---

### ⚡ Comandos Extra — Modo Dios

| Comando | Descripción |
|---------|-------------|
| `tail -f centinela.log` | Ver en tiempo real lo que el Centinela está procesando *(Ctrl+C para salir sin apagarlo)* |
| `./vendor/bin/sail restart` | Reiniciar solo PHP si hay lentitud o error de caché, sin apagar todo |
| `pkill -f "imap:sentinel"` | Matar el Centinela si se comporta mal |
| `./vendor/bin/sail stop` | Apagar completamente todo el servidor |
| `./vendor/bin/sail ps` | Ver el estado de todos los contenedores activos |

> 💡 **Pro Tip:** Guarda esta guía en tus marcadores del navegador o en el escritorio del servidor. No necesitas internet para consultarla.

---

## 📝 Historial de Versiones


### v2.0 — Julio 2026
- 🆕 Sistema multi-tenant completo con aislamiento estricto
- 🆕 Aislamiento hermético: Franquicias y Revendedores solo ven sus propios Clientes
- 🆕 Login inteligente con detección automática de rol
- 🆕 Fix: Email case-insensitive en login (compatibilidad móviles)
- 🆕 Sistema de garantías (congelamiento de tiempo automático)
- 🆕 Fix Garantías: Aislamiento estricto de tickets y acceso desbloqueado para Súper Admin
- 🆕 Fix Consultas (Queries): Aislamiento hermético y parche de seguridad (prevención de borrado masivo)
- 🆕 Fix Seguridad (Crítico): Rutas de Planes de Franquicia bloqueadas exclusivamente para el Súper Admin
- 🆕 Carga masiva de perfiles (Mass Upload)
- 🆕 Sistema Anti-Spam con IP Ban automático
- 🆕 Autenticación 2FA
- 🆕 Portal exclusivo para clientes finales
- 🆕 Planes de franquicia configurables

### v1.0 — Versión inicial
- Panel básico de gestión
- CRUD de plataformas y clientes

---

<div align="center">

**Desarrollado con ❤️ para la gestión profesional de servicios de streaming**

[![GitHub](https://img.shields.io/badge/GitHub-streamvzla-181717?style=for-the-badge&logo=github)](https://github.com/streamvzla/webBot)

</div>
