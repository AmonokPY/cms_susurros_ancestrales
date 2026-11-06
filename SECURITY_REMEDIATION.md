# Remediación de Seguridad OWASP ZAP 2.17.0 — CMS "Susurros Ancestrales"

- **Proyecto:** CMS "Susurros Ancestrales" (Laravel 12 · PHP 8.3 · Nginx 1.30.6 en Azure App Service)
- **Fecha de Auditoría:** 2026-11-05
- **Rama de Remediación:** `security/zap-remediation-2026-11`

---

## 1. Matriz de Remediación y Evidencia por Hallazgo ZAP

| Hallazgo ZAP | CWE | Archivo(s) modificado(s) | Estado | Evidencia |
| :--- | :--- | :--- | :--- | :--- |
| **[ALTO]** Inyección SQL en `POST /contacto` (`name`) | CWE-89 | `app/Http/Requests/StoreContactRequest.php`, `app/Http/Controllers/ContactController.php`, `app/Mail/ContactMessage.php`, `resources/views/emails/contact.blade.php`, `tests/Feature/ZapSecurityRemediationTest.php` | ✅ Resuelto | Validación estricta por lista blanca `regex:/^[\pL\s\'\-\.]+$/u`, uso exclusivo de Eloquent prepared statements (`AuditLog::record`), escapado explícito con `e()` y test `test_contact_rejects_sql_injection_payload_and_stores_literals_safely` (`422` ante `"ZAP OR 1=1 --"` y persistencia literal ante `"ZAP' OR 'a'--"`). |
| **[ALTO]** Path Traversal en `POST /contacto` (`name`) | CWE-22 | `app/Http/Requests/StoreContactRequest.php`, `app/Http/Controllers/ContactController.php`, `app/Services/MediaService.php`, `tests/Feature/ZapSecurityRemediationTest.php` | ✅ Resuelto | Regla `not_regex:/(\.\.|[\/\\\\\x00])/u` en `StoreContactRequest`, validación `basename()` + `realpath()` confinada a `storage_path('app/public')` en `MediaService::resolveSafePublicStoragePath()`, y tests `test_contact_rejects_path_traversal_payloads_with_422` (`"\\contacto"`, `"../../etc/passwd"` → `422`) y `test_media_service_blocks_path_traversal`. |
| **[ALTO]** PHP 8.3.36 con vulnerabilidades conocidas / Exposición de versión | CWE-1395 | `php.ini`, `public/.user.ini`, `.github/workflows/main_app-susurros-cms.yml`, `startup.sh` | ✅ Resuelto | Directiva `expose_php = Off` en `php.ini`, `public/.user.ini` y workflow de GitHub Actions (`shivammathur/setup-php@v2` última revisión parcheada de PHP 8.3.x). |
| **[BAJO]** Divulgación de versión en cabeceras `Server` y `X-Powered-By` | CWE-497 | `app/Http/Middleware/RemoveServerHeaders.php`, `bootstrap/app.php`, `default`, `public/.htaccess`, `php.ini` | ✅ Resuelto | `server_tokens off;` y `fastcgi_hide_header X-Powered-By;` en `default` (Nginx), `expose_php = Off` en `php.ini`, y middleware global `RemoveServerHeaders` que elimina `X-Powered-By` y fija `Server: webserver`. |
| **[MEDIO]** `Content-Security-Policy` (CSP) ausente | CWE-693 | `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php`, `tests/Feature/ZapSecurityRemediationTest.php` | ✅ Resuelto | Cabecera `Content-Security-Policy` aplicada globalmente con `default-src 'self'`, `frame-ancestors 'none'`, `base-uri 'self'`, `form-action 'self'`, `upgrade-insecure-requests` y orígenes estrictos (`https://cdn.jsdelivr.net`, Google Fonts, YouTube/Vimeo embed). |
| **[MEDIO]** Anti-Clickjacking (`X-Frame-Options`) ausente | CWE-1021 | `app/Http/Middleware/SecurityHeaders.php`, `default`, `tests/Feature/ZapSecurityRemediationTest.php` | ✅ Resuelto | Cabecera `X-Frame-Options: DENY` inyectada globalmente en `SecurityHeaders` y en el bloque `server` de Nginx (`default`). |
| **[MEDIO]** Subresource Integrity (SRI) ausente en scripts externos | CWE-345 / CWE-829 | `resources/views/layouts/public.blade.php`, `resources/views/layouts/cms.blade.php`, `tests/Feature/ZapSecurityRemediationTest.php` | ✅ Resuelto | Atributos `integrity="sha384-..."` y `crossorigin="anonymous"` añadidos en `gsap.min.js`, `Draggable.min.js` y `tinymce.min.js`. Cubierto por `test_external_scripts_include_sri_integrity_attributes`. |
| **[BAJO]** `Strict-Transport-Security` (HSTS) ausente | CWE-319 | `app/Http/Middleware/SecurityHeaders.php`, `app/Providers/AppServiceProvider.php`, `default` | ✅ Resuelto | Cabecera `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload` y `URL::forceScheme('https')` en `AppServiceProvider::boot()`. |
| **[BAJO]** `X-Content-Type-Options` ausente | CWE-693 | `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php`, `default` | ✅ Resuelto | Cabecera `X-Content-Type-Options: nosniff` registrada globalmente en `bootstrap/app.php` (cubre respuestas 2xx, 3xx, 4xx y 5xx). |
| **[BAJO]** Cookie `XSRF-TOKEN` sin bandera `HttpOnly` | CWE-1004 | `app/Http/Middleware/VerifyCsrfToken.php`, `app/Http/Middleware/SecurityHeaders.php`, `config/session.php`, `resources/js/bootstrap.js`, `bootstrap/app.php` | ✅ Resuelto | `VerifyCsrfToken` y `SecurityHeaders` emiten `XSRF-TOKEN` y la cookie de sesión con `HttpOnly=true`, `Secure=true` y `SameSite=Lax`. El frontend consume el token desde `<meta name="csrf-token">`. Cubierto por `test_xsrf_token_cookie_has_httponly_secure_and_samesite_lax`. |
| **[BAJO]** Gran redirección con cuerpo (`POST /login`, `/registro`, `/contacto`) | CWE-201 | `app/Http/Middleware/SecurityHeaders.php`, `app/Http/Controllers/Auth/LoginController.php`, `app/Http/Controllers/Auth/RegisterController.php`, `app/Http/Controllers/ContactController.php` | ✅ Resuelto | Eliminación del cuerpo HTML `"Redirecting to..."` (`$response->setContent('')` y `Content-Length: 0`) en todas las respuestas 3xx. Cubierto por `test_login_register_and_contact_redirects_have_empty_body`. |
| **[BAJO]** `Cache-Control` en respuestas de autenticación y formularios | CWE-525 | `app/Http/Middleware/SecurityHeaders.php`, `default`, `public/.htaccess` | ✅ Resuelto | `Cache-Control: no-store, no-cache, must-revalidate, private` y `Pragma: no-cache` en `/login`, `/registro`, `/contacto`, `/dashboard`, `/cms/*` y `Cache-Control: public, max-age=31536000, immutable` para activos estáticos. |
| **[Auditoría Composer]** Paquetes Symfony vulnerables | CWE-1395 | `composer.lock`, `.github/workflows/main_app-susurros-cms.yml` | ✅ Resuelto | Actualizados `symfony/http-foundation` (`v7.4.23`), `symfony/http-kernel` (`v7.4.23`), `symfony/mime` (`v7.4.22`), `symfony/routing` (`v7.4.23`), `symfony/yaml` (`v7.4.23`). Paso `composer audit --no-dev --format=json` añadido al CI. |
| **[Insight ZAP]** 98% respuestas lentas | Rendimiento | `php.ini`, `public/.user.ini`, `startup.sh`, `.github/workflows/main_app-susurros-cms.yml`, `app/Http/Controllers/PageController.php` | ✅ Resuelto | OPcache activado (`opcache.enable=1`, `opcache.validate_timestamps=0`), caché de configuración/rutas/vistas (`config:cache`, `route:cache`, `view:cache`) en `startup.sh` y eager loading `with('puzzle')` en `PageController`. |

