# Guía completada — Correo en el CMS Susurros Ancestrales

**Base:** Guía de aprendizaje 2026-II (enviar y recibir correo en Laravel).  
**Proyecto real:** CMS Laravel 12 del videojuego **Susurros Ancestrales**, no el ZIP “CMS Clase” con Posts.  
**Fecha de cierre de esta implementación:** 18 de septiembre de 2026.

Esta guía describe **lo que quedó construido en este repositorio**. SMTP envía; POP3 (Mailtrap Email Testing) consulta el buzón. IMAP queda preparado por configuración (`IMAP_PROTOCOL`).

---

## 0. Qué ya existía y qué se agregó

| Requisito de la guía | Estado en este CMS |
|----------------------|--------------------|
| CMS base (MVC, login, dashboard, CRUD) | Ya existía (Inicio, Juega, Explora, Puzzles, usuarios) |
| Credenciales fuera de Git | `.env` (no se sube). `.env.example` sin secretos |
| SMTP en `.env` | Mailtrap sandbox (usted ya lo tenía) |
| Mailable `ContactMessage` | Completado (Reply-To del visitante; el From es del CMS) |
| Vista `emails/contact` | Completada con `{{ }}` (escape XSS) |
| Formulario `/contacto` | Completado (antes faltaba la vista y el throttle real) |
| Rate limit `throttle:contact` | 10/min por IP |
| IMAP/POP3 | Webklex 6.2 (IMAP) + cliente POP3 nativo para Mailtrap/XAMPP |
| Comando `mail:receive` | Implementado |
| Tabla `received_emails` + `message_id` unique | Implementada |
| Panel CMS de correos | `/cms/correos` (Gate `manage-content`) |
| Scheduler cada 5 minutos | `routes/console.php` |
| Webhooks | No implementados (opcional avanzado de la guía) |

**Publicaciones / Posts:** este CMS no los usa. El equivalente de “contenido” es Inicio, Juega, Explora y Puzzles.

---

## 1. Idea central (igual que la guía del curso)

**Enviar y recibir son problemas distintos.**

- **SMTP** entrega el mensaje saliente (formulario → servidor de correo → buzón).
- **IMAP o POP3** consultan un buzón que ya tiene mensajes. Laravel **no** “recibe SMTP” como si fuera un POST del navegador.

Mailtrap Email Testing, en este laboratorio, publica:

| Función | Host | Puerto | Protocolo |
|---------|------|--------|-----------|
| Envío | `sandbox.smtp.mailtrap.io` | 2525 (también 587 / 465) | SMTP + STARTTLS |
| Recepción | `pop3.mailtrap.io` | 1100 (STARTTLS opcional; en XAMPP Windows a veces `notls`) o 9950 (SSL) | **POP3** |

La guía prefiere IMAP; Mailtrap Testing documenta **POP3**. El paquete Webklex admite `IMAP_PROTOCOL=pop3`. Si más adelante usan un buzón IMAP real, cambian host, puerto 993, `IMAP_PROTOCOL=imap` y `IMAP_ENCRYPTION=ssl`.

---

## 2. Arquitectura implementada

```
VISITANTE
    |  GET/POST /contacto  (CSRF + validación + throttle)
    v
ContactController
    |  Mail::to(MAIL_CONTACT_TO)
    v
Mailable ContactMessage  (From = CMS, Reply-To = visitante)
    v
SMTP Mailtrap
    v
Buzón de pruebas
    |  POP3  (artisan mail:receive  o  botón en el CMS)
    v
MailReceiveService
    |  ¿message_id existe?  → ignorar  /  guardar
    v
Tabla received_emails
    v
Panel /cms/correos  (auth + authorized + can:manage-content)
```

---

## 3. Archivos clave

| Pieza | Ruta |
|-------|------|
| Rutas | `routes/web.php` (`contact.*`, `admin.emails.*`) |
| Form Request | `app/Http/Requests/StoreContactRequest.php` |
| Controlador envío | `app/Http/Controllers/ContactController.php` |
| Mailable | `app/Mail/ContactMessage.php` |
| Vista correo | `resources/views/emails/contact.blade.php` |
| Formulario | `resources/views/contact/create.blade.php` |
| Rate limit | `AppServiceProvider` + `config/security.php` |
| Config IMAP/POP3 | `config/imap.php` |
| Servicio recepción | `app/Services/MailReceiveService.php` + `app/Services/Pop3Client.php` |
| Comando | `app/Console/Commands/ReceiveEmails.php` |
| Modelo | `app/Models/ReceivedEmail.php` |
| Migración | `database/migrations/2026_09_18_000001_create_received_emails_table.php` |
| Panel | `ReceivedEmailController` + `resources/views/admin/emails/` |
| Scheduler | `routes/console.php` |
| Pruebas | `tests/Feature/ContactAndInboxTest.php` |

---

## 4. Configuración (sin secretos)

