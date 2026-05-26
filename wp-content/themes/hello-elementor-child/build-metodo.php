<?php
/**
 * Rebuild metodo-pocket page (ID 17) with Elementor content.
 * Run: /Applications/MAMP/bin/php/php8.2.26/bin/php build-metodo.php
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
function white_text(string $html, string $align = 'left', int $size = 17): array {
    return w('text-editor', [
        'editor' => $html, 'align' => $align, 'text_color' => '#FFFFFF',
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
function col(array $children): array {
    return cont([
        'content_width' => 'full', 'flex_direction' => 'column',
        'width' => ['unit'=>'%','size'=>50], 'align_self' => 'flex-start',
    ], $children);
}
function row(array $children, int $gap = 24, array $extra = []): array {
    return cont(array_merge([
        'flex_direction' => 'row',
        'gap' => ['unit'=>'px','size'=>$gap,'column'=>(string)$gap,'row'=>(string)$gap,'isLinked'=>true],
    ], $extra), $children);
}
function card(array $children, string $bg = '#ffffff', int $pad = 24, int $radius = 6): array {
    return cont([
        'flex_direction' => 'column',
        'background_background' => 'classic', 'background_color' => $bg,
        'border_radius' => ['unit'=>'px','top'=>(string)$radius,'right'=>(string)$radius,'bottom'=>(string)$radius,'left'=>(string)$radius,'isLinked'=>true],
        'padding' => ['unit'=>'px','top'=>(string)$pad,'right'=>(string)$pad,'bottom'=>(string)$pad,'left'=>(string)$pad,'isLinked'=>true],
    ], $children);
}
function pillar_card_metodo(string $eyebrow_t, string $title_t, string $text, string $bg = '#f5f3f0', string $eyebrow_color = '#6B8896'): array {
    return cont([
        'flex_direction' => 'column',
        'background_background' => 'classic', 'background_color' => $bg,
        'border_radius' => ['unit'=>'px','top'=>'6','right'=>'6','bottom'=>'6','left'=>'6','isLinked'=>true],
        'padding' => ['unit'=>'px','top'=>'32','right'=>'28','bottom'=>'32','left'=>'28','isLinked'=>false],
    ], [
        w('heading', [
            'title' => $eyebrow_t, 'header_size' => 'p', 'align' => 'left',
            'title_color' => $eyebrow_color,
            'typography_typography' => 'custom', 'typography_font_family' => 'Marcellus SC',
            'typography_font_size' => ['unit'=>'px','size'=>12],
            'typography_font_weight' => '400', 'typography_letter_spacing' => ['unit'=>'em','size'=>0.1],
            'typography_text_transform' => 'uppercase',
            '_margin' => ['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'8','left'=>'0','isLinked'=>false],
        ]),
        heading($title_t, 'h3', 'left', '#171E34', 22),
        body_text($text, 'left', 15),
    ]);
}
function phase_card(string $num, string $title_t, string $text): array {
    return cont([
        'flex_direction' => 'column',
        'background_background' => 'classic', 'background_color' => '#ffffff',
        'border_radius' => ['unit'=>'px','top'=>'6','right'=>'6','bottom'=>'6','left'=>'6','isLinked'=>true],
        'padding' => ['unit'=>'px','top'=>'28','right'=>'24','bottom'=>'28','left'=>'24','isLinked'=>false],
    ], [
        w('heading', [
            'title' => $num, 'header_size' => 'p', 'align' => 'left',
            'title_color' => '#F3C244',
            'typography_typography' => 'custom', 'typography_font_family' => 'Marcellus SC',
            'typography_font_size' => ['unit'=>'px','size'=>13],
            'typography_font_weight' => '400', 'typography_letter_spacing' => ['unit'=>'em','size'=>0.1],
            'typography_text_transform' => 'uppercase',
            '_margin' => ['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'8','left'=>'0','isLinked'=>false],
        ]),
        heading($title_t, 'h3', 'left', '#171E34', 18),
        body_text($text, 'left', 15),
    ]);
}

// ── Build page elements ───────────────────────────────────────────────────
$pad80  = ['unit'=>'px','top'=>'80','right'=>'0','bottom'=>'80','left'=>'0','isLinked'=>false];
$pad100 = ['unit'=>'px','top'=>'100','right'=>'0','bottom'=>'100','left'=>'0','isLinked'=>false];

$elements = [

    // ── 0. Hero ──────────────────────────────────────────────────────────
    section([
        narrow([
            eyebrow('IL METODO'),
            heading('Small, Smart, Slow Business Strategy', 'h1', 'center', '#171E34', 48),
            subtitle('Il Metodo Pocket nasce per rompere lo schema della strategia riservata alle grandi aziende.'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#E9E3DA','padding'=>$pad100]),

    // ── 1. Introduzione (sfondo navy, due colonne) ────────────────────────
    section([
        narrow([
            col([
                white_text('<p>Il Metodo Pocket nasce da una constatazione semplice, ma spesso ignorata: <strong>la strategia di business non è un lusso</strong>, e non dovrebbe essere riservata solo alle grandi aziende.</p><p>Eppure, per professionisti e piccoli imprenditori, è spesso così. Gli strumenti di management sono costosi, complessi, pensati per strutture che non hanno nulla a che vedere con una micro-impresa. Il risultato? Si cresce &ldquo;a intuito&rdquo;, si decide sotto pressione, si spegne un problema alla volta senza mai avere davvero il controllo del quadro generale.</p>'),
            ]),
            col([
                white_text('<p>Il Metodo Pocket rende <strong>immediatamente disponibili e accessibili</strong> competenze, strumenti e logiche strategiche che di solito richiederebbero anni di studio o grandi budget.</p>'),
            ]),
        ], ['flex_direction'=>'row','gap'=>['unit'=>'px','size'=>48,'column'=>'48','row'=>'48','isLinked'=>true]]),
    ], ['background_background'=>'classic','background_color'=>'#171E34','padding'=>$pad80]),

    // ── 2. CTA button ────────────────────────────────────────────────────
    section([
        inner([
            btn('Come possiamo aiutarti', 'https://sl1nk.com/come-possiamo-aiutarti', '#E9E3DA', '#171E34'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#171E34','padding'=>$pad80]),

    // ── 3. La nostra visione ─────────────────────────────────────────────
    section([
        narrow([
            eyebrow('LA NOSTRA VISIONE'),
            heading("Specialisti del quadro d'insieme", 'h2', 'center', '#171E34', 32),
            body_text('<p>Non siamo specialisti di un singolo ambito isolato. Col sorriso, ci piace definirci <strong>&ldquo;specialisti del quadro d\'insieme&rdquo;</strong>. In Italia, siamo stati tra i primi a osservare le micro-imprese nella loro reale complessità e a portare la <strong>&ldquo;small business strategy&rdquo;</strong> americana, adattandola al contesto locale.</p><p>Questo non significa &ldquo;fare tutto&rdquo;. Significa avere una <strong>visione ampia e sistemica</strong>, capace di cogliere le relazioni tra gli elementi del business e la loro evoluzione nel tempo.</p>'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 4. La Consulenza Formativa ───────────────────────────────────────
    section([
        narrow([
            eyebrow('LA NOSTRA FORMULA'),
            heading('La "Consulenza Formativa": un approccio ibrido', 'h2', 'center', '#171E34', 30),
            body_text('<p>I limiti dei due approcci tradizionali:</p>'),
            row([
                card([
                    body_text('<p>Il limite della <strong>formazione</strong> è che spesso rimane chiusa in un cassetto, non viene colta a pieno e si fa fatica ad applicarla al caso specifico.</p>', 'left', 15),
                ], '#fafaf8', 24, 4),
                card([
                    body_text('<p>Il limite della <strong>consulenza</strong> è che le soluzioni vengono fornite dall\'esterno, non sono frutto di nuove consapevolezze e non si sentono come &ldquo;proprie&rdquo;.</p>', 'left', 15),
                ], '#fafaf8', 24, 4),
            ], 24),
            body_text('<p>Per questo ogni nostro intervento è un ibrido tra <strong>consulenza strategica</strong> e <strong>formazione sul campo</strong>. Lavoriamo in modo pratico, orientato alle soluzioni, senza mai togliere all\'imprenditore il controllo della situazione.</p>'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 5. I tre pilastri ────────────────────────────────────────────────
    section([
        inner([
            eyebrow('I TRE PILASTRI'),
            heading('I tre pilastri del Metodo Pocket', 'h2', 'center', '#171E34', 36),
            body_text('<p>Ogni impresa, per crescere in modo sano, ha bisogno di equilibrio. Nel Metodo Pocket questo equilibrio si costruisce su tre pilastri, che devono nascere insieme e crescere insieme.</p>', 'center'),
            row([
                pillar_card_metodo('IL PRIMO PILASTRO', 'Business',
                    '<p>Pianificazione, controllo di gestione, capacità di governare tempo, denaro e risorse. Struttura per le decisioni.</p>'),
                pillar_card_metodo('IL SECONDO PILASTRO', 'Mercato',
                    '<p>Clienti, concorrenti, comunità, contesto territoriale. Posizionamento, dinamiche competitive, coerenza tra ciò che l\'impresa è e ciò che comunica.</p>'),
                pillar_card_metodo('IL TERZO PILASTRO', 'Felicità',
                    '<p>Persone coinvolte soddisfatte, crescita continua. Il metodo lavora perché l\'imprenditore diventi progressivamente più consapevole e autonomo.</p>'),
            ], 24, ['padding'=>['unit'=>'px','top'=>'32','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false]]),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#f5f3f0','padding'=>$pad80]),

    // ── 6. Multipotenzialità ─────────────────────────────────────────────
    section([
        narrow([
            eyebrow('FOCUS'),
            heading('Multipotenzialità: da caos a vantaggio competitivo', 'h2', 'center', '#171E34', 32),
            body_text('<p>Una parte importante delle persone con cui lavoriamo si riconosce in una parola: <strong>multipotenziale</strong>.</p><p>Percorsi di carriera non lineari, tante passioni e competenze in ambiti molto diversi, curiosità costante. Nel mercato del lavoro questo viene spesso letto come dispersione. Nel Metodo Pocket, no.</p><p>La multipotenzialità, se compresa e valorizzata, è un <strong>enorme asset strategico</strong>. Il problema non è avere molte direzioni possibili — è non avere una struttura che permetta di scegliere, testare e crescere nel tempo.</p>'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 7. Quando diventa essenziale ─────────────────────────────────────
    section([
        inner([
            eyebrow('QUATTRO FASI'),
            heading('Quando il Metodo Pocket diventa essenziale', 'h2', 'center', '#171E34', 36),
            body_text('<p>Il Metodo Pocket accompagna l\'impresa in ogni fase della sua vita, ma diventa particolarmente prezioso nei momenti di cambiamento.</p>', 'center'),
            row([
                phase_card('01', 'Ideazione & Startup',
                    '<p>Le prime decisioni strategiche sono le più importanti.</p>'),
                phase_card('02', 'Ampliamento & Sviluppo',
                    '<p>Crescere richiede struttura e metodo.</p>'),
                phase_card('03', 'Crisi & Restart',
                    '<p>Uscire dalla crisi con una nuova visione.</p>'),
                phase_card('04', 'Acquisizione & Passaggio generazionale',
                    '<p>Le transizioni più delicate.</p>'),
            ], 20, ['padding'=>['unit'=>'px','top'=>'32','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false]]),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 8. Vantaggi e limiti ─────────────────────────────────────────────
    section([
        narrow([
            eyebrow('VANTAGGI E LIMITI'),
            heading('Cosa aspettarsi dalla strategia di business', 'h2', 'center', '#171E34', 32),
            body_text('<p><strong>I vantaggi:</strong></p><ul><li>Aiuta ad anticipare rischi e opportunità</li><li>Permette di valutare la fattibilità dei progetti prima di investirci</li><li>Trasforma errori e fallimenti in informazioni preziose</li><li>Mette in condizioni di costruire il domani partendo da oggi</li></ul><p><strong>I limiti:</strong></p><ul><li>I suoi effetti più profondi emergono solo nel medio-lungo periodo</li><li>Non può sostituire l\'intuizione e la motivazione dell\'imprenditore</li><li>Senza monitoraggio e follow-up, nessuna pianificazione può garantire risultati</li></ul>'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 9. Livelli di accesso ─────────────────────────────────────────────
    section([
        narrow([
            eyebrow('LIVELLI DI ACCESSO'),
            heading('Un metodo, molte possibilità', 'h2', 'center', '#171E34', 32),
            body_text('<p>Il Metodo Pocket non è pensato per pochi. È pensato per accompagnare persone reali, in momenti diversi del loro percorso imprenditoriale.</p><p>Per questo abbiamo costruito <strong>un ecosistema di proposte su più livelli</strong>: percorsi gratuiti per chi si avvicina ora, una fascia intermedia per chi vuole crescere insieme, e progetti strutturati per situazioni più complesse.</p><p>Non esiste un percorso giusto in assoluto. Esiste <strong>il percorso giusto per il tuo momento</strong>.</p>'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#ffffff','padding'=>$pad80]),

    // ── 10. CTA finale ───────────────────────────────────────────────────
    section([
        inner([
            heading('Per iniziare, raccontaci di cosa si tratta.', 'h2', 'center', '#171E34', 32),
            btn('Come possiamo aiutarti', 'https://sl1nk.com/come-possiamo-aiutarti'),
        ], ['align_items'=>'center']),
    ], ['background_background'=>'classic','background_color'=>'#E9E3DA','padding'=>$pad80]),
];

// ── Save to page 17 ───────────────────────────────────────────────────────
$page_id = 17;
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
update_post_meta($page_id, '_wp_page_template', 'default');
delete_post_meta($page_id, '_elementor_css');
delete_post_meta($page_id, '_elementor_element_cache');
@unlink(WP_CONTENT_DIR . '/uploads/elementor/css/post-' . $page_id . '.css');

echo 'Done — metodo-pocket (ID ' . $page_id . ') rebuilt.' . PHP_EOL;
