<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer {{ $post->post_title }} - Éditeur</title>
    <?php wp_head(); ?>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .editor-body #wpadminbar {
            display: none;
        }

        .gutenberg-frame {
            width: 100%;
            height: 1000px;
            border: none;
            border-radius: 0 0 8px 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gutenberg-frame.loaded {
            opacity: 1;
        }

        .status-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0073aa;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="editor-body bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <img class="h-12 w-auto" src="https://lesentreprises-sengagent.gouv.fr/_next/image?url=https%3A%2F%2Fmedia.graphassets.com%2FhdIQgvtKQyCCc4u0BFwF&w=640&q=75" alt="Logo">
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900">Éditeur de contenu</h1>
                            <p class="text-sm text-gray-500">Modifier : {{ $post->post_title }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ home_url('/editor/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour au dashboard
                        </a>
                        <button id="save-button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" onclick="triggerSave()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            <span id="save-text">Enregistrer</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div id="status-message" class="status-message"></div>

            <!-- Editor Container -->
            <div class="bg-white shadow rounded-lg overflow-hidden" style="position: relative;">
                <div id="loading-overlay" class="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                <iframe id="gutenberg-frame" class="gutenberg-frame"
                    src="{{ admin_url('post.php?post=' . $post->ID . '&action=edit') }}" title="Éditeur Gutenberg"
                    onload="hideLoading()">
                </iframe>
            </div>
        </main>
    </div>

    <script>
        // Masquer le loading une fois l'iframe chargée
        function hideLoading() {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }

        // Fonction pour déclencher la sauvegarde depuis le bouton personnalisé
        function triggerSave() {
            const iframe = document.getElementById('gutenberg-frame');
            const saveButton = document.getElementById('save-button');
            const saveText = document.getElementById('save-text');

            if (iframe && iframe.contentWindow) {
                // Désactiver le bouton et changer le texte
                saveButton.disabled = true;
                saveText.textContent = 'Sauvegarde...';

                // Afficher le message de sauvegarde
                showStatus('Sauvegarde en cours...', 'info');

                // Timeout de sécurité pour réactiver le bouton après 10 secondes
                const timeoutId = setTimeout(() => {
                    saveButton.disabled = false;
                    saveText.textContent = 'Enregistrer';
                }, 10000);

                try {
                    // Simuler un clic sur le bouton de sauvegarde dans l'iframe
                    const saveButtonInIframe = iframe.contentDocument.querySelector('.editor-post-publish-button, .editor-post-publish-panel__toggle, button[data-wp-component="Button"][aria-label*="Mettre à jour"], button[data-wp-component="Button"][aria-label*="Publier"]');

                    if (saveButtonInIframe) {
                        saveButtonInIframe.click();
                    } else {
                        const event = new KeyboardEvent('keydown', {
                            key: 's',
                            code: 'KeyS',
                            ctrlKey: true,
                            bubbles: true
                        });
                        iframe.contentDocument.dispatchEvent(event);
                    }

                } catch (error) {
                    clearTimeout(timeoutId);
                    console.error('Erreur lors de la sauvegarde:', error);
                    showStatus('Erreur lors de la sauvegarde', 'error');
                    saveButton.disabled = false;
                    saveText.textContent = 'Enregistrer';
                }
            }
        }

        window.addEventListener('message', (event) => {
            if (event.data?.type === 'POST_UPDATED') {
                console.log('Mise à jour réussie du post ! ID :', event.data.postId);
                showStatus('Sauvegarde effectuée avec succès', 'success');

                // Réactiver le bouton de sauvegarde
                const saveButton = document.getElementById('save-button');
                const saveText = document.getElementById('save-text');
                if (saveButton) {
                    saveButton.disabled = false;
                    saveText.textContent = 'Enregistrer';
                }
            }
        });


        function showStatus(message, type) {
            const statusEl = document.getElementById('status-message');
            statusEl.textContent = message;
            statusEl.className = 'status-message status-' + type;
            statusEl.style.display = 'block';

            // Masquer le message après 3 secondes
            setTimeout(() => {
                statusEl.style.display = 'none';
            }, 3000);
        }

        // Injecter du CSS pour cacher l'en-tête de l'éditeur
        function injectCustomCSS() {
            const iframe = document.getElementById('gutenberg-frame');
            if (iframe && iframe.contentWindow && iframe.contentDocument) {
                try {
                    const style = iframe.contentDocument.createElement('style');
                    style.textContent = `
                        .editor-header,
                        .edit-post-header,
                        [data-name="tag"], 
                        [data-name="color"],
                        .components-notice,
                        [data-name="position"],
                        .components-popover,
                        .wp-block-post-title,
                        .editor-visual-editor__post-title-wrapper,
                        .interface-interface-skeleton__footer,
                        .block-list-appender {
                            display: none !important;
                        }                    
                        
                        .html.wp-toolbar {
                            padding-top: 0px !important;
                            padding-bottom: 0px !important;
                        }

                        .acf-block-body .acf-block-fields {
                            
                        }

                        .acf-block-body .acf-fields > .acf-field {                            
                            
                        }

                        .block-editor-block-list__layout .block-editor-block-list__block:before {
                            padding: 0px !important;
                            font-size: 23px !important;
                            background: none !important;
                            color: #000 !important;
                            left: 0px !important;
                            border: none !important;                        
                            transform: none !important;
                            top: -39px !important;
                            font-style: normal !important;
                        }


                        .acf-fields>.acf-field {
                            border: none !important;
                        }


                    `;
                    iframe.contentDocument.head.appendChild(style);

                    // Afficher l'iframe après l'injection du CSS
                    iframe.classList.add('loaded');
                } catch (error) {
                    console.log('Impossible d\'injecter le CSS');
                    // Afficher l'iframe même en cas d'erreur
                    iframe.classList.add('loaded');
                }
            }
        }

        // Injecter le script de détection de sauvegarde
        function injectSaveDetectionScript() {
            const iframe = document.getElementById('gutenberg-frame');
            if (iframe && iframe.contentWindow) {
                try {
                    const script = `
                        wp.data.subscribe(() => {
                            const isSaving = wp.data.select('core/editor').isSavingPost();

                            if (isSaving) {
                                console.log('Sauvegarde en cours');
                                // Envoyer le message au parent une seule fois
                                if (!window._notifiedSave) {
                                    window.parent.postMessage({
                                        type: 'POST_UPDATED',
                                        postId: wp.data.select('core/editor').getCurrentPostId(),
                                    }, '*');

                                    window._notifiedSave = true;
                                    setTimeout(() => { window._notifiedSave = false }, 1000); // réarmement
                                }
                            }
                        });
                    `;

                    iframe.contentWindow.eval(script);
                    console.log('Script de détection de sauvegarde injecté');
                } catch (error) {
                    console.log('Impossible d\'injecter le script de détection:', error);
                }
            }
        }

        // Injecter le script après le chargement de l'iframe
        document.getElementById('gutenberg-frame').addEventListener('load', function() {
            setTimeout(() => {
                injectCustomCSS();
                injectSaveDetectionScript();
            }, 1000); // Réduit le délai pour une meilleure expérience
        });

        // Rediriger vers le dashboard après sauvegarde si nécessaire
        function redirectToDashboard() {
            window.location.href = '{{ home_url("/editor/") }}';
        }
    </script>

    <?php wp_footer(); ?>
</body>

</html>