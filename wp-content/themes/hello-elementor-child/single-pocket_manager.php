<?php get_header(); the_post();

$has_acf   = function_exists('get_field');
$job_title = $has_acf ? get_field('pm_job_title')        : null;
$subtitle  = $has_acf ? get_field('pm_subtitle')         : null;
$photo     = $has_acf ? get_field('pm_photo')            : null;
$photo_url = is_array($photo) ? ($photo['url'] ?? null)  : $photo;
$s1        = $has_acf ? get_field('pm_da_dove_arrivo')   : null;
$s2        = $has_acf ? get_field('pm_specializzazione') : null;
$s3        = $has_acf ? get_field('pm_chi_si_chi_no')    : null;
$s4        = $has_acf ? get_field('pm_metodo_personale') : null;
$s5        = $has_acf ? get_field('pm_perche_pocket')    : null;
$s6        = $has_acf ? get_field('pm_extra')            : null;
$show_s6   = $has_acf ? get_field('pm_extra_show')       : false;
$name      = get_the_title();
$initial   = mb_strtoupper(mb_substr($name, 0, 1));
?>

<section class="pm-single-hero">
  <div class="pm-container pm-container--narrow">
    <div class="pm-single-hero__inner">

      <div class="pm-single-hero__photo" style="flex-shrink:0;">
        <?php if ($photo_url) : ?>
          <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($name); ?>">
        <?php else : ?>
          <div class="pm-initial" style="width:11rem;height:11rem;border-radius:50%;background:var(--cream);border:4px solid rgba(107,136,150,.25);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:3rem;color:var(--navy);">
            <?php echo esc_html($initial); ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="pm-single-hero__meta">
        <?php if ($job_title) : ?>
          <p class="pm-single-hero__jobtitle"><?php echo esc_html($job_title); ?></p>
        <?php endif; ?>
        <h1 class="pm-single-hero__name"><?php echo esc_html($name); ?></h1>
        <?php if ($subtitle) : ?>
          <p class="pm-single-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php
$sections = [
  ['eyebrow' => '01', 'title' => 'Da dove arrivo',                        'content' => $s1],
  ['eyebrow' => '02', 'title' => "L'area di specializzazione",            'content' => $s2],
  ['eyebrow' => '03', 'title' => 'Chi sì / chi no',                      'content' => $s3],
  ['eyebrow' => '04', 'title' => 'Il Metodo Pocket, declinato su di me', 'content' => $s4],
  ['eyebrow' => '05', 'title' => 'Perché ho scelto Pocket',               'content' => $s5],
];
if ($show_s6 && $s6) {
  $sections[] = ['eyebrow' => 'EXTRA', 'title' => 'Approfondimenti', 'content' => $s6];
}

foreach ($sections as $i => $sec) :
  if (! $sec['content']) continue;
?>
<section class="pm-section <?php echo ($i % 2 !== 0) ? 'pm-section--alt' : ''; ?>">
  <div class="pm-container pm-container--narrow">
    <div class="pm-section__header">
      <p class="pm-section__number"><?php echo esc_html($sec['eyebrow']); ?></p>
      <h2 class="pm-section__title"><?php echo esc_html($sec['title']); ?></h2>
    </div>
    <div class="pm-section__content pm-body pm-body--lg">
      <?php echo wp_kses_post($sec['content']); ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<div class="pm-cta pm-cta--primary">
  <div class="pm-container">
    <p class="pm-cta__title">Vuoi lavorare insieme?</p>
    <a href="https://sl1nk.com/come-possiamo-aiutarti" class="pm-btn pm-btn--secondary">Come possiamo aiutarti</a>
  </div>
</div>

<?php get_footer(); ?>
