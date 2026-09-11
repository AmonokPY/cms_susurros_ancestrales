# CMS Susurros Ancestrales

Portal web y CMS del videojuego **Susurranes**, desarrollado en Laravel para las asignaturas *Desarrollo de Software Seguro* y *Seguridad en Aplicaciones* (Especialización en Seguridad de la Información — Universidad de Cundinamarca).

Identidad visual: design system **gia_stilo_colombiano** (paleta de la bandera, tipografías Fraunces/Outfit, tema claro/oscuro).

## Stack

- Laravel 12, PHP 8.2+
- Vite, Blade
- MySQL (XAMPP) en desarrollo local; SQLite en memoria para pruebas
- PHPUnit

## Requisitos

- PHP 8.2+, Composer, Node.js y npm
- MySQL 8 (o MariaDB de XAMPP) para el entorno local

## Instalación

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
```

En producción: `APP_DEBUG=false`, `APP_ENV=production` y nunca versionar `.env`.

## Ejecución

```bash
composer run dev
```

O `php artisan serve` + `npm run dev`. Abrir `http://localhost:8000`.

## Usuario de prueba (seeder)

| Campo | Valor |
|-------|-------|
| Correo | `usuario@secureapp.test` |
| Contraseña | `Segura#2026!` |

## Rutas principales

| Método | URI | Nombre | Acceso |
|--------|-----|--------|--------|
| GET | `/` | `home` | Público |
| GET | `/acerca` | `about` | Público |
| GET | `/contacto` | `contact` | Público |
| POST | `/contacto` | `contact.send` | Público + `throttle:contact` |
| GET | `/puzzles` | `puzzles.index` | Público |
| GET | `/login` | `login` | Guest |
| POST | `/login` | `login.store` | Guest + `throttle:login` |
| GET | `/registro` | `register` | Guest |
| POST | `/registro` | `register.store` | Guest |
| GET | `/dashboard` | `dashboard` | Auth |
| resource | `/posts` | `posts.*` | Auth + Policy |
| POST | `/logout` | `logout` | Auth |
| prefix | `/cms/*` | `admin.*` | Auth |

## Controles de seguridad

- `.env` fuera de Git; secretos fuera del código
- CSRF en formularios de estado
- Validación en servidor (Form Request / `$request->validate`)
- `Auth::attempt()` + `session()->regenerate()`
- Logout con `invalidate()` + `regenerateToken()`
- Middleware `auth` / `guest`
- Rate limiting de login (5/min email+IP) y contacto (10/min por IP)
- Hash de contraseñas (`hashed` / `Hash::make`)
- Route model binding y `PostPolicy`
- Escape Blade `{{ }}`
- Rutas nombradas y `@vite`
- Documentación en `/docs`

Detalle del cruce con las guías del curso: [docs/auditoria-guias.md](docs/auditoria-guias.md).

## Pruebas

```bash
php artisan test
composer audit
npm audit
```

## Cómo contribuir

1. Crea una rama desde `main` (`feature/...`, `security/...`, `docs/...`).
2. Commits atómicos con Conventional Commits en español.
3. Abre un Pull Request hacia `main` (plantilla en `.github/`).
4. No uses `git push --force` sobre `main`.
