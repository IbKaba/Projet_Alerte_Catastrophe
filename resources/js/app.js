document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-sidebar]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        backdrop?.classList.remove('show');
        sidebarToggle?.setAttribute('aria-expanded', 'false');
    };

    sidebarToggle?.addEventListener('click', () => {
        const willOpen = !sidebar?.classList.contains('open');
        sidebar?.classList.toggle('open');
        backdrop?.classList.toggle('show');
        sidebarToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });

    backdrop?.addEventListener('click', closeSidebar);
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeSidebar();
    });

    document.querySelectorAll('[data-flash-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-flash]')?.remove());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || 'Confirmer cette action ?';
            if (!window.confirm(message)) event.preventDefault();
        });
    });

    const geolocateButton = document.querySelector('[data-geolocate]');
    geolocateButton?.addEventListener('click', () => {
        const status = document.querySelector('[data-geolocation-status]');
        const latitude = document.querySelector('#latitude');
        const longitude = document.querySelector('#longitude');

        if (!navigator.geolocation) {
            if (status) status.textContent = 'La géolocalisation n’est pas disponible dans ce navigateur.';
            return;
        }

        geolocateButton.disabled = true;
        if (status) status.textContent = 'Recherche de votre position…';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (latitude) latitude.value = position.coords.latitude.toFixed(8);
                if (longitude) longitude.value = position.coords.longitude.toFixed(8);
                if (status) status.textContent = `Position détectée · précision estimée : ${Math.round(position.coords.accuracy)} m.`;
                geolocateButton.disabled = false;
            },
            (error) => {
                const messages = {
                    1: 'Autorisation refusée. Vous pouvez saisir les coordonnées manuellement.',
                    2: 'Position indisponible. Vérifiez la localisation de votre appareil ou saisissez les coordonnées.',
                    3: 'La recherche de position a expiré. Réessayez ou saisissez les coordonnées manuellement.',
                };
                if (status) status.textContent = messages[error.code] || 'Impossible d’obtenir la position.';
                geolocateButton.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 },
        );
    });

    document.querySelectorAll('[data-photo-uploader]').forEach((uploader) => {
        const cameraInput = uploader.querySelector('[data-camera-input]');
        const galleryInput = uploader.querySelector('[data-gallery-input]');
        const countLabel = uploader.querySelector('[data-photo-count]');
        const statusLabel = uploader.querySelector('[data-photo-status]');
        const preview = uploader.querySelector('[data-photo-preview]');
        const existing = Number.parseInt(uploader.dataset.existingCount || '0', 10) || 0;
        let previewUrls = [];

        const selectedFiles = () => [
            ...(cameraInput?.files ? Array.from(cameraInput.files) : []),
            ...(galleryInput?.files ? Array.from(galleryInput.files) : []),
        ];

        const clearPreviewUrls = () => {
            previewUrls.forEach((url) => URL.revokeObjectURL(url));
            previewUrls = [];
        };

        const syncPhotos = () => {
            const files = selectedFiles();
            const total = existing + files.length;
            const tooMany = total > 6;
            const message = tooMany ? `La limite est de 6 photos au total. ${existing} photo(s) sont déjà enregistrée(s).` : '';

            cameraInput?.setCustomValidity(message);
            galleryInput?.setCustomValidity(message);

            if (countLabel) {
                countLabel.textContent = `${Math.min(total, 99)}/6`;
                countLabel.classList.toggle('invalid', tooMany);
            }

            if (statusLabel) {
                statusLabel.classList.toggle('text-danger', tooMany);
                statusLabel.textContent = tooMany
                    ? `Trop de photos : ${total}/6. Réduisez votre sélection.`
                    : files.length === 0
                        ? 'Aucune nouvelle photo sélectionnée.'
                        : `${files.length} nouvelle(s) photo(s) prête(s) à être envoyée(s).`;
            }

            if (preview) {
                clearPreviewUrls();
                preview.replaceChildren();

                files.slice(0, 6).forEach((file) => {
                    const url = URL.createObjectURL(file);
                    previewUrls.push(url);
                    const item = document.createElement('div');
                    item.className = 'photo-preview-item';
                    const image = document.createElement('img');
                    image.src = url;
                    image.alt = `Aperçu de ${file.name || 'la photo sélectionnée'}`;
                    const badge = document.createElement('span');
                    badge.textContent = file === cameraInput?.files?.[0] ? 'Caméra' : 'Galerie';
                    item.append(image, badge);
                    preview.append(item);
                });
            }
        };

        cameraInput?.addEventListener('change', syncPhotos);
        galleryInput?.addEventListener('change', syncPhotos);
        syncPhotos();
    });

    document.querySelectorAll('[data-moderation-form]').forEach((form) => {
        const statusSelect = form.querySelector('[data-status-select]');
        const rejectionField = form.querySelector('[data-rejection-field]');
        const rejectionTextarea = rejectionField?.querySelector('textarea');

        const syncRejectionField = () => {
            const rejecting = statusSelect?.value === 'rejected';
            if (rejectionField) rejectionField.hidden = !rejecting;
            if (rejectionTextarea) rejectionTextarea.required = Boolean(rejecting);
        };

        statusSelect?.addEventListener('change', syncRejectionField);
        syncRejectionField();
    });
});
