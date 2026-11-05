import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// [BAJO · CWE-1004] Leer token CSRF desde <meta name="csrf-token"> para permitir XSRF-TOKEN con HttpOnly
const csrfToken = document.head?.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}
