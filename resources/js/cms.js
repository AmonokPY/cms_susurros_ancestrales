import { initThemeToggle } from './theme';

initThemeToggle();

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (window.tinymce) {
    tinymce.init({
        selector: 'textarea.wysiwyg',
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link',
        height: 280,
        license_key: 'gpl',
    });
}

document.getElementById('add-social')?.addEventListener('click', () => {
    const wrap = document.getElementById('extra-socials');
    const index = wrap.querySelectorAll('.social-row').length;
    const row = document.createElement('div');
    row.className = 'social-row';
    row.innerHTML = `
        <input name="extra_socials[${index}][name]" placeholder="Nombre" aria-label="Nombre de la red social ${index + 1}">
        <input type="url" name="extra_socials[${index}][url]" placeholder="https://" aria-label="URL de la red social ${index + 1}">
    `;
    wrap.appendChild(row);
    row.querySelector('input')?.focus();
});

const announceLive = (message) => {
    const live = document.getElementById('cms-live-region');
    if (live) {
        live.textContent = message;
    }
};

const saveOrder = async (list) => {
    const ids = [...list.querySelectorAll('li[data-id]')].map((li) => Number(li.dataset.id));
    await fetch(list.dataset.reorder, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ ids }),
    });
};

document.querySelectorAll('.reorder-list').forEach((list) => {
    let dragged;

    list.addEventListener('dragstart', (event) => {
        dragged = event.target.closest('li[data-id]');
        event.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();
        const over = event.target.closest('li[data-id]');
        if (!dragged || !over || over === dragged) return;
        const rect = over.getBoundingClientRect();
        const after = event.clientY > rect.top + rect.height / 2;
        over.parentNode.insertBefore(dragged, after ? over.nextSibling : over);
    });

    list.addEventListener('drop', async (event) => {
        event.preventDefault();
        await saveOrder(list);
        announceLive('Orden actualizado.');
    });

    list.addEventListener('keydown', async (event) => {
        if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') return;
        const handle = event.target.closest('.handle');
        if (!handle) return;

        const item = handle.closest('li[data-id]');
        if (!item) return;

        event.preventDefault();
        if (event.key === 'ArrowUp' && item.previousElementSibling) {
            item.parentNode.insertBefore(item, item.previousElementSibling);
            handle.focus();
            await saveOrder(list);
            announceLive('Elemento movido hacia arriba.');
        } else if (event.key === 'ArrowDown' && item.nextElementSibling) {
            item.parentNode.insertBefore(item.nextElementSibling, item);
            handle.focus();
            await saveOrder(list);
            announceLive('Elemento movido hacia abajo.');
        }
    });
});
