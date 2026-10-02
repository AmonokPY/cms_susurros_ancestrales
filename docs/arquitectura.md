# Arquitectura del CMS (adaptada a Susurros Ancestrales)

La guía del curso describe un CMS genérico (páginas, noticias, banners). Este proyecto cubre la misma capa de seguridad con el contenido real del portal.

```
USUARIO
   |
   v
NAVEGADOR / BLADE + VITE
   |
   v
ROUTES (web.php)
   +-- guest: login, registro
   +-- middleware auth: dashboard, logout
   +-- can:manage-users: usuarios
   +-- can:manage-content: inicio, juega, explora
   +-- can:manage-media: puzzles
   |
   v
CONTROLLERS / SERVICES
   +-- Auth (login, registro, logout)
   +-- Admin (contenido y usuarios)
   +-- AuditLogger
   |
   v
MODELS
   |
   v
DATABASE
   +-- users (rol admin | editor)
   +-- audit_logs
   +-- site_settings   (equivale a páginas / configuración)
   +-- play_items      (equivale a servicios / módulos)
   +-- sponsors        (equivale a alianzas / banners de marca)
   +-- puzzles         (equivale a galería / multimedia)
   +-- puzzle_slug_redirects
```

Gates: `manage-users` (solo admin), `manage-content` y `manage-media` (admin y editor).
