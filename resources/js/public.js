import { initThemeToggle } from './theme';

function initGalleries() {
    if (typeof gsap === 'undefined' || typeof Draggable === 'undefined') {
        return;
    }

    gsap.registerPlugin(Draggable);
    document.querySelectorAll('.gallery').forEach(initGallery);
}

function initGallery(root) {
    const items = gsap.utils.toArray(root.querySelectorAll('.cards li'));
    const nextBtn = root.querySelector('.next');
    const prevBtn = root.querySelector('.prev');
    const proxy = root.parentElement.querySelector('.drag-proxy');

    if (!items.length) {
        return;
    }

    gsap.set(items, { xPercent: 400, opacity: 0, scale: 0 });

    const spacing = 0.1;
    const snapTime = gsap.utils.snap(spacing);
    const animateFunc = (element) => {
        const tl = gsap.timeline();
        tl.fromTo(
            element,
            { scale: 0, opacity: 0 },
            { scale: 1, opacity: 1, zIndex: 100, duration: 0.5, yoyo: true, repeat: 1, ease: 'power1.in', immediateRender: false }
        ).fromTo(
            element,
            { xPercent: 400 },
            { xPercent: -400, duration: 1, ease: 'none', immediateRender: false },
            0
        );
        return tl;
    };

    const seamlessLoop = buildSeamlessLoop(items, spacing, animateFunc);
    const playhead = { offset: 0 };
    const wrapTime = gsap.utils.wrap(0, seamlessLoop.duration());
    const scrub = gsap.to(playhead, {
        offset: 0,
        onUpdate() {
            seamlessLoop.time(wrapTime(playhead.offset));
        },
        duration: 0.5,
        ease: 'power3',
        paused: true,
    });

    function scrollToOffset(offset) {
        const snappedTime = snapTime(offset);
        playhead.offset = snappedTime;
        scrub.vars.offset = snappedTime;
        scrub.invalidate().restart();
    }

    nextBtn?.addEventListener('click', () => scrollToOffset(scrub.vars.offset + spacing));
    prevBtn?.addEventListener('click', () => scrollToOffset(scrub.vars.offset - spacing));

    if (proxy) {
        Draggable.create(proxy, {
            type: 'x',
            trigger: root.querySelector('.cards'),
            onPress() {
                this.startOffset = scrub.vars.offset;
            },
            onDrag() {
                scrub.vars.offset = this.startOffset + (this.startX - this.x) * 0.001;
                scrub.invalidate().restart();
            },
            onDragEnd() {
                scrollToOffset(scrub.vars.offset);
            },
        });
    }

    scrollToOffset(0.5);
}

function buildSeamlessLoop(items, spacing, animateFunc) {
    const overlap = Math.ceil(1 / spacing);
    const startTime = items.length * spacing + 0.5;
    const loopTime = (items.length + overlap) * spacing + 1;
    const rawSequence = gsap.timeline({ paused: true });
    const seamlessLoop = gsap.timeline({
        paused: true,
        repeat: -1,
        onRepeat() {
            this._time === this._dur && (this._tTime += this._dur - 0.01);
        },
    });

    const l = items.length + overlap * 2;
    for (let i = 0; i < l; i++) {
        const index = i % items.length;
        const time = i * spacing;
        rawSequence.add(animateFunc(items[index]), time);
        if (i <= items.length) {
            seamlessLoop.add('label' + i, time);
        }
    }

    rawSequence.time(startTime);
    seamlessLoop
        .to(rawSequence, {
            time: loopTime,
            duration: loopTime - startTime,
            ease: 'none',
        })
        .fromTo(
            rawSequence,
            { time: overlap * spacing + 1 },
            {
                time: startTime,
                duration: startTime - (overlap * spacing + 1),
                immediateRender: false,
                ease: 'none',
            }
        );

    return seamlessLoop;
}

function youtubeEmbed(url) {
    if (!url) return null;
    const yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
    if (yt) return `https://www.youtube.com/embed/${yt[1]}`;
    const vm = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
    if (vm) return `https://player.vimeo.com/video/${vm[1]}`;
    return url;
}

