<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="pm-header">
  <div class="pm-container pm-header__inner">

    <?php // Logo ?>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="pm-header__logo" aria-label="<?php bloginfo('name'); ?> — Home">
      <?php
      if (has_custom_logo()) {
          $logo_id  = get_theme_mod('custom_logo');
          $logo_img = wp_get_attachment_image($logo_id, 'full', false, ['style' => 'height:44px;width:auto;']);
          echo $logo_img;
      } else {
          echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/logo.png') . '" alt="' . esc_attr(get_bloginfo('name')) . '" style="height:44px;width:auto;">';
      }
      ?>
    </a>

    <div class="pm-header__right">
      <a href="https://sl1nk.com/come-possiamo-aiutarti" class="pm-header__cta">Lavora con noi</a>
      <button id="pm-menu-toggle" class="pm-hamburger" aria-label="Apri menu" aria-expanded="false">
        <span id="pm-bar1"></span>
        <span id="pm-bar2"></span>
        <span id="pm-bar3"></span>
      </button>
    </div>

  </div>
</header>

<?php // Fullscreen nav overlay ?>
<div id="pm-overlay" class="pm-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu">
  <nav class="pm-overlay__nav" aria-label="Menu principale">
    <?php
    wp_nav_menu([
      'theme_location' => 'primary_navigation',
      'container'      => false,
      'items_wrap'     => '<ul>%3$s</ul>',
      'fallback_cb'    => false,
    ]);
    ?>
  </nav>
</div>
