<?php
/**
 * Create Home page with Elementor content and set as front page.
 * Run ONCE: http://localhost:8888/pocket-manager/wp-content/themes/hello-elementor-child/build-homepage.php
 */
require_once dirname(__FILE__, 4) . '/wp-load.php';
if (php_sapi_name() !== 'cli' && ! current_user_can('manage_options')) wp_die('No.');

global $wpdb;

// ── Seed testimonianze (solo se non esistono) ─────────────────────────────
$seed_testi = [
    ['name' => 'Marco R.',   'role' => 'Consulente',            'quote' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.'],
    ['name' => 'Giulia T.',  'role' => 'Imprenditrice',         'quote' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.'],
    ['name' => 'Luca M.',    'role' => 'Libero professionista', 'quote' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'],
    ['name' => 'Sara F.',    'role' => 'Artigiana',             'quote' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium totam rem.'],
    ['name' => 'Davide C.',  'role' => 'Commerciante',          'quote' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores.'],
    ['name' => 'Anna B.',    'role' => 'Freelance',             'quote' => 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet consectetur adipisci velit sed quia.'],
    ['name' => 'Roberto V.', 'role' => 'Titolare PMI',          'quote' => 'Ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam quis nostrum exercitationem.'],
    ['name' => 'Chiara P.',  'role' => 'Studio legale',         'quote' => 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.'],
    ['name' => 'Filippo S.', 'role' => 'Architetto',            'quote' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque.'],
    ['name' => 'Elena G.',   'role' => 'Coach',                 'quote' => 'Nam libero tempore cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime.'],
];
$existing_testi = get_posts(['post_type' => 'pm_testimonial', 'posts_per_page' => 1, 'post_status' => 'publish']);
if (empty($existing_testi)) {
    foreach ($seed_testi as $i => $t) {
        $pid = wp_insert_post(['post_title' => $t['name'], 'post_type' => 'pm_testimonial', 'post_status' => 'publish', 'menu_order' => $i]);
        if ($pid && ! is_wp_error($pid)) {
            update_post_meta($pid, '_pm_testi_quote', $t['quote']);
            update_post_meta($pid, '_pm_testi_role',  $t['role']);
        }
    }
}

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

    // 1. Pitch
    section([
        narrow([
            heading('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'h1', 'center', '#171E34', 48),
            subtitle('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.'),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic',
        'background_color'      => '#E9E3DA',
        'padding' => ['unit'=>'px','top'=>'64','right'=>'0','bottom'=>'64','left'=>'0','isLinked'=>false],
    ]),

    // 2. Quiz
    section([
        inner([
            shortcode('[pm_quiz]'),
        ], ['align_items'=>'center']),
    ], [
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'padding' => ['unit'=>'px','top'=>'80','right'=>'0','bottom'=>'80','left'=>'0','isLinked'=>false],
    ]),

    // 3. Testimonianze a nastro
    section([
        shortcode('[pm_testimonials]'),
    ], [
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'padding' => ['unit'=>'px','top'=>'64','right'=>'0','bottom'=>'64','left'=>'0','isLinked'=>false],
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
delete_post_meta($page_id, '_elementor_element_cache');
$elementor_css = WP_CONTENT_DIR . '/uploads/elementor/css/post-' . $page_id . '.css';
@unlink($elementor_css);

// ── Set as static front page ──────────────────────────────────────────────
update_option('show_on_front', 'page');
update_option('page_on_front', $page_id);

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Homepage</title>
<style>body{font-family:sans-serif;padding:2rem;background:#E9E3DA;color:#171E34}
.ok{color:#2e7d32;font-weight:bold;font-size:1.1rem}</style></head><body>';
echo '<p class="ok">✅ Homepage creata (ID ' . $page_id . ') e impostata come front page.</p>';
echo '<p><a href="' . home_url('/') . '">→ Vai alla homepage</a></p>';
echo '</body></html>';