---

## 2. Registro de Hashes Subresource Integrity (SRI)

Hashes SHA-384 calculados directamente sobre los artefactos oficiales servidos por `https://cdn.jsdelivr.net`:

| Recurso Externo | Versión | Hash SRI (`sha384`) | Vista Blade |
| :--- | :--- | :--- | :--- |
| `https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js` | `3.12.5` | `sha384-g4NTh/Iv5PPU4xPyhEWqPcwtNXOvdaDI8LLnyYfyNZOjKJeYQyjzQ9X5275eBjpt` | `resources/views/layouts/public.blade.php` |
| `https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/Draggable.min.js` | `3.12.5` | `sha384-ioeiiA4Qj8TNkk15vf+DfcM4fQbw3fUabf9VWRr56yQZ2fjAWiHPV6oWH2Jo0bXE` | `resources/views/layouts/public.blade.php` |
| `https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js` | `6.8.4` | `sha384-Pkj8wZH9tfNXs3Pnom44o+ZJMfEFnbi74rOyCJr+lin9jpqzmvUHAmeZjwKT+T95` | `resources/views/layouts/cms.blade.php` |

---

## 3. Instrucciones de Despliegue en Azure App Service (`app-susurros-cms`)

### 3.1. Configuración de Runtime PHP y Startup Command
En **Azure Portal → App Services → `app-susurros-cms` → Configuration → General settings**:
1. **Stack:** `PHP`
2. **Major version / Minor version:** `PHP 8.3` (Azure aplica automáticamente la última revisión parcheada disponible en la imagen base; compatible también con `PHP 8.4`).
3. **Startup Command:**
   ```bash
   /home/site/wwwroot/startup.sh
   ```
   *(El script `startup.sh` copia `default` a `/etc/nginx/sites-available/default` y `/etc/nginx/sites-enabled/default`, aplica `php.ini` en `/usr/local/etc/php/conf.d/zz-security-zap.ini`, ejecuta `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache` y recarga Nginx).*

### 3.2. Variables de Entorno (App Settings) en Azure
En **Azure Portal → App Services → `app-susurros-cms` → Environment variables (App settings)**, verificar/establecer:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app-susurros-cms.azurewebsites.net
PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/home/site/wwwroot
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SECURITY_SKIP_MX=false
```

### 3.3. Directivas `php.ini` Aplicadas en Producción
El archivo `php.ini` incluido en la raíz del proyecto aplica automáticamente:
```ini
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Lax"
session.use_strict_mode = 1
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
```

### 3.4. Configuración Nginx (`default`)
El archivo `default` en la raíz del proyecto configura:
- `server_tokens off;` y `fastcgi_hide_header X-Powered-By;` para suprimir versiones en cabeceras HTTP.
- Cabeceras `X-Frame-Options "DENY"`, `X-Content-Type-Options "nosniff"`, `Strict-Transport-Security` y `Referrer-Policy` con directiva `always`.
- Bloque `location ~* \.(?:css|js|mjs|webp|avif|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$` con `Cache-Control "public, max-age=31536000, immutable"`.

### 3.5. Comandos de Verificación Post-Despliegue
```bash
php artisan test
composer audit
curl -sI https://app-susurros-cms.azurewebsites.net/ | grep -Ei 'content-security|x-frame|strict-transport|x-content-type|server|x-powered'
```
