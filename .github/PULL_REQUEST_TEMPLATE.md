## Resumen

- Qué cambia y por qué
- Controles de seguridad afectados (CSRF, auth, policies, throttle, etc.)
- Cómo se verificó (pruebas, capturas, `php artisan test`)

## Checklist

- [ ] No se incluye `.env`, claves ni credenciales
- [ ] Las rutas usan `route()` y nombres existentes
- [ ] Formularios de estado llevan `@csrf`
- [ ] `php artisan test` pasa
- [ ] El design system (tokens ColombiaDS) se mantiene en vistas nuevas