function initPuzzleCarousel() {
    const cards = document.querySelectorAll('.carousel-track .card');
    if (!cards.length) {
        return;
    }

    const dataEl = document.getElementById('puzzles-data');
    const bootEl = document.getElementById('puzzles-boot');
    const puzzles = dataEl ? JSON.parse(dataEl.textContent) : [];
    const boot = bootEl ? JSON.parse(bootEl.textContent) : {};
    const dots = document.querySelectorAll('.dot');
    const memberName = document.querySelector('.member-name');
    const memberRole = document.querySelector('.member-role');
    const upArrows = document.querySelectorAll('.nav-arrow.up');
    const downArrows = document.querySelectorAll('.nav-arrow.down');
    const modal = document.getElementById('puzzle-modal');
    const modalBody = document.getElementById('puzzle-modal-body');
    let currentIndex = 0;
    let isAnimating = false;

    function updateCarousel(newIndex) {
        if (isAnimating || !cards.length) return;
        isAnimating = true;
        currentIndex = (newIndex + cards.length) % cards.length;

        cards.forEach((card, i) => {
            const offset = (i - currentIndex + cards.length) % cards.length;
            card.classList.remove('center', 'up-1', 'up-2', 'down-1', 'down-2', 'hidden');
            if (offset === 0) card.classList.add('center');
            else if (offset === 1) card.classList.add('down-1');
            else if (offset === 2) card.classList.add('down-2');
            else if (offset === cards.length - 1) card.classList.add('up-1');
            else if (offset === cards.length - 2) card.classList.add('up-2');
            else card.classList.add('hidden');
        });

        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));

        if (memberName && puzzles[currentIndex]) {
            memberName.style.opacity = '0';
            if (memberRole) memberRole.style.opacity = '0';
            setTimeout(() => {
                memberName.textContent = puzzles[currentIndex].nombre;
                if (memberRole) memberRole.textContent = puzzles[currentIndex].descripcion_corta || '';
                memberName.style.opacity = '1';
                if (memberRole) memberRole.style.opacity = '1';
            }, 300);
        }

        setTimeout(() => {
            isAnimating = false;
        }, 800);
    }

    function openModal(slug) {
        const puzzle = puzzles.find((item) => item.slug === slug);
        if (!puzzle || !modal || !modalBody) return;

        const details = puzzle.detalles || {};
        const embed = youtubeEmbed(details.video_url);
        modalBody.innerHTML = `
            <h2>${puzzle.detalles?.titulo_completo || puzzle.nombre}</h2>
            <div class="rich-text">${details.texto_descripcion || ''}</div>
            ${embed ? `<iframe src="${embed}" title="Video" height="360" allowfullscreen></iframe>` : ''}
            ${details.audio_url ? `<audio controls src="${details.audio_url}"></audio>` : ''}
            ${details.imagen_adicional ? `<img src="${details.imagen_adicional}" alt="">` : ''}
            <div class="rich-text">${details.beneficios || ''}</div>
            ${details.ubicacion?.direccion ? `<p><strong>Dirección:</strong> ${details.ubicacion.direccion}</p>` : ''}
            ${details.ubicacion?.coordenadas ? `<p><strong>Coordenadas:</strong> ${details.ubicacion.coordenadas}</p>` : ''}
            ${details.ubicacion?.maps_url ? `<p><a href="${details.ubicacion.maps_url}" target="_blank" rel="noopener noreferrer">Ver en Google Maps</a></p>` : ''}
            ${details.cta?.texto && details.cta?.enlace ? `<a class="cta-btn" href="${details.cta.enlace}">${details.cta.texto}</a>` : ''}
        `;
        modal.hidden = false;
        const url = `/puzzles/${slug}`;
        if (window.location.pathname !== url) {
            history.pushState({ slug }, '', url);
        }
    }

    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        if (boot.indexUrl && window.location.pathname !== boot.indexUrl) {
            history.pushState({}, '', boot.indexUrl);
        }
    }

    upArrows.forEach((arrow) => arrow.addEventListener('click', () => updateCarousel(currentIndex - 1)));
    downArrows.forEach((arrow) => arrow.addEventListener('click', () => updateCarousel(currentIndex + 1)));
    dots.forEach((dot, i) => dot.addEventListener('click', () => updateCarousel(i)));
    cards.forEach((card, i) => {
        card.addEventListener('click', (event) => {
            if (event.target.closest('[data-open-puzzle]')) return;
            updateCarousel(i);
        });
    });

    document.querySelectorAll('[data-open-puzzle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            openModal(button.getAttribute('data-open-puzzle'));
        });
    });
    document.querySelectorAll('[data-close-modal]').forEach((el) => {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowUp') updateCarousel(currentIndex - 1);
        if (e.key === 'ArrowDown') updateCarousel(currentIndex + 1);
    });

    let touchStartY = 0;
    document.addEventListener('touchstart', (e) => {
        touchStartY = e.changedTouches[0].screenY;
    });
    document.addEventListener('touchend', (e) => {
        const diff = touchStartY - e.changedTouches[0].screenY;
        if (Math.abs(diff) > 50) {
            updateCarousel(diff > 0 ? currentIndex + 1 : currentIndex - 1);
        }
    });

    updateCarousel(0);

    if (boot.active) {
        const idx = puzzles.findIndex((item) => item.slug === boot.active);
        if (idx >= 0) {
            updateCarousel(idx);
            openModal(boot.active);
        }
    }
}

initGalleries();
initPuzzleCarousel();
initThemeToggle();
