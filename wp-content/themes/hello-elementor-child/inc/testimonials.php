<?php

// ── CPT: pm_testimonial ────────────────────────────────────────────────────
add_action('init', function (): void {
    register_post_type('pm_testimonial', [
        'labels' => [
            'name'               => 'Testimonianze',
            'singular_name'      => 'Testimonianza',
            'add_new'            => 'Nuova',
            'add_new_item'       => 'Aggiungi Testimonianza',
            'edit_item'          => 'Modifica Testimonianza',
            'all_items'          => 'Tutte le Testimonianze',
            'not_found'          => 'Nessuna testimonianza trovata',
            'not_found_in_trash' => 'Nessuna testimonianza nel cestino',
            'menu_name'          => 'Testimonianze',
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => false,
        'menu_icon'          => 'dashicons-format-quote',
        'menu_position'      => 6,
        'supports'           => ['title', 'page-attributes'],
        'rewrite'            => false,
    ]);
});

// ── Meta box ───────────────────────────────────────────────────────────────
add_action('add_meta_boxes', function (): void {
    add_meta_box(
        'pm_testi_fields',
        'Dettagli testimonianza',
        'pm_testi_meta_box_cb',
        'pm_testimonial',
        'normal',
        'high'
    );
});

function pm_testi_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('pm_testi_save', 'pm_testi_nonce');
    $quote = get_post_meta($post->ID, '_pm_testi_quote', true);
    $role  = get_post_meta($post->ID, '_pm_testi_role',  true);
    ?>
    <table class="form-table">
      <tr>
        <th style="width:110px;padding:12px 0"><label for="pm_testi_quote"><strong>Testo</strong></label></th>
        <td>
          <textarea id="pm_testi_quote" name="pm_testi_quote" rows="5"
            style="width:100%;font-size:14px;line-height:1.6"><?php echo esc_textarea($quote); ?></textarea>
          <p class="description">Il corpo della testimonianza.</p>
        </td>
      </tr>
      <tr>
        <th style="padding:12px 0"><label for="pm_testi_role"><strong>Professione</strong></label></th>
        <td>
          <input type="text" id="pm_testi_role" name="pm_testi_role"
            value="<?php echo esc_attr($role); ?>" style="width:100%;font-size:14px">
          <p class="description">Es. Consulente, Imprenditrice, Architetto…</p>
        </td>
      </tr>
    </table>
    <p class="description" style="margin-top:8px">
      Il campo <strong>Titolo</strong> in alto è il nome dell'autore.<br>
      Usa <strong>Ordine</strong> (in basso a destra) per cambiare la sequenza nel nastro.
    </p>
    <?php
}

add_action('save_post_pm_testimonial', function (int $post_id): void {
    if (
        ! isset($_POST['pm_testi_nonce']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pm_testi_nonce'])), 'pm_testi_save') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        ! current_user_can('edit_post', $post_id)
    ) return;

    if (isset($_POST['pm_testi_quote'])) {
        update_post_meta($post_id, '_pm_testi_quote', sanitize_textarea_field(wp_unslash($_POST['pm_testi_quote'])));
    }
    if (isset($_POST['pm_testi_role'])) {
        update_post_meta($post_id, '_pm_testi_role', sanitize_text_field(wp_unslash($_POST['pm_testi_role'])));
    }
});

// ── [pm_testimonials] shortcode ────────────────────────────────────────────
add_shortcode('pm_testimonials', function (): string {
    $query = new WP_Query([
        'post_type'      => 'pm_testimonial',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    if (! $query->have_posts()) return '';

    $cards = '';
    while ($query->have_posts()) {
        $query->the_post();
        $quote = get_post_meta(get_the_ID(), '_pm_testi_quote', true);
        $role  = get_post_meta(get_the_ID(), '_pm_testi_role',  true);
        $name  = get_the_title();
        if (! $quote) continue;
        $cards .= '<div class="pmt-item">'
            . '<p class="pmt-quote">&#8220;' . esc_html($quote) . '&#8221;</p>'
            . '<span class="pmt-author">— ' . esc_html($name)
            . ($role ? ', <em>' . esc_html($role) . '</em>' : '')
            . '</span>'
            . '</div>';
    }
    wp_reset_postdata();

    if (! $cards) return '';

    $css = '<style>
.pmt-wrap{overflow:hidden;position:relative}
.pmt-track{display:flex;gap:24px;width:max-content;animation:pmt-scroll 50s linear infinite}
.pmt-wrap:hover .pmt-track{animation-play-state:paused}
.pmt-item{width:300px;flex-shrink:0;background:#F7F4F0;border:1px solid #E9E3DA;border-radius:8px;padding:28px 24px;display:flex;flex-direction:column;gap:16px}
.pmt-quote{font-family:"Playfair Display",serif;font-size:15px;line-height:1.7;color:#4C4E5A;font-style:italic;margin:0}
.pmt-author{font-family:"Marcellus SC",sans-serif;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#6B8896}
@keyframes pmt-scroll{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
</style>';

    return $css . '<div class="pmt-wrap"><div class="pmt-track">' . $cards . $cards . '</div></div>';
});
