// Import du SCSS pour la compilation
import '../css/modules-admin.scss';

document.addEventListener('DOMContentLoaded', function () {

    // Gestionnaire pour tous les boutons toggle
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('toggle-module')) {
            e.preventDefault();
            handleModuleToggle(e.target);
        }
    });

    function handleModuleToggle(button) {
        const moduleName = button.dataset.module;
        const action = button.dataset.action;
        const card = button.closest('.akyos-module-card');

        // Désactiver le bouton pendant le traitement
        button.disabled = true;
        button.textContent = 'Chargement...';

        // Effectuer la requête AJAX
        fetch(akyosModules.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'akyos_toggle_module',
                module: moduleName,
                toggle_action: action,
                nonce: akyosModules.nonce
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour l'interface
                    updateModuleInterface(card, action === 'activate');

                    // Afficher un message de succès
                    showNotice('Module ' + (action === 'activate' ? 'activé' : 'désactivé') + ' avec succès !', 'success');
                } else {
                    showNotice('Erreur: ' + (data.data || 'Erreur inconnue'), 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotice('Erreur de connexion. Veuillez réessayer.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                button.disabled = false;
            });
    }

    function updateModuleInterface(card, isActive) {
        const button = card.querySelector('.toggle-module');
        const statusIndicator = card.querySelector('.status-indicator');
        const statusText = card.querySelector('.status-text');

        if (isActive) {
            // Activer le module
            card.classList.remove('inactive');
            card.classList.add('active');
            statusIndicator.classList.remove('inactive');
            statusIndicator.classList.add('active');
            statusText.textContent = 'Actif';
            button.classList.remove('button-primary');
            button.classList.add('button-secondary');
            button.textContent = 'Désactiver';
            button.dataset.action = 'deactivate';
        } else {
            // Désactiver le module
            card.classList.remove('active');
            card.classList.add('inactive');
            statusIndicator.classList.remove('active');
            statusIndicator.classList.add('inactive');
            statusText.textContent = 'Inactif';
            button.classList.remove('button-secondary');
            button.classList.add('button-primary');
            button.textContent = 'Activer';
            button.dataset.action = 'activate';
        }

        // Animation de transition
        card.classList.add('updating');
        setTimeout(() => {
            card.classList.remove('updating');
        }, 300);
    }

    function showNotice(message, type) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = document.createElement('div');
        notice.className = `notice ${noticeClass} is-dismissible`;
        notice.innerHTML = `<p>${message}</p>`;

        // Insérer la notice après le titre
        const title = document.querySelector('.wrap h1');
        if (title && title.parentNode) {
            title.parentNode.insertBefore(notice, title.nextSibling);
        }

        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            notice.style.opacity = '0';
            notice.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                if (notice.parentNode) {
                    notice.parentNode.removeChild(notice);
                }
            }, 300);
        }, 5000);

        // Permettre la fermeture manuelle
        notice.addEventListener('click', function (e) {
            if (e.target.classList.contains('notice-dismiss')) {
                notice.style.opacity = '0';
                notice.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (notice.parentNode) {
                        notice.parentNode.removeChild(notice);
                    }
                }, 300);
            }
        });
    }

    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.akyos-module-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
    });

    // Effet hover amélioré
    cards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const button = this.querySelector('.module-actions .button');
            if (button) {
                button.classList.add('hover');
            }
        });

        card.addEventListener('mouseleave', function () {
            const button = this.querySelector('.module-actions .button');
            if (button) {
                button.classList.remove('hover');
            }
        });
    });
}); 