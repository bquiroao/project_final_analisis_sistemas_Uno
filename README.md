# Proyecto base — Evaluación Final Análisis de Sistemas I

Proyecto **Laravel 12 + Vue 3 (Vite)** con **JWT**, **Spatie Laravel Permission** y **Stancl Tenancy** (tenant identificado por cabecera `X-Tenant-ID`). Esta base se entrega para que el estudiante analice la estructura existente y desarrolle el módulo asignado por el docente.

---

## Arquitectura construida

La aplicación sigue un modelo **SPA + API REST**: el navegador carga una única vista Blade que monta Vue; el backend expone JSON bajo `/api/v1`.

### Vista general

| Capa | Tecnología | Para qué sirve |
|------|------------|----------------|
| **Backend / API** | Laravel 12 | Punto único de negocio, persistencia, seguridad y contratos HTTP JSON. |
| **Autenticación API** | `tymon/jwt-auth` | Emite y valida tokens JWT en el guard `api`; no usa sesiones para el API. |
| **Autorización (RBAC)** | `spatie/laravel-permission` | Roles y permisos sobre el modelo `User` (guard `api`). |
| **Multitenancy base** | `stancl/tenancy` + tabla `tenants` | Modelo `Tenant` y columna `tenant_id` en usuarios. El tenant activo se **indica en cada petición** con `X-Tenant-ID` (sin bases de datos separadas en esta fase). |
| **Middleware propio** | `TenantMiddleware`, `JwtAuth` | `TenantMiddleware` resuelve y valida el tenant por cabecera; `JwtAuth` protege rutas con JWT y coherencia tenant–token. |
| **Frontend** | Vue 3 + Vue Router + Pinia | SPA: rutas del lado cliente, estado global (p. ej. sesión / token) y pantallas como login. |
| **Build frontend** | Vite 7 + `@vitejs/plugin-vue` | Empaqueta JS/CSS; alias `@` apunta a `resources/js`. |
| **Cliente HTTP** | Axios (`resources/js/plugins/axios.js`) | Llama al API con `Authorization: Bearer` y `X-Tenant-ID` según lo guardado en `localStorage`. |
| **Vista shell** | `resources/views/app.blade.php` | Inyecta el bundle Vite y el `<div id="app">` donde Vue se monta. |
| **Rutas web** | `routes/web.php` | Cualquier ruta devuelve la misma SPA (fallback) para que Vue Router maneje `/`, `/login`, etc. |

### Flujo típico de una petición

1. El usuario (o el formulario de login) fija el **ID del tenant**; Axios envía `X-Tenant-ID` y, si hay sesión, el **JWT** en `Authorization`.
2. Laravel aplica `TenantMiddleware` donde corresponda: si el tenant no existe, responde 404 JSON.
3. En rutas protegidas, `jwt.auth` valida el token; opcionalmente se compara el tenant del header con el del usuario del token.
4. Las respuestas del API son siempre **JSON**.

### Estructura relevante en el repo

```
app/Http/Controllers/Api/V1/AuthController.php   # registro, login, me, refresh, logout
app/Http/Middleware/TenantMiddleware.php         # cabecera X-Tenant-ID
app/Http/Middleware/JwtAuth.php                  # JWT + coherencia tenant
app/Models/User.php                              # JWT + HasRoles + tenant_id
app/Models/Tenant.php                            # modelo Stancl / tabla tenants
resources/js/                                    # Vue: router, stores, páginas, Axios
routes/api.php                                   # rutas bajo prefijo api/v1 (ver bootstrap/app.php)
```

---

## Qué se necesita para correr el proyecto

### Software instalado en tu máquina

| Requisito | Uso |
|-----------|-----|
| **PHP ≥ 8.2** | Ejecutar Laravel y Composer scripts (`artisan`, migraciones). |
| **Composer ≥ 2.x** | Instalar dependencias PHP (`vendor/`). |
| **Node.js ≥ 20** y **npm** | Instalar dependencias JS y ejecutar Vite (`npm run dev` / `npm run build`). |
| **Extensiones PHP habituales** | `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath` (según tu stack). |
| **Base de datos** | **SQLite** (rápido en desarrollo, archivo `database/database.sqlite`) o **MySQL 8** en entornos más cercanos a producción. |

### Variables de entorno imprescindibles

Tras copiar `.env.example` a `.env`:

- **`APP_KEY`** — `php artisan key:generate`
- **`JWT_SECRET`** — `php artisan jwt:secret`
- **Conexión a BD** — según elijas SQLite o MySQL en `.env`
- **`VITE_API_URL`** — URL base del API que usará el frontend en desarrollo (p. ej. `http://localhost:8000/api/v1`) si el navegador sirve la SPA desde otro puerto (Vite).

Sin PHP/Composer/Node o sin BD configurada, el proyecto no podrá migrar ni compilar el frontend.

---

## Instalación y ejecución

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Configura la base de datos en `.env` (SQLite o MySQL). Luego:

```bash
php artisan migrate
npm install
npm run dev
```

En **otra terminal**, el servidor HTTP de Laravel:

```bash
php artisan serve
```

Abre el frontend según la URL que muestre Vite (típicamente `http://localhost:5173`) y asegúrate de que `VITE_API_URL` apunte al backend (`php artisan serve` suele ser `http://127.0.0.1:8000`).

### Variables `.env` más usadas

| Variable | Descripción |
|----------|-------------|
| `APP_URL` | URL pública del backend (p. ej. `http://localhost:8000`). |
| `FRONTEND_URL` | URL del frontend en desarrollo (referencia / CORS si aplica). |
| `JWT_SECRET` | Secreto de firma JWT (generado con `jwt:secret`). |
| `JWT_TTL` | Minutos de vida del access token (por defecto 60). |
| `VITE_API_URL` | Base URL del API para Axios desde Vite. |

## API (`/api/v1`)

Todas las rutas del API requieren la cabecera **`X-Tenant-ID`** (UUID del tenant).

| Método | Ruta | Auth |
|--------|------|------|
| POST | `/auth/register` | No (devuelve JWT al registrar) |
| POST | `/auth/login` | No |
| GET | `/auth/me` | Bearer JWT |
| POST | `/auth/refresh` | Middleware `jwt.refresh` (renovación con ventana de refresh) |
| POST | `/auth/logout` | Bearer JWT |

Respuestas siempre en **JSON**.

---

## Validación recomendada

```bash
php artisan route:list --path=api
php artisan config:clear
npm run build
php artisan test
```

---

## Entrega esperada

El estudiante debe trabajar sobre su propio fork del repositorio y entregar en Canvas el enlace al repositorio forkeado, junto con una breve descripción del módulo implementado y los commits principales que evidencian su avance.

# Módulo Notas Clínicas SOAP

## Descripción

Implementación del módulo de notas clínicas SOAP para el sistema hospitalario.

## Funcionalidades

- Crear nota SOAP
- Listar notas SOAP
- Consultar nota SOAP

## Rutas API

POST /api/v1/soap-notes

GET /api/v1/soap-notes

GET /api/v1/soap-notes/{id}

## Evidencias

Las evidencias se encuentran organizadas por Sprint dentro de la carpeta evidencias.