En `.env` (valores reales **solo** en su máquina):

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=...inbox Mailtrap...
MAIL_PASSWORD=...inbox Mailtrap...
MAIL_FROM_ADDRESS="noreply@susurros-ancestrales.test"
MAIL_CONTACT_TO="noreply@susurros-ancestrales.test"

IMAP_HOST=pop3.mailtrap.io
IMAP_PORT=1100
IMAP_ENCRYPTION=notls
IMAP_PROTOCOL=pop3
IMAP_USERNAME=...la misma del inbox...
IMAP_PASSWORD=...la misma del inbox...
IMAP_VALIDATE_CERT=true
```

Después de cambiar `.env`:

```
php artisan config:clear
```

PHP 8.2 (XAMPP): se habilitaron `extension=imap` y `extension=zip`. En este laboratorio **Mailtrap POP3 se consulta con un cliente TCP propio** (`Pop3Client`) porque `php_imap` en XAMPP falla al negociar TLS (`SSL not supported on this machine`) y Mailtrap Testing no implementa `UIDL`. IMAP clásico (Webklex, puerto 993) queda listo para un buzón real.

---

## 5. Cómo probar (producto de la guía)

### Envío SMTP

1. `php artisan migrate` (crea `received_emails`).
2. `php artisan serve` y `npm run dev` (o `composer run dev`).
3. Abrir `/contacto`.
4. Enviar nombre, correo y mensaje.
5. En Mailtrap → Email Testing → Inbox: debe aparecer “Nuevo mensaje desde el CMS”.
6. Comprobar **Reply-To** = el correo del formulario, no un From arbitrario del visitante.
7. Probar mensaje vacío y texto enorme (`max:5000`).
8. El 11.º envío en el mismo minuto debe fallar por throttle (429).

### Recepción POP3

1. Con un correo ya visible en Mailtrap, ejecutar:

```
php artisan mail:receive
```

2. Entrar al CMS (admin `usuario@secureapp.test` / `Segura#2026!`) → **Correos**.
3. O pulsar **Consultar buzón ahora**.
4. Volver a ejecutar el comando: no debe duplicar filas (`message_id` unique).
5. Abrir el mensaje: el cuerpo se muestra **escapado** (`{{ }}` / `<pre>`), no `{!! html !!}`.
6. Los adjuntos no se descargan ni se ejecutan; solo se indica si existían.

### Automatización

```
php artisan schedule:work
```

Ejecuta `mail:receive` cada 5 minutos, `withoutOverlapping`.

---

## 6. Autorización (justificación para el docente)

| Módulo | Quién | Por qué |
|--------|--------|---------|
| `/contacto` | Público | Canal de entrada del visitante |
| `/cms/correos` | Admin y editores con `cms_access` (`manage-content`) | El buzón es un activo interno; no es del visitante registrado |
| Usuarios / lista blanca | Solo admin | Gestión de identidades |

Un usuario solo “ver la página” **no** entra al CMS ni a la bandeja.

---

## 7. Matriz de riesgos (evidencia 10)

| Riesgo | Control aplicado |
|--------|------------------|
| Credenciales en GitHub | Solo `.env`; example vacío |
| SMTP / POP3 en claro | TLS/SSL; `validate_cert=true` |
| Spam del formulario | Validación servidor + 10 POST/min/IP + CSRF |
| XSS en HTML de correo | Texto plano + escape Blade |
| Adjuntos maliciosos | No se abren ni se guardan como ejecutables |
| Duplicados | `message_id` unique + `firstOrCreate` |
| Acceso a la bandeja | `auth` + `authorized` + Gate |
| From spoofing | From fijo del CMS; visitante solo en Reply-To |
| Logs | No se registran contraseñas SMTP |
| Session | Login ya regenera ID (módulo auth previo) |

---

## 8. Comandos de instalación de esta etapa

```
composer require webklex/laravel-imap
php artisan vendor:publish --provider="Webklex\IMAP\Providers\LaravelServiceProvider"
php artisan migrate
php artisan config:clear
php artisan test
```

---

## 9. Evidencias de la guía (qué capturar)

1. Formulario `/contacto`.
2. Mensaje en la inbox de Mailtrap.
3. Código `ContactMessage`.
4. Código `ContactController`.
5. `.env.example` (SMTP/IMAP **sin** secretos).
6. Consola `php artisan mail:receive`.
7. Migración `received_emails`.
8. Tabla en `/cms/correos`.
9. Diagrama SMTP + POP3 (sección 2).
10. Esta matriz de seguridad.
11. Commits del módulo de correo.
12. Video de explicación (login + este flujo).

---

## 10. Mensaje final de la guía

Primero entendemos el CMS. Después entendemos el correo. Luego conectamos ambos. La meta es explicar cada capa y defender por qué existe cada control.

**La seguridad no es una etapa final: es una decisión que acompaña cada ruta, cada dato y cada despliegue.**
