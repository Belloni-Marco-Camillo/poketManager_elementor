<?php
/**
 * Create Home page with Elementor content and set as front page.
 * Run ONCE: http://localhost:8888/pocket-manager/wp-content/themes/hello-elementor-child/build-homepage.php
 */
require_once dirname(__FILE__, 4) . '/wp-load.php';
if (php_sapi_name() !== 'cli' && ! current_user_can('manage_options')) wp_die('No.');

global $wpdb;

// ── Helpers ───────────────────────────────────────────────────────────────
function uid(): string { return substr(md5(uniqid('', true)), 0, 7); }

function cont(array $settings, array $children = []): array {
    return ['id' => uid(), 'elType' => 'container', 'settings' => $settings, 'elements' => $children, 'isInner' => false];
}
function w(string $type, array $settings): array {
    return ['id' => uid(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => []];
}
function section(array $children, array $extra = []): array {
    return cont(array_merge(['content_width' => 'full', 'flex_direction' => 'column', 'align_items' => 'center'], $extra), $children);
}
function inner(array $children, array $extra = []): array {
    return cont(array_merge([
        'content_width' => 'boxed', 'flex_direction' => 'column',
        'padding' => ['unit'=>'px','top'=>'0','right'=>'24','bottom'=>'0','left'=>'24','isLinked'=>false],
        'width'   => ['unit'=>'px','size'=>1152],
    ], $extra), $children);
}
function narrow(array $children, array $extra = []): array {
    return cont(array_merge([
        'content_width' => 'boxed', 'flex_direction' => 'column',
        'padding' => ['unit'=>'px','top'=>'0','right'=>'24','bottom'=>'0','left'=>'24','isLinked'=>false],
        'width'   => ['unit'=>'px','size'=>768],
    ], $extra), $children);
}
function eyebrow(string $text, string $align = 'center'): array {
    return w('heading', [
        'title' => $text, 'header_size' => 'p', 'align' => $align,
        'title_color' => '#6B8896',
        'typography_typography' => 'custom', 'typography_font_family' => 'Marcellus SC',
        'typography_font_size' => ['unit'=>'px','size'=>12],
        'typography_font_weight' => '400', 'typography_letter_spacing' => ['unit'=>'em','size'=>0.1],
        'typography_text_transform' => 'uppercase',
        '_margin' => ['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'8','left'=>'0','isLinked'=>false],
    ]);
}
function heading(string $text, string $tag = 'h2', string $align = 'center', string $color = '#171E34', int $size = 36): array {
    return w('heading', [
        'title' => $text, 'header_size' => $tag, 'align' => $align, 'title_color' => $color,
        'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
        'typography_font_size' => ['unit'=>'px','size'=>$size], 'typography_font_weight' => '700',
        'typography_line_height' => ['unit'=>'em','size'=>1.2],
        '_margin' => ['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'16','left'=>'0','isLinked'=>false],
    ]);
}
function body_text(string $html, string $align = 'left', int $size = 17): array {
    return w('text-editor', [
        'editor' => $html, 'align' => $align, 'text_color' => '#4C4E5A',
        'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
        'typography_font_size' => ['unit'=>'px','size'=>$size],
        'typography_line_height' => ['unit'=>'em','size'=>1.75],
    ]);
}
function subtitle(string $text, string $align = 'center'): array {
    return w('heading', [
        'title' => $text, 'header_size' => 'h3', 'align' => $align, 'title_color' => '#1C445E',
        'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
        'typography_font_size' => ['unit'=>'px','size'=>22],
        'typography_font_weight' => '400', 'typography_font_style' => 'italic',
        '_margin' => ['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'32','left'=>'0','isLinked'=>false],
    ]);
}
function btn(string $label, string $url, string $bg = '#171E34', string $color = '#E9E3DA'): array {
    return w('button', [
        'text' => $label, 'link' => ['url'=>$url,'is_external'=>false], 'align' => 'center',
        'background_color' => $bg, 'button_text_color' => $color,
        'border_radius' => ['unit'=>'px','top'=>'4','right'=>'4','bottom'=>'4','left'=>'4','isLinked'=>true],
        'text_padding' => ['unit'=>'px','top'=>'14','right'=>'32','bottom'=>'14','left'=>'32','isLinked'=>false],
        'typography_typography' => 'custom', 'typography_font_family' => 'Marcellus SC',
        'typography_font_size' => ['unit'=>'px','size'=>13], 'typography_letter_spacing' => ['unit'=>'em','size'=>0.07],
    ]);
}
function shortcode(string $code): array {
    return w('shortcode', ['shortcode' => $code]);
}
function pillar_card(string $eyebrow_t, string $title_t, string $text): array {
    return cont([
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'border_border' => 'solid', 'border_width' => ['unit'=>'px','top'=>'1','right'=>'1','bottom'=>'1','left'=>'1','isLinked'=>true],
        'border_color' => '#E9E3DA', 'border_radius' => ['unit'=>'px','top'=>'8','right'=>'8','bottom'=>'8','left'=>'8','isLinked'=>true],
        'padding' => ['unit'=>'px','top'=>'32','right'=>'32','bottom'=>'32','left'=>'32','isLinked'=>false],
        'flex_direction' => 'column',
    ], [
        eyebrow($eyebrow_t, 'left'),
        heading($title_t, 'h3', 'left', '#171E34', 22),
        body_text($text, 'left', 15),
    ]);
}

// ── Build homepage elements ───────────────────────────────────────────────
$elements = [

    // 1. Hero
    section([
        narrow([
            eyebrow('POCKET MANAGER'),
            heading('Strategia di business per chi lavora in proprio', 'h1', 'center', '#171E34', 48),
            subtitle('Competenze manageriali accessibili. Per professionisti e piccole imprese che vogliono crescere con metodo.'),
            cont(['flex_direction'=>'row','align_items'=>'center','justify_content'=>'center','gap'=>['unit'=>'px','size'=>16],'flex_wrap'=>'wrap'], [
                btn('Scopri il Metodo', home_url('/metodo-pocket/'), '#171E34', '#E9E3DA'),
                btn('Lavoriamo insieme', 'https://sl1nk.com/come-possiamo-aiutarti', 'transparent', '#171E34'),
            ]),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic',
        'background_color'      => '#E9E3DA',
        'padding' => ['unit'=>'px','top'=>'120','right'=>'0','bottom'=>'120','left'=>'0','isLinked'=>false],
    ]),

    // 2. Intro
    section([
        narrow([
            eyebrow('IL PROBLEMA'),
            heading('La strategia non è un lusso — è una necessità.', 'h2', 'center', '#171E34', 34),
            body_text('<p>Per troppo tempo gli strumenti di management sono stati pensati solo per le grandi aziende. Pocket Manager nasce per cambiare questo: rendere la consulenza strategica <strong>immediata, accessibile e concreta</strong> per chi lavora in proprio.</p>', 'center', 17),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'padding' => ['unit'=>'px','top'=>'96','right'=>'0','bottom'=>'96','left'=>'0','isLinked'=>false],
    ]),

    // 3. Tre pilastri
    section([
        inner([
            eyebrow('IL METODO'),
            heading('Small · Smart · Slow', 'h2', 'center', '#171E34', 34),
            body_text('<p>Tre parole che sintetizzano una filosofia: lavorare su ciò che conta davvero, con lucidità e senza fretta.</p>', 'center', 16),
        ], ['align_items'=>'center','_margin'=>['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'40','left'=>'0','isLinked'=>false]]),
        inner([
            pillar_card('SMALL', 'Dimensione è una scelta', 'Piccolo non è un limite, è una posizione strategica. Lavorare su scala ridotta permette flessibilità, qualità e relazioni autentiche.'),
            pillar_card('SMART', 'Chiarezza prima di tutto', 'Decisioni basate su dati, non su istinto. Strumenti semplici ma potenti per capire dove sei e dove vuoi andare.'),
            pillar_card('SLOW', 'Crescita sostenibile', 'Costruire qualcosa che duri nel tempo. Senza accelerare a tutti i costi, ma con una direzione chiara e passi concreti.'),
        ], ['flex_direction'=>'row','gap'=>['unit'=>'px','size'=>24],'align_items'=>'stretch']),
    ], [
        'background_background' => 'classic', 'background_color' => '#F7F4F0',
        'padding' => ['unit'=>'px','top'=>'96','right'=>'0','bottom'=>'96','left'=>'0','isLinked'=>false],
    ]),

    // 4. Team preview
    section([
        inner([
            eyebrow('IL TEAM'),
            heading('I tuoi Pocket Manager', 'h2', 'center', '#171E34', 34),
            body_text('<p>Professionisti con esperienza manageriale reale, pronti a lavorare al tuo fianco.</p>', 'center', 16),
            shortcode('[pm_team type="all" cols="3"]'),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'padding' => ['unit'=>'px','top'=>'96','right'=>'0','bottom'=>'96','left'=>'0','isLinked'=>false],
    ]),

    // 5. CTA finale
    section([
        narrow([
            heading('Pronto a fare chiarezza nel tuo business?', 'h2', 'center', '#E9E3DA', 32),
            cont(['flex_direction'=>'row','align_items'=>'center','justify_content'=>'center','gap'=>['unit'=>'px','size'=>16],'flex_wrap'=>'wrap'], [
                btn('Lavoriamo insieme', 'https://sl1nk.com/come-possiamo-aiutarti', '#E9E3DA', '#171E34'),
                btn('Scopri il Metodo', home_url('/metodo-pocket/'), 'transparent', '#E9E3DA'),
            ]),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic', 'background_color' => '#171E34',
        'padding' => ['unit'=>'px','top'=>'100','right'=>'0','bottom'=>'100','left'=>'0','isLinked'=>false],
    ]),
];

// ── Create or find page ───────────────────────────────────────────────────
$existing = get_page_by_path('home-page', OBJECT, 'page');
if ($existing) {
    $page_id = $existing->ID;
} else {
    $page_id = wp_insert_post([
        'post_title'  => 'Home',
        'post_name'   => 'home-page',
        'post_status' => 'publish',
        'post_type'   => 'page',
    ]);
}

if (is_wp_error($page_id) || ! $page_id) {
    die('Errore creazione pagina: ' . (is_wp_error($page_id) ? $page_id->get_error_message() : 'ID nullo'));
}

// ── Save Elementor data (direct SQL to bypass wp_unslash) ─────────────────
$json = wp_json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$exists = $wpdb->get_var($wpdb->prepare(
    "SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_elementor_data'",
    $page_id
));
if ($exists) {
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value=%s WHERE post_id=%d AND meta_key='_elementor_data'",
        $json, $page_id
    ));
} else {
    $wpdb->insert($wpdb->postmeta, ['post_id'=>$page_id,'meta_key'=>'_elementor_data','meta_value'=>$json]);
}

update_post_meta($page_id, '_elementor_edit_mode', 'builder');
update_post_meta($page_id, '_elementor_template_type', 'wp-page');
delete_post_meta($page_id, '_elementor_css');

// ── Set as static front page ──────────────────────────────────────────────
update_option('show_on_front', 'page');
update_option('page_on_front', $page_id);

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Homepage</title>
<style>body{font-family:sans-serif;padding:2rem;background:#E9E3DA;color:#171E34}
.ok{color:#2e7d32;font-weight:bold;font-size:1.1rem}</style></head><body>';
echo '<p class="ok">✅ Homepage creata (ID ' . $page_id . ') e impostata come front page.</p>';
echo '<p><a href="' . home_url('/') . '">→ Vai alla homepage</a></p>';
echo '</body></html>';
