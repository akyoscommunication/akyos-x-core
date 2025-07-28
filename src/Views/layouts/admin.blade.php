<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <title>@yield('title') - <?php bloginfo('name'); ?></title>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="wpcontent">
        <div id="wpbody" role="main">
            <div id="wpbody-content">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
    <?php wp_footer(); ?>
</body>

</html>