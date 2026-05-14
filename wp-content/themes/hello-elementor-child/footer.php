<?php $year = date('Y'); ?>

<footer class="pm-footer">
  <div class="pm-container">

    <div class="pm-footer__grid">

      <?php // Col 1: Logo + tagline + contatti ?>
      <div class="pm-footer__col--brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="pm-footer__logo">
          <?php
          if (has_custom_logo()) {
              $logo_id  = get_theme_mod('custom_logo');
              $logo_img = wp_get_attachment_image($logo_id, 'full', false, ['style' => 'height:44px;width:auto;filter:brightness(0) invert(1);']);
              echo $logo_img;
          } else {
              echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/logo-negative.png') . '" alt="' . esc_attr(get_bloginfo('name')) . '" style="height:44px;width:auto;">';
          }
          ?>
        </a>
        <p class="pm-footer__tagline">il tuo business chiaro e semplice</p>
        <div class="pm-footer__contact">
          <div class="pm-footer__contact-row">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" style="flex-shrink:0;opacity:.7;margin-top:2px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
            <span>Milano · Torino · Roma</span>
          </div>
          <div class="pm-footer__contact-row">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" style="flex-shrink:0;opacity:.7;margin-top:2px"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <a href="mailto:info@pocketmanager.it">info@pocketmanager.it</a>
          </div>
          <div class="pm-footer__contact-row">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" style="flex-shrink:0;opacity:.7;margin-top:2px"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
            <a href="tel:+393425032146">+39 342 50 32 146</a>
          </div>
        </div>
      </div>

      <?php // Col 2: Community + CTA ?>
      <div class="pm-footer__cta-col">
        <p class="pm-footer__nav-label">Community</p>
        <div class="pm-footer__social">
          <a href="#" target="_blank" rel="noopener" aria-label="Pocket Space su WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.122 1.528 5.855L.057 23.882l6.198-1.625A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.794 9.794 0 01-5.001-1.373l-.358-.214-3.718.975.991-3.62-.234-.372A9.775 9.775 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
            <span>Pocket Space</span>
          </a>
          <a href="#" target="_blank" rel="noopener" aria-label="LinkedIn aziendale">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            <span>LinkedIn</span>
          </a>
          <a href="#" target="_blank" rel="noopener" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            <span>Instagram</span>
          </a>
        </div>
        <a href="https://sl1nk.com/come-possiamo-aiutarti" class="pm-btn pm-btn--secondary" style="margin-top:1.5rem;">
          Lavoriamo insieme →
        </a>
      </div>

    </div>

    <div class="pm-footer__divider"></div>

    <div class="pm-footer__bottom">
      <p>© Copyright 2018 – <?php echo esc_html($year); ?>, tutti i diritti riservati &nbsp;|&nbsp; Pocket Manager &nbsp;|&nbsp; P.IVA: 02848210809</p>
      <p><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a></p>
    </div>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
