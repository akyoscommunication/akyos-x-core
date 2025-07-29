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

        /* Styles pour la modale de prévisualisation */
        .preview-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .preview-modal.active {
            display: flex;
        }

        .preview-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 1200px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .preview-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .preview-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .preview-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .preview-iframe {
            flex: 1;
            width: 100%;
            border: none;
            border-radius: 0 0 12px 12px;
        }

        .preview-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: #f9fafb;
        }

        .preview-loading .loading-spinner {
            width: 40px;
            height: 40px;
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
                        <!-- Bouton de prévisualisation -->
                        <button id="preview-button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" onclick="openPreview()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Prévisualiser
                        </button>

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

    <!-- Modale de prévisualisation -->
    <div id="preview-modal" class="preview-modal">
        <div class="preview-content">
            <div class="preview-header">
                <h3>Prévisualisation : {{ $post->post_title }}</h3>
                <button class="preview-close" onclick="closePreview()">&times;</button>
            </div>
            <div id="preview-loading" class="preview-loading">
                <div class="loading-spinner"></div>
            </div>
            <iframe id="preview-iframe" class="preview-iframe" style="display: none;"></iframe>
        </div>
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

                    showStatus('Sauvegarde effectuée avec succès', 'success');
                    const saveButton = document.getElementById('save-button');
                    const saveText = document.getElementById('save-text');
                    if (saveButton) {
                        saveButton.disabled = false;
                        saveText.textContent = 'Enregistrer';
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


        function showStatus(message, type) {
            const statusEl = document.getElementById('status-message');
            statusEl.textContent = message;
            statusEl.className = 'status-message status-' + type;
            statusEl.style.display = 'block';

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

        // Injecter le script après le chargement de l'iframe
        document.getElementById('gutenberg-frame').addEventListener('load', function() {
            setTimeout(() => {
                injectCustomCSS();
            }, 1000); // Réduit le délai pour une meilleure expérience
        });

        // Rediriger vers le dashboard après sauvegarde si nécessaire
        function redirectToDashboard() {
            window.location.href = '{{ home_url("/editor/") }}';
        }

        // Fonction pour ouvrir la prévisualisation
        function openPreview() {
            const modal = document.getElementById('preview-modal');
            const iframe = document.getElementById('preview-iframe');
            const loading = document.getElementById('preview-loading');
            const previewButton = document.getElementById('preview-button');
            const gutenbergFrame = document.getElementById('gutenberg-frame');

            // Désactiver le bouton de prévisualisation
            previewButton.disabled = true;
            previewButton.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Préparation de la prévisualisation...
            `;

            // Afficher la modale
            modal.classList.add('active');

            // Utiliser la prévisualisation native de WordPress
            getWordPressPreviewUrl()
                .then(previewUrl => {
                    console.log('🔗 URL de prévisualisation WordPress:', previewUrl);
                    loadPreviewInIframe(previewUrl, previewButton, modal, iframe, loading);
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la génération de l\'URL de prévisualisation:', error);
                    showStatus('Erreur lors de la préparation de la prévisualisation', 'error');

                    // Réactiver le bouton de prévisualisation
                    previewButton.disabled = false;
                    previewButton.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Prévisualiser
                    `;
                });
        }

        // Obtenir l'URL de prévisualisation WordPress native
        function getWordPressPreviewUrl() {
            return new Promise((resolve, reject) => {
                const gutenbergFrame = document.getElementById('gutenberg-frame');
                let timeoutId;

                if (gutenbergFrame && gutenbergFrame.contentWindow) {
                    try {
                        // Créer un gestionnaire temporaire pour cette requête
                        const messageHandler = function(event) {
                            if (event.data && event.data.type === 'WORDPRESS_PREVIEW_URL') {
                                clearTimeout(timeoutId);
                                window.removeEventListener('message', messageHandler);
                                resolve(event.data.url);
                            } else if (event.data && event.data.type === 'WORDPRESS_PREVIEW_ERROR') {
                                clearTimeout(timeoutId);
                                window.removeEventListener('message', messageHandler);
                                reject(new Error(event.data.error));
                            }
                        };

                        // Ajouter l'écouteur temporaire
                        window.addEventListener('message', messageHandler);

                        // Timeout de sécurité (15 secondes pour la prévisualisation)
                        timeoutId = setTimeout(() => {
                            window.removeEventListener('message', messageHandler);
                            reject(new Error('Timeout lors de la génération de l\'URL de prévisualisation'));
                        }, 15000);

                        // Script pour utiliser le système de prévisualisation natif de Gutenberg
                        const homeUrl = '{{ home_url("/") }}';
                        const scriptContent = [
                            'try {',
                            '    console.log("🔧 Début de la génération de prévisualisation Gutenberg");',
                            '    ',
                            '    // Récupérer les données du post en cours d\'édition',
                            '    const postId = wp.data.select("core/editor").getCurrentPostId();',
                            '    const title = wp.data.select("core/editor").getEditedPostAttribute("title") || "";',
                            '    const content = wp.data.select("core/editor").getEditedPostAttribute("content") || "";',
                            '    ',
                            '    console.log("📝 Données récupérées:", { postId, title: title.substring(0, 50) + "..." });',
                            '    ',
                            '    // Méthode 1 : Essayer de récupérer le lien de prévisualisation depuis l\'interface',
                            '    const previewButton = document.querySelector(\'button[aria-label="Preview"]\') || document.querySelector(\'button[data-label="Preview"]\');',
                            '    if (previewButton && previewButton.href) {',
                            '        console.log("🔗 URL de prévisualisation trouvée via bouton:", previewButton.href);',
                            '        window.parent.postMessage({',
                            '            type: "WORDPRESS_PREVIEW_URL",',
                            '            url: previewButton.href',
                            '        }, "*");',
                            '    } else {',
                            '    ',
                            '    // Méthode 2 : Utiliser l\'API de prévisualisation de Gutenberg',
                            '    wp.data.dispatch("core/editor").savePost({ isPreview: true }).then(() => {',
                            '        console.log("✅ Post sauvegardé pour prévisualisation");',
                            '        ',
                            '        // Récupérer l\'URL de prévisualisation générée',
                            '        const post = wp.data.select("core/editor").getCurrentPost();',
                            '        const previewLink = post._links?.preview?.[0]?.href;',
                            '        ',
                            '        if (previewLink) {',
                            '            console.log("🔗 URL de prévisualisation trouvée:", previewLink);',
                            '            window.parent.postMessage({',
                            '                type: "WORDPRESS_PREVIEW_URL",',
                            '                url: previewLink',
                            '            }, "*");',
                            '        } else {',
                            '            // Méthode 3 : Essayer de récupérer depuis les métadonnées du post',
                            '            const previewUrl = post.meta?._preview_url || post._preview_url;',
                            '            if (previewUrl) {',
                            '                console.log("🔗 URL de prévisualisation trouvée dans les métadonnées:", previewUrl);',
                            '                window.parent.postMessage({',
                            '                    type: "WORDPRESS_PREVIEW_URL",',
                            '                    url: previewUrl',
                            '                }, "*");',
                            '            } else {',
                            '                // Fallback : construire l\'URL manuellement',
                            '                const fallbackUrl = "' + homeUrl + '?p=" + postId + "&preview=true";',
                            '                console.log("🔗 URL de prévisualisation fallback:", fallbackUrl);',
                            '                window.parent.postMessage({',
                            '                    type: "WORDPRESS_PREVIEW_URL",',
                            '                    url: fallbackUrl',
                            '                }, "*");',
                            '            }',
                            '        }',
                            '    }).catch((error) => {',
                            '        console.error("❌ Erreur lors de la sauvegarde pour prévisualisation:", error);',
                            '        ',
                            '        // En cas d\'erreur, essayer de récupérer le lien de prévisualisation existant',
                            '        const post = wp.data.select("core/editor").getCurrentPost();',
                            '        const existingPreviewLink = post._links?.preview?.[0]?.href;',
                            '        ',
                            '        if (existingPreviewLink) {',
                            '            console.log("🔗 URL de prévisualisation existante trouvée:", existingPreviewLink);',
                            '            window.parent.postMessage({',
                            '                type: "WORDPRESS_PREVIEW_URL",',
                            '                url: existingPreviewLink',
                            '            }, "*");',
                            '        } else {',
                            '            window.parent.postMessage({',
                            '                type: "WORDPRESS_PREVIEW_ERROR",',
                            '                error: error.message',
                            '            }, "*");',
                            '        }',
                            '    });',
                            '    }',
                            '    ',
                            '} catch (error) {',
                            '    console.error("❌ Erreur lors de la génération de l\'URL:", error);',
                            '    window.parent.postMessage({',
                            '        type: "WORDPRESS_PREVIEW_ERROR",',
                            '        error: error.message',
                            '    }, "*");',
                            '}'
                        ].join('\n');

                        gutenbergFrame.contentWindow.eval(scriptContent);
                    } catch (error) {
                        clearTimeout(timeoutId);
                        console.error('❌ Erreur lors de l\'accès à Gutenberg:', error);
                        reject(error);
                    }
                } else {
                    reject(new Error('Frame Gutenberg non disponible'));
                }
            });
        }

        // Charger la prévisualisation dans l'iframe (fallback)
        function loadPreviewInIframe(previewUrl, previewButton, modal, iframe, loading) {
            iframe.src = previewUrl;
            iframe.style.display = 'none';
            loading.style.display = 'flex';

            iframe.onload = function() {
                loading.style.display = 'none';
                iframe.style.display = 'block';

                // Réactiver le bouton de prévisualisation
                previewButton.disabled = false;
                previewButton.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Prévisualiser
                `;
            };
        }

        // Fonction pour fermer la prévisualisation
        function closePreview() {
            const modal = document.getElementById('preview-modal');
            const iframe = document.getElementById('preview-iframe');
            const loading = document.getElementById('preview-loading');

            // Masquer la modale
            modal.classList.remove('active');

            // Réinitialiser l'iframe
            iframe.src = '';
            iframe.style.display = 'none';
            loading.style.display = 'flex';
        }

        // Générer un token temporaire pour la prévisualisation
        function generatePreviewToken() {
            return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        }

        // Fermer la prévisualisation en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('preview-modal');
            if (event.target === modal) {
                closePreview();
            }
        });

        // Fermer la prévisualisation avec la touche Échap
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('preview-modal');
                if (modal.classList.contains('active')) {
                    closePreview();
                }
            }
        });

        // Gestion des messages postMessage depuis Gutenberg
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'POST_UPDATED') {
                // Gérer la notification de sauvegarde
                showStatus('Contenu sauvegardé avec succès !', 'success');
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>

</html>