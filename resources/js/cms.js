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
        <input name="extra_socials[${index}][name]" placeholder="Nombre">
        <input type="url" name="extra_socials[${index}][url]" placeholder="https://">
    `;
    wrap.appendChild(row);
});

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
    });
});
