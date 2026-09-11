# Design system ColombiaDS

Fuente: ZIP `guia_stilo_colombiano` (tokens y componentes de la guía interactiva).

## Tokens

- Amarillo `#FCD116`, azul `#003893`, rojo `#CE1126`
- Tipografías: Fraunces (display), Outfit (UI), JetBrains Mono
- Tema claro/oscuro: `data-theme` + `localStorage` clave `colombia-theme`
- Franja tricolor: clase `.franja-tricolor`

## Archivos

- `resources/css/colombia-ds.css` — tokens y componentes
- `resources/js/theme.js` — persistencia del tema
- Importado por `app.css`, `public.css` y `cms.css`

## Uso rápido

```html
<button class="btn-colombia">Acción</button>
<div class="ds-card franja-tricolor">...</div>
```
