<?php get_header(); ?>

<section class="pm-archive-hero">
  <div class="pm-container pm-container--narrow" style="text-align:center;">
    <p class="pm-eyebrow">IL TEAM</p>
    <h1 class="pm-h1">I nostri Pocket Manager</h1>
    <p class="pm-body pm-body--lg" style="margin-top:1.5rem;max-width:560px;margin-inline:auto;color:var(--slate);">
      Tutti i consulenti e le fondatrici della rete Pocket Manager.
    </p>
  </div>
</section>

<section class="pm-archive-grid">
  <div class="pm-container pm-container--wide">
    <?php if (have_posts()) : ?>
      <div class="pm-archive-grid__inner">
        <?php while (have_posts()) : the_post();
          $photo     = function_exists('get_field') ? get_field('pm_photo') : null;
          $photo_url = is_array($photo) ? ($photo['url'] ?? null) : $photo;
          $name      = get_the_title();
          $initial   = mb_strtoupper(mb_substr($name, 0, 1));
          $job_title = function_exists('get_field') ? get_field('pm_job_title') : null;
        ?>
          <a href="<?php the_permalink(); ?>" class="pm-member-card">
            <div class="pm-member-card__photo">
              <?php if ($photo_url) : ?>
                <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($name); ?>">
              <?php else : ?>
                <div class="pm-initial"><?php echo esc_html($initial); ?></div>
              <?php endif; ?>
            </div>
            <h2 class="pm-member-card__name"><?php echo esc_html($name); ?></h2>
            <?php if ($job_title) : ?>
              <p class="pm-member-card__title"><?php echo esc_html($job_title); ?></p>
            <?php endif; ?>
          </a>
        <?php endwhile; ?>
      </div>
    <?php else : ?>
      <p style="text-align:center;font-style:italic;color:rgba(76,78,90,.5);">Nessun Pocket Manager ancora — aggiungili dal backend.</p>
    <?php endif; ?>
  </div>
</section>

<div class="pm-cta pm-cta--secondary">
  <div class="pm-container">
    <p class="pm-cta__title">Vuoi unirti a noi?</p>
    <a href="<?php echo esc_url(home_url('/franchising')); ?>" class="pm-btn pm-btn--outline">Scopri il franchising</a>
  </div>
</div>

<?php get_footer(); ?>
