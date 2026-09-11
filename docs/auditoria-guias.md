# Auditoría de las guías del curso

Cruce entre *Guía Semana 2 Laravel Sitio Web Seguro* y *Guía Login Seguro Laravel MEDIT* frente al código actual.

Leyenda: **IMPLEMENTADO** · **EQUIVALENTE** · **FALTANTE** (los FALTANTE de esta pasada ya se corrigieron en código).

## Semana 2 — sitio base y DevSecOps

| Requisito | Estado | Evidencia |
|-----------|--------|-----------|
| Rutas home, about, contact con nombre | IMPLEMENTADO | `home`, `about`, `contact` |
| POST contacto + throttle | IMPLEMENTADO | `contact.send` + `throttle:10,1` |
| Layout Blade + `@vite` + `route()` | IMPLEMENTADO | `layouts/app.blade.php` |
| Escape `{{ }}` | IMPLEMENTADO | vistas Blade |
| `.env` fuera de Git | IMPLEMENTADO | `.gitignore` |
| APP_KEY / secretos fuera del código | IMPLEMENTADO | `.env` |
| Auth: registro, login guest, logout auth | IMPLEMENTADO | `Auth/*Controller` |
| Password hashed + política fuerte | IMPLEMENTADO | `Hash::make` / `Password::min(12)` |
| `session()->regenerate()` tras login | IMPLEMENTADO | `LoginController@store` |
| Logout invalidate + regenerateToken | IMPLEMENTADO | `LoginController@destroy` |
| Dashboard `auth` | IMPLEMENTADO | `DashboardController` |
| CRUD posts resource + Form Request | IMPLEMENTADO | `PostController` |
| `user_id` desde usuario autenticado | IMPLEMENTADO | `posts()->create()` |
| Policy dueño del post | IMPLEMENTADO | `PostPolicy` |
| README de instalación | IMPLEMENTADO | `README.md` |

## Login seguro MEDIT

| Requisito | Estado | Evidencia |
|-----------|--------|-----------|
| GET/POST `/login` con nombres | IMPLEMENTADO | `login`, `login.store` |
| `throttle:login` por email+IP | IMPLEMENTADO | `AppServiceProvider` |
| Validación servidor | IMPLEMENTADO | `$request->validate` |
| Error genérico | IMPLEMENTADO | mensaje único en `email` |
| Remember me | IMPLEMENTADO | `Auth::attempt(..., remember)` |
| Dashboard protegido | IMPLEMENTADO | middleware `auth` |
| Logout POST + CSRF | IMPLEMENTADO | formulario POST |
| CSS/JS con Vite | IMPLEMENTADO | `@vite` |
| Prueba acceso sin login | IMPLEMENTADO | `SecurityFlowsTest` |

## Portal Susurranes (CMS de contenido)

| Requisito | Estado |
|-----------|--------|
| Inicio + Puzzles + CMS | IMPLEMENTADO |
| Carrusel GSAP Juega/Explora | EQUIVALENTE (instancias independientes, sin pin de página completa) |
| Carrusel 3D puzzles + modal | IMPLEMENTADO |
| Slug único + redirect de slug antiguo | IMPLEMENTADO |

## Controles mínimos de seguridad

CSRF, Form Request, Auth::attempt, regenerate, logout seguro, auth/guest, throttle, hashing, policies, Blade escape, rutas nombradas, Vite: **IMPLEMENTADO**.
