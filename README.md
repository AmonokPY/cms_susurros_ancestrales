# CMS Susurros Ancestrales

Portal web seguro con Laravel: páginas públicas, registro, login y dashboard del CMS protegido.

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- SQLite (por defecto) o MySQL

## Instalación

```bash
composer install
copy .env.example .env   # Windows
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Ejecución

```bash
composer run dev
```

O en dos terminales:

```bash
php artisan serve
npm run dev
```

Abrir: http://localhost:8000

## Usuario de prueba

| Campo | Valor |
|-------|-------|
| Correo | `usuario@secureapp.test` |
| Contraseña | `Segura#2026!` |

## Flujo de la aplicación

1. **Inicio** (`/`) — página principal pública
2. **Registro** (`/registro`) o **Login** (`/login`)
3. **Dashboard** (`/dashboard`) — solo usuarios autenticados

## Estructura de rutas

| Método | URI | Nombre | Controlador | Acceso |
|--------|-----|--------|-------------|--------|
| GET | `/` | `home` | PageController@home | Público |
| GET | `/acerca` | `about` | PageController@about | Público |
| GET | `/contacto` | `contact` | PageController@contact | Público |
| POST | `/contacto` | `contact.send` | PageController@sendContact | Público + throttle |
| GET | `/registro` | `register` | RegisterController@create | Guest |
| POST | `/registro` | `register.store` | RegisterController@store | Guest |
| GET | `/login` | `login` | LoginController@create | Guest |
| POST | `/login` | `login.store` | LoginController@store | Guest + throttle:login |
| GET | `/dashboard` | `dashboard` | vista dashboard | Auth |
| GET | `/posts` | `posts.index` | PostController@index | Auth |
| GET | `/posts/create` | `posts.create` | PostController@create | Auth |
| POST | `/posts` | `posts.store` | PostController@store | Auth |
| GET | `/posts/{post}` | `posts.show` | PostController@show | Auth |
| GET | `/posts/{post}/edit` | `posts.edit` | PostController@edit | Auth |
| PUT/PATCH | `/posts/{post}` | `posts.update` | PostController@update | Auth + Policy |
| DELETE | `/posts/{post}` | `posts.destroy` | PostController@destroy | Auth + Policy |
| POST | `/logout` | `logout` | LoginController@destroy | Auth |

## Controles de seguridad aplicados

- Secretos en `.env` (no versionado)
- CSRF (`@csrf`) en formularios POST
- Validación en servidor
- Hash de contraseñas (`Hash::make` / cast `hashed`)
- Regeneración de sesión tras login/registro
- Logout con `invalidate` + `regenerateToken`
- Middleware `auth` / `guest`
- Rate limiting en login (`throttle:login`, 5/min por email+IP)
- Escape Blade `{{ }}` contra XSS
- Contraseña de registro: mínimo 12 caracteres, mayúsculas, minúsculas, números y símbolos
- CRUD de publicaciones con `user_id` desde el usuario autenticado (no del formulario)
- Policy/Gate: solo el propietario edita o elimina
- Form Requests para validación de store/update

## Comandos útiles

```bash
php artisan route:list
composer audit
npm audit
php artisan test
```
