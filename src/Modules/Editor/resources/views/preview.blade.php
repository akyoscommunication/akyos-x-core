<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévisualisation : {{ $post->post_title }}</title>
    <?php wp_head(); ?>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9fafb;
        }

        /* Masquer l'admin bar pour la prévisualisation */
        #wpadminbar {
            display: none !important;
        }

        /* Styles pour la prévisualisation */
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .preview-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .preview-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 10px 0;
        }

        .preview-meta {
            color: #6b7280;
            font-size: 1rem;
        }
    </style>
</head>

<body>

    <div class="preview-container">
        <div class="preview-content">
            {!! apply_filters('the_content', $post->post_content) !!}
        </div>
    </div>

    <?php wp_footer(); ?>
</body>

</html>