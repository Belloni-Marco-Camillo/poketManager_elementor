<?php

// ── Includes ───────────────────────────────────────────────────────────
require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/testimonials.php';

// ── Enqueue assets ─────────────────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    // Parent theme
    wp_enqueue_style('hello-elementor-parent', get_template_directory_uri() . '/style.css');
    // Child theme CSS
    wp_enqueue_style(
        'pm-theme',
        get_stylesheet_directory_uri() . '/assets/css/theme.css',
        ['hello-elementor-parent'],
        filemtime(get_stylesheet_directory() . '/assets/css/theme.css')
    );
    // Google Fonts
    wp_enqueue_style(
        'pm-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Marcellus+SC&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap',
        [],
        null
    );
    // Child theme JS
    wp_enqueue_script(
        'pm-theme-js',
        get_stylesheet_directory_uri() . '/assets/js/theme.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/theme.js'),
        true
    );
});

// ── Theme supports ─────────────────────────────────────────────────────
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style']);

    register_nav_menus([
        'primary_navigation' => 'Navigazione principale',
    ]);
});

// ── [pm_team] shortcode ────────────────────────────────────────────────
// Usage in Elementor Shortcode widget: [pm_team type="all" cols="3" eyebrow="IL TEAM" title="I nostri Pocket Manager"]
add_shortcode('pm_team', function ($atts) {
    $atts = shortcode_atts([
        'type'   => 'all',
        'cols'   => 3,
        'eyebrow' => '',
        'title'  => '',
    ], $atts, 'pm_team');

    $cols = max(2, min(4, (int) $atts['cols']));

    $query_args = [
        'post_type'      => 'pocket_manager',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ($atts['type'] !== 'all') {
        $query_args['meta_key']   = 'pm_team_type';
        $query_args['meta_value'] = sanitize_text_field($atts['type']);
    }

    $query = new WP_Query($query_args);

    ob_start();
    ?>
    <div class="pm-shortcode-team" style="padding-block:2rem;">
      <?php if ($atts['eyebrow'] || $atts['title']) : ?>
        <div style="text-align:center;margin-bottom:3rem;">
          <?php if ($atts['eyebrow']) : ?>
            <p class="pm-eyebrow"><?php echo esc_html($atts['eyebrow']); ?></p>
          <?php endif; ?>
          <?php if ($atts['title']) : ?>
            <h2 class="pm-h2"><?php echo esc_html($atts['title']); ?></h2>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($query->have_posts()) : ?>
        <div style="display:grid;grid-template-columns:repeat(<?php echo esc_attr($cols); ?>,1fr);gap:1.5rem;">
          <?php while ($query->have_posts()) : $query->the_post();
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
              <h3 class="pm-member-card__name"><?php echo esc_html($name); ?></h3>
              <?php if ($job_title) : ?>
                <p class="pm-member-card__title"><?php echo esc_html($job_title); ?></p>
              <?php endif; ?>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      <?php else : ?>
        <p style="text-align:center;font-style:italic;color:rgba(76,78,90,.5);">Nessun membro ancora.</p>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
});

// ── [pm_quiz] shortcode ────────────────────────────────────────────────
add_shortcode('pm_quiz', function ($atts) {
    $atts = shortcode_atts([
        'pitch'     => 'Scopri quale Pocket Manager fa per te.',
        'pitch_sub' => 'Rispondi a quattro domande: ti aiutiamo a capire di cosa ha bisogno il tuo business.',
    ], $atts, 'pm_quiz');

    $questions = [
        ['id'=>'q1_phase','q'=>'In che fase di sviluppo del tuo business ti trovi?','options'=>[
            ['v'=>'A','l'=>'Sto muovendo i primi passi ma il business non è ancora (pienamente) attivo'],
            ['v'=>'B','l'=>'Ho un\'attività già avviata, che sta crescendo molto'],
            ['v'=>'C','l'=>'Ho un\'attività già avviata che sta attraversando un momento di crisi'],
            ['v'=>'D','l'=>'La mia attività sta cambiando gestione (acquisizione o passaggio generazionale)'],
        ]],
        ['id'=>'q2_business','q'=>'Di cosa ti occupi?','options'=>[
            ['v'=>'A','l'=>'B2B - vendita a PMI e professionisti'],
            ['v'=>'B','l'=>'B2C - vendita a privati'],
            ['v'=>'C','l'=>'Arte, cultura e artigianato'],
            ['v'=>'D','l'=>'Non profit e associazioni'],
        ]],
        ['id'=>'q3_topic','q'=>'Quale argomento ti interessa di più?','options'=>[
            ['v'=>'A','l'=>'Marketing & Branding'],
            ['v'=>'B','l'=>'Business Planning & Event Management'],
            ['v'=>'C','l'=>'Sviluppo & Franchising'],
            ['v'=>'D','l'=>'Raccolta fondi & Finanziamenti'],
            ['v'=>'E','l'=>'Skills Imprenditoriali & Sviluppo Team'],
            ['v'=>'F','l'=>'Pocket Manager & Business Strategy'],
        ]],
        ['id'=>'q4_multipotential','q'=>'Ultima domanda: se dico "multipotenziale", tu dici… ?','options'=>[
            ['v'=>'a','l'=>'Sono io! *-*'],
            ['v'=>'b','l'=>'Eh? Di cosa si tratta?'],
            ['v'=>'c','l'=>'Non fa per me'],
        ]],
    ];

    ob_start();
    $uid  = 'pmq' . substr(md5(rand()), 0, 6);
    $js_q = array_map(function ($q) {
        return ['id' => $q['id'], 'text' => $q['q'], 'options' => array_map(function ($o) {
            return ['value' => $o['v'], 'label' => $o['l']];
        }, $q['options'])];
    }, $questions);
    ?>
    <div class="pm-quiz" id="<?php echo esc_attr($uid); ?>">
      <?php if ($atts['pitch']) : ?>
      <div class="pm-quiz__pitch">
        <p class="pm-quiz__pitch-title"><?php echo esc_html($atts['pitch']); ?></p>
        <?php if ($atts['pitch_sub']) : ?>
          <p class="pm-quiz__pitch-sub"><?php echo esc_html($atts['pitch_sub']); ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="pm-quiz__card" id="<?php echo esc_attr($uid); ?>_qa"></div>
    </div>
    <script>
    (function () {
      var qa = document.getElementById('<?php echo esc_js($uid); ?>_qa');
      if (!qa) return;
      var QUESTIONS   = <?php echo wp_json_encode($js_q, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
      var URL_METHOD  = '<?php echo esc_js(home_url('/metodo-pocket/')); ?>';
      var URL_RESULTS = '<?php echo esc_js(home_url('/risultati/')); ?>';
      var answers = {};
      function wrap(html) {
        return '<div class="pm-quiz__inner pm-quiz__inner--entering">' + html + '</div>';
      }
      function transition(fn) {
        var el = qa.querySelector('.pm-quiz__inner');
        if (!el) { fn(); return; }
        el.classList.remove('pm-quiz__inner--entering');
        el.classList.add('pm-quiz__inner--leaving');
        el.addEventListener('animationend', fn, { once: true });
      }
      function showQuestion(idx) {
        var q = QUESTIONS[idx];
        qa.innerHTML = wrap(
          '<p class="pm-quiz__question">' + q.text + '</p>' +
          '<div class="pm-quiz__options">' +
          q.options.map(function (o) {
            return '<button class="pm-quiz__option" data-value="' + o.value + '">' + o.label + '</button>';
          }).join('') + '</div>'
        );
        qa.querySelectorAll('.pm-quiz__option').forEach(function (btn) {
          btn.addEventListener('click', function () {
            answers[q.id] = this.dataset.value;
            if (idx === 2 && answers.q3_topic === 'F') {
              transition(function () { save(); window.location.href = URL_METHOD; });
            } else if (idx < QUESTIONS.length - 1) {
              transition(function () { showQuestion(idx + 1); });
            } else {
              transition(function () { save(); window.location.href = URL_RESULTS; });
            }
          });
        });
      }
      function save() {
        QUESTIONS.forEach(function (q) { if (!(q.id in answers)) answers[q.id] = null; });
        localStorage.setItem('pocket_questionnaire', JSON.stringify({
          version: '1.0', submittedAt: new Date().toISOString(), answers: answers,
          derived: {
            goToPocketMethod: answers.q3_topic === 'F',
            showM1: answers.q4_multipotential === 'a' || answers.q4_multipotential === 'b'
          }
        }));
      }
      showQuestion(0);
    })();
    </script>
    <?php
    return ob_get_clean();
});

// ── [pm_quiz_landing] shortcode ────────────────────────────────────────
add_shortcode('pm_quiz_landing', function () {

    $to_html = function (string $text): string {
        $paras = preg_split('/\n{2,}/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $out   = '';
        foreach ($paras as $p) {
            $p = trim($p);
            if ($p === '') continue;
            $out .= '<p>' . esc_html($p) . '</p>';
        }
        return $out;
    };

    // ── Phase texts (Q1) ───────────────────────────────────────────────
    $m2 = [
        'A' => [
            'title' => 'Come fare un salto nel vuoto',
            'html'  => $to_html(<<<'EOT'
                So bene come ci si sente quando si parte da un'idea ancora fumosa, senza contatti, senza fondi e con mille paure. Forse anche tu ti stai chiedendo: sarò in grado? troverò mai dei clienti? e se rischio tutto e non funziona?

                La verità è che fare impresa significa vivere nell'incertezza fin dal primo istante. E quell'incertezza non sparisce mai del tutto: rimane anche dopo il lancio, anche dopo i primi successi. Ma è proprio lì che nasce la forza dell'imprenditore: nel decidere di buttarsi e imparare a nuotare, invece di restare fermo sulla riva aspettando che qualcuno guidi la barca al posto suo.

                Mettersi in proprio significa prendere ciò che per te conta davvero — un sogno, un talento, un'idea — e farne il centro della tua vita. È un percorso che richiede coraggio, visione e metodo.

                Tutti i sogni restano tali finché non si trasformano in progetti e azioni concrete. E per farlo servono strumenti, chiarezza e competenze.

                Le domande da cui tutto parte sono solo tre: questa idea è abbastanza importante per me? Quello che posso offrire può davvero migliorare la vita delle persone? Come posso ottenere ciò che mi serve per partire?

                Alla prima domanda puoi rispondere solo tu. Per le altre due, invece, ci sono io — il tuo Pocket Manager.
                EOT),
        ],
        'B' => [
            'title' => 'Il "brivido" della crescita!',
            'html'  => $to_html(<<<'EOT'
                Se tutto va bene, nel business prima o poi arriva un momento in cui capisci che quello che hai costruito sta funzionando. I clienti aumentano, il lavoro non manca, le opportunità iniziano a bussare alla porta. E insieme all'entusiasmo, compaiono nuove domande: sto crescendo nel modo giusto? Riuscirò a reggere questo ritmo?

                Far crescere un'attività non è solo una questione di numeri. È un cambio di pelle. Significa passare da una "zona di comfort" dove le cose scorrono con un ritmo che conosci, al dover scegliere, delegare, strutturare. Significa prendere decisioni che avranno un impatto reale sul tuo tempo, sulle tue energie e sul tuo futuro.

                Spesso nessuno ci insegna come si fa. Se sei come me, hai imparato sul campo, giorno dopo giorno, risolvendo problemi, adattandoti, improvvisando quando serviva. Ed è proprio grazie a questo che sei arrivato fin qui. Ma per andare oltre serve altro.

                Crescere non significa solo fare di più. Significa fare meglio, con più lucidità e con una direzione chiara.

                Le domande chiave: quali opportunità mi portano davvero più vicino a ciò che desidero realizzare? Questa crescita è sostenibile per me e per il mio business? Quali scelte devo fare oggi per non ritrovarmi bloccato domani?

                Alla prima puoi rispondere solo tu. Per le altre due, invece, posso aiutarti io — il tuo Pocket Manager.
                EOT),
        ],
        'C' => [
            'title' => 'Quando andare avanti sembra più difficile che fermarsi',
            'html'  => $to_html(<<<'EOT'
                Ci sono momenti in cui alzare la serranda, mettersi al computer o rispondere al telefono pesa più del solito. Il lavoro cala, i margini si assottigliano, le energie diminuiscono. E inizi a chiederti se abbia ancora senso continuare.

                La verità è che fare impresa significa anche attraversare fasi buie. Periodi in cui le soluzioni non sono evidenti e ogni decisione sembra rischiosa. In questi momenti è facile perdere lucidità, sentirsi soli e confondere la stanchezza con il fallimento.

                Ma una crisi non è sempre la fine della storia: è un segnale. Un campanello d'allarme che chiede di fermarsi, guardare le cose da un'altra prospettiva e fare scelte diverse.

                Chiudere può essere una scelta legittima, ma dovrebbe essere una scelta consapevole e voluta, non una fuga dettata dalla paura o dall'esaurimento. Prima di decidere, c'è bisogno di fare chiarezza. Capire oggettivamente cosa non sta funzionando, cosa può essere cambiato e cosa non dipende più da te.

                Le domande chiave: questa attività ha ancora senso per me, oggi? Esistono strade alternative che non sto vedendo? Cosa devo capire prima di prendere una decisione definitiva?

                Alla prima puoi rispondere solo tu. Per le altre, posso affiancarti io — il tuo Pocket Manager.
                EOT),
        ],
        'D' => [
            'title' => 'Quando un\'impresa passa di mano',
            'html'  => $to_html(<<<'EOT'
                Un cambio di gestione non è mai solo una questione burocratica. Che tu stia per lasciare o per subentrare, sai bene che in gioco non c'è solo un'attività, ma una storia fatta di persone, scelte, sacrifici e identità.

                Forse stai cedendo qualcosa che hai costruito in anni di lavoro. Oppure stai per prendere in mano un'impresa che esiste già, con clienti, abitudini e dinamiche tutte sue. In entrambi i casi, il rischio è lo stesso: sottovalutare la complessità del passaggio.

                Perché un'impresa non è solo ciò che si vede dall'esterno. È fatta di equilibri delicati, di relazioni, di decisioni prese nel tempo. E ogni cambiamento, se non gestito con attenzione, può rompere qualcosa di prezioso.

                Un passaggio di gestione è un momento chiave. Può essere l'inizio di una nuova fase di crescita oppure l'origine di problemi difficili da recuperare più avanti. Serve lucidità, metodo e una visione chiara del "dopo".

                Le domande fondamentali: quando cedi o rilevi questa attività, cosa lasci e cosa prendi davvero? Cosa va rispettato e cosa può o deve cambiare? Come rendere questo passaggio solido e sostenibile nel tempo?

                Alla prima puoi rispondere solo tu. Per le altre, posso esserci io — il tuo Pocket Manager.
                EOT),
        ],
    ];

    // ── Sector texts (Q2) ──────────────────────────────────────────────
    $m3 = [
        'A' => [
            'title' => 'Nel B2B il problema non è vendere. È farsi capire (e ricordare)',
            'html'  => $to_html(<<<'EOT'
                Quando si parla di B2B si pensa subito a grandi aziende, strutture complesse, reparti acquisti e lunghi processi decisionali. Ma la realtà è molto più frammentata. Gran parte del B2B è fatto di piccole e piccolissime imprese che vendono ad altre imprese… spesso simili a loro.

                Ed è qui che iniziano le difficoltà. Capire con chi parlare, come parlargli e quando intercettarlo non è affatto scontato. I ruoli non sono chiari, le responsabilità si sovrappongono, le decisioni passano di mano in mano.

                A questo si aggiunge una burocrazia spesso lenta e articolata, che allunga i tempi, complica le relazioni e rende tutto più faticoso del necessario. Risultato? Trattative lunghe, comunicazioni poco dirette e relazioni che faticano a diventare davvero solide.

                E poi c'è un rischio sottile, ma enorme: legarsi a uno o pochi clienti "grandi" che diventano il fulcro del fatturato. Finché va bene, tutto sembra stabile. Ma basta perderne uno per mettere in crisi l'intero equilibrio.

                Nel B2B non vince chi urla più forte. Vince chi costruisce relazioni chiare, fiducia nel tempo e una percezione di valore che non dipende da una singola commessa.

                Se il tuo lavoro funziona davvero, la sfida non è solo trovare clienti, ma costruire un sistema che regga nel tempo. Andiamo avanti insieme.
                EOT),
        ],
        'B' => [
            'title' => 'Nel B2C il vero problema non è trovare clienti. È reggere il peso delle scelte',
            'html'  => $to_html(<<<'EOT'
                Fare B2C significa avere a che fare con tante persone, ogni giorno. Richieste diverse, aspettative alte, urgenze continue. E spesso la sensazione di essere sempre in rincorsa, senza mai riuscire a fermarsi davvero.

                All'inizio è comune dire sì a tutto. A ogni cliente, a ogni progetto, a ogni opportunità. È normale: serve fatturare, farsi conoscere, capire cosa funziona. Ma a lungo andare questo approccio ha un costo enorme.

                Nel B2C, se non fai scelte precise, il mercato le fa al posto tuo. Ti ritrovi con clienti poco allineati, progetti che non ti rappresentano, margini risicati e un'organizzazione che cresce in modo caotico.

                Scegliere per chi lavorare significa anche scegliere come organizzarti. Che tipo di offerte creare. Quanto tempo dedicare a ogni cliente. Quali paletti mettere per tutelare te e la tua attività.

                Se quello che fai ha valore, il punto non è attirare più clienti, ma costruire un sistema che ti permetta di crescere senza sfiancarti nel tentativo.
                EOT),
        ],
        'C' => [
            'title' => 'Non si può vendere un\'opera d\'arte come fosse dentifricio',
            'html'  => $to_html(<<<'EOT'
                Diciamocelo chiaramente: l'ambito artistico è la mosca bianca del business. C'è chi crea valore tangibile — e chi, come te, crea emozione, bellezza, cultura, significato. Meraviglioso, no? Eppure, quante volte hai sentito dire "non si può vivere di arte"?

                In Italia, chi fa arte, cultura o artigianato viene spesso ammirato e sottovalutato nello stesso tempo. A volte sono proprio gli artisti i primi a non voler trasformare la propria attività in business per non "snaturarla". Lo pensi anche tu?

                Vivere di arte si può — ma ci sono delle condizioni. Bisogna credere di poterlo fare, riconoscere il valore delle proprie creazioni e imparare a comunicarlo nel modo giusto.

                Se credi davvero di avere qualcosa di importante da dire o da dare; se senti che la tua arte o il tuo lavoro artigianale portano nel mondo un messaggio che vale la pena condividere, allora è anche tua precisa responsabilità renderlo sostenibile e diffonderlo.

                Perché sì, devi poter guadagnare abbastanza da sostenere la tua opera, farla viaggiare, farla arrivare a più persone possibile. Solo così il tuo talento può davvero avere impatto.

                Se ti ritrovi in queste parole, continua a leggere: scopriamo insieme come farlo, con metodo, visione e coraggio.
                EOT),
        ],
        'D' => [
            'title' => 'Chi aiuta i volontari?',
            'html'  => $to_html(<<<'EOT'
                Il volontariato è, per definizione, un gesto altruistico. Tempo, energie, competenze messe a disposizione degli altri, spesso senza chiedere nulla in cambio. Ed è proprio questo che lo rende così prezioso.

                Ma c'è una verità scomoda che va detta. Se si vuole fare di più, se si vuole arrivare dove il singolo non può arrivare, serve un'organizzazione. E un'organizzazione, volenti o nolenti, funziona come un'impresa.

                Progetti più grandi significano più persone coinvolte, più responsabilità, più costi. E se tutto questo non è sostenibile nel tempo, il rischio è enorme: drenare risorse, entusiasmo e soldi… per poi chiudere.

                Rendere sostenibile un'impresa senza scopo di lucro non significa snaturarne la missione. Significa proteggerla. Significa permetterle di durare, crescere, avere un impatto reale e continuo.

                Introiti, organizzazione, ruoli chiari, comunicazione efficace non sono un tradimento dei valori. Sono ciò che permette a quei valori di non spegnersi.

                Se credi davvero nella causa che porti avanti, pensare in modo strutturato è un atto di responsabilità, non di cinismo.
                EOT),
        ],
    ];

    // ── Topic texts (Q3) ───────────────────────────────────────────────
    $m4 = [
        'A' => [
            'title' => 'Parliamo, ad esempio, di Marketing e Branding',
            'html'  => $to_html(<<<'EOT'
                Fare marketing e branding, per me, ha a che fare con conoscersi e farsi conoscere. Con l'avere qualcosa di importante da dire e la volontà di farlo arrivare a più persone possibile. Con il mostrare il proprio valore e la propria unicità con orgoglio e senza filtri.

                Per me, il marketing parte sempre dall'ascolto — del professionista che ho davanti e del suo pubblico. Dalle loro esigenze, dai loro desideri, dalle domande a cui cercano risposta.

                Fare branding significa capire cosa puoi offrire di unico, di autentico e di valore al tuo pubblico. Quell'aspetto che nessun altro può replicare come te. E aiutarti a farti percepire nel modo giusto, da chi può davvero beneficiare di ciò che fai.

                Fare marketing, per me, significa trovare modi nuovi, semplici e sostenibili per entrare in contatto con nuove persone, raccontare loro chi sei e farlo in modo che possano fidarsi di te. Significa offrire valore prima ancora di chiedere attenzione, costruire relazioni vere e lasciare che la tua autenticità faccia la differenza.

                Il mio obiettivo è farti fare meno, ma meglio — in modo più efficace, più sostenibile e più tuo.
                EOT),
        ],
        'B' => [
            'title' => 'Pianificare non è prevedere il futuro. È smettere di improvvisare',
            'html'  => $to_html(<<<'EOT'
                Per me, planning ed event management significano costruire strutture che reggono. Strutture che non si basano sull'eroismo dell'ultimo minuto, ma su processi chiari, leggibili e sostenibili.

                Non significa impaginare presentazioni affascinanti che raccontano il nulla. Non significa inventare numeri che non stanno in piedi solo per "vendere" un progetto. E non significa produrre documenti che nessuno userà mai solo perché "vanno fatti".

                Un business plan, se fatto bene, è prima di tutto uno strumento di auto-analisi. Serve a chi lo scrive molto prima che a chi lo legge. Aiuta a capire se un'idea sta in piedi, dove scricchiola e cosa richiede davvero per funzionare.

                Lo stesso vale per gli eventi: un evento ben riuscito, nella stragrande maggioranza dei casi, è stato pianificato bene. Un semplice foglio di budget può diventare una risorsa preziosa per raggiungere obiettivi concreti.

                Pianificare non significa avere la sfera di cristallo. Significa costruire piani che sanno adattarsi alla realtà, senza diventare inutili al primo imprevisto.
                EOT),
        ],
        'C' => [
            'title' => 'Crescere non è una questione di coraggio. È una questione di consapevolezza',
            'html'  => $to_html(<<<'EOT'
                Mettiamo da parte la narrazione da super-businessman e diciamolo onestamente: crescere spaventa. Molto più spesso e molto più profondamente di quanto siamo disposti ad ammettere.

                C'è chi, per indole o per esperienza, sceglie di non crescere e di godersi ciò che funziona. È una scelta legittima, ma spesso più rischiosa di quanto sembri. Perché i business raramente sono fatti per "galleggiare" a lungo: o crescono, o lentamente iniziano a perdere spinta.

                La buona notizia è che si può crescere senza strappi. Si può andare avanti con i piedi di piombo, assumersi più responsabilità senza eccedere e senza vivere costantemente in affanno.

                Il franchising, ad esempio, è una strada concreta anche per chi fa servizi, anche per singoli professionisti con P.IVA. Creare un franchising non significa "aprire copie", ma strutturare un modello solido che altri possano replicare, investendo risorse proprie e condividendo il rischio.

                Il mio lavoro è aiutarti a capire se crescere, come farlo e a quale ritmo. Perché crescere bene è molto diverso dal crescere in fretta.
                EOT),
        ],
        'D' => [
            'title' => 'Trovare soldi è facile, se poi sai già come gestirli',
            'html'  => $to_html(<<<'EOT'
                Quando si parla di raccolta fondi o finanziamenti, la prima domanda è quasi sempre la stessa: "Come e dove trovo i soldi?" Ed è comprensibile. I soldi sembrano la leva che sblocca tutto.

                Ma molto spesso questa è una falsa partenza. Perché il vero tema non è dove trovare i soldi, ma se ha davvero senso cercarli — e soprattutto per cosa.

                I finanziamenti non risolvono problemi. Li amplificano. Se il modello è fragile, se gli obiettivi non sono chiari, se l'organizzazione non regge, più soldi significano semplicemente più pressione, più vincoli e meno libertà.

                Le domande giuste sono altre: quali sono le mie reali esigenze oggi? Sto cercando risorse per crescere, o per tappare falle? Quale potrebbe essere la modalità più giusta per trovarli, gestirli ed eventualmente restituirli?

                Se hai davvero un progetto solido, trovare dei soldi non è quasi mai il problema principale. Il problema è scegliere le modalità giuste, nel modo giusto e al momento giusto.
                EOT),
        ],
        'E' => [
            'title' => 'Il ruolo dell\'imprenditore non è fare tutto. È far funzionare il tutto',
            'html'  => $to_html(<<<'EOT'
                C'è un grande equivoco sul ruolo dell'imprenditore. Molti pensano che significhi essere ovunque, fare tutto, risolvere ogni problema in prima persona. In realtà è l'opposto. Il ruolo dell'imprenditore è costruire un sistema che funzioni anche senza di lui — almeno in parte.

                Ti faccio una domanda: cosa succederebbe alla tua impresa se ti assentassi, di colpo, per due mesi? Se la risposta è "troverei terra bruciata", c'è molto su cui lavorare. Se invece la risposta è "la troverei ancora attiva": Jackpot — ma quanti imprenditori possono dirlo davvero?

                Quando si parla di team, spesso si punta il dito sulle persone: come trovarle, come motivarle, come gestirle. Ma nella maggior parte dei casi il problema non sono le persone. Sono le procedure. Se non esistono sistemi chiari, anche il miglior talento farà fatica.

                Se fai crescere le tue skills imprenditoriali, se impari a progettare, delegare e monitorare, se ti dedichi unicamente alle cose che nell'impresa puoi fare solo tu, hai svoltato. Parola mia.
                EOT),
        ],
    ];

    // Topic labels for M7 dynamic text
    $topic_names = [
        'A' => 'Marketing & Branding',
        'B' => 'Business Planning & Event Management',
        'C' => 'Sviluppo & Franchising',
        'D' => 'Raccolta fondi & Finanziamenti',
        'E' => 'Skills Imprenditoriali & Sviluppo Team',
    ];

    ob_start();
    ?>
    <div class="pml-wrap">

      <!-- Hero -->
      <div class="pml-hero">
        <p class="pml-eyebrow">LA TUA GUIDA PERSONALIZZATA</p>
        <h1 class="pml-h1">Ecco cosa ho preparato per te</h1>
        <p class="pml-hero-sub">In base alle tue risposte, ho selezionato le informazioni più utili per il tuo percorso.</p>
      </div>

      <!-- Fallback: no localStorage data -->
      <div class="pml-fallback" id="pml-fallback" hidden>
        <p>Non trovo le tue risposte al questionario.</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="pml-back-link">← Torna alla homepage e completa il questionario</a>
      </div>

      <!-- M1: multipotential (conditional) -->
      <section class="pml-section pml-m1" id="pml-m1" hidden>
        <p class="pml-eyebrow">UNA PREMESSA IMPORTANTE</p>
        <h2 class="pml-section-title">Da professionista multipotenziale a imprenditore seriale</h2>
        <div class="pml-section-body">
          <p>Prima di iniziare, ci tengo a fare una premessa importante.</p>
          <p>Ci sono professionisti che non rientrano facilmente in una sola definizione. Hanno più interessi, più competenze, più direzioni possibili. Imparano in fretta, si appassionano, collegano ambiti diversi… e spesso si sentono fuori posto nel mondo del lavoro.</p>
          <p>Se ti è capitato di pensare di essere dispersivo o inconcludente, potresti essere una persona multipotenziale — magari anche senza saperlo. La multipotenzialità è la capacità di sviluppare più interessi e competenze e farli dialogare tra loro. Non porta a carriere lineari e, senza strumenti adeguati, può generare frustrazione, senso di colpa e la sensazione di non riuscire mai a scegliere "la strada giusta".</p>
          <p>Eppure oggi questa "trasversalità" è una dote enorme, forse l'unica vera chiave che può metterci in condizione di affrontare con slancio e serenità i grandi cambiamenti che stanno avvenendo nel mondo. Un professionista multipotenziale senza metodo si sente spesso "meno" degli altri. Con metodo e chiarezza, invece, può sentirsi finalmente "giusto" e libero di scegliere e coltivare più passioni, trasformandole in progetti e business fino a diventare, se lo vuole, un imprenditore seriale.</p>
          <p>In Pocket siamo tutti multipotenziali e vogliamo cambiare le regole del gioco.</p>
          <p>Se vuoi conoscere altri imprenditori multipotenziali come noi, puoi unirti alla nostra community WhatsApp: <a href="#" class="pml-link">link community</a>.</p>
          <p>👉 Detto questo… veniamo a noi.</p>
        </div>
      </section>

      <!-- M2: phase (dynamic, Q1) -->
      <section class="pml-section pml-phase" id="pml-phase" hidden>
        <h2 class="pml-section-title" id="pml-phase-title"></h2>
        <div class="pml-section-body" id="pml-phase-body"></div>
      </section>

      <!-- M3: sector (dynamic, Q2) -->
      <section class="pml-section pml-sector" id="pml-sector" hidden>
        <h2 class="pml-section-title" id="pml-sector-title"></h2>
        <div class="pml-section-body" id="pml-sector-body"></div>
      </section>

      <!-- M4: topic (dynamic, Q3) -->
      <section class="pml-section pml-topic" id="pml-topic" hidden>
        <h2 class="pml-section-title" id="pml-topic-title"></h2>
        <div class="pml-section-body" id="pml-topic-body"></div>
      </section>

      <!-- M5: CTA static -->
      <section class="pml-section pml-cta" id="pml-cta" hidden>
        <h2 class="pml-section-title">Mettiamoci alla prova!</h2>
        <div class="pml-section-body">
          <p>Scommetti che, nel corso di una chiacchierata, posso cambiare radicalmente il modo in cui guardi al tuo business? Dammi la chance di dimostrartelo — in cambio ti regalo una prima consulenza di 40 minuti dedicata al tuo progetto!</p>
        </div>
        <div class="pml-cta-opts">
          <a href="https://wa.me/393425032146" class="pml-cta-btn">📱 WhatsApp · 342 503 2146</a>
          <a href="mailto:info@pocketmanager.it" class="pml-cta-btn">✉️ info@pocketmanager.it</a>
          <a href="https://sl1nk.com/come-possiamo-aiutarti" class="pml-cta-btn pml-cta-btn--primary">📅 Prenota una call conoscitiva</a>
        </div>
      </section>

      <!-- M7: events (dynamic topic name) + community (static) -->
      <section class="pml-section pml-events" id="pml-events" hidden>
        <p>Ah! Quasi dimenticavo, se ti interessa approfondire <strong class="pml-topic-label"></strong>, organizziamo spesso degli eventi anche gratuiti che potrebbero essere una bella occasione per iniziare a conoscerci e lavorare insieme! Dai un'occhiata ai prossimi eventi in programma: <a href="#" class="pml-link">Pocket Labs</a>.</p>
        <p>Seguici qui per non perderti tutte le prossime proposte!</p>
        <ul class="pml-list">
          <li><a href="#" class="pml-link">Community MicroB</a></li>
          <li><a href="#" class="pml-link">Canale IG</a></li>
        </ul>
      </section>

      <!-- M6: navigation (static) -->
      <section class="pml-section pml-nav" id="pml-nav" hidden>
        <div class="pml-section-body">
          <p>Ogni giorno aiuto persone come te a costruire, trasformare o far crescere la propria impresa. Ma Pocket Manager non sono solo io: è un metodo, una visione, una rete di professionisti che credono nel valore dell'impresa come percorso umano, prima ancora che economico.</p>
          <p>Qui puoi scoprire come lavoriamo, leggere le storie di chi ci ha scelto e trovare risorse utili per fare un passo avanti, oggi stesso.</p>
        </div>
        <p class="pml-nav-cta">👉 Entra nel mondo di Pocket Manager e scopri come possiamo camminare insieme.</p>
        <ul class="pml-nav-links">
          <li><a href="<?php echo esc_url(home_url('/metodo-pocket/')); ?>">Il Metodo Pocket Manager</a></li>
          <li><a href="#">Il Blog – storie di clienti</a></li>
          <li><a href="#">Le Risorse e i Giochi Pocket</a></li>
          <li><a href="<?php echo esc_url(home_url('/pocket-managers/')); ?>">Il nostro Team: chi è davvero Pocket Manager</a></li>
        </ul>
      </section>

    </div>

    <script>
    (function () {
      var DATA = {
        phase:      <?php echo wp_json_encode($m2, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        sector:     <?php echo wp_json_encode($m3, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        topic:      <?php echo wp_json_encode($m4, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        topicNames: <?php echo wp_json_encode($topic_names, JSON_UNESCAPED_UNICODE); ?>
      };

      var raw     = localStorage.getItem('pocket_questionnaire');
      var payload = raw ? JSON.parse(raw) : null;

      function show(id) {
        var el = document.getElementById(id);
        if (el) el.hidden = false;
      }

      function fill(titleId, bodyId, data) {
        if (!data) return false;
        var t = document.getElementById(titleId);
        var b = document.getElementById(bodyId);
        if (t) t.textContent = data.title;
        if (b) b.innerHTML  = data.html;
        return true;
      }

      if (!payload || !payload.answers) {
        show('pml-fallback');
        return;
      }

      var a       = payload.answers;
      var derived = payload.derived || {};

      if (derived.showM1) show('pml-m1');

      if (fill('pml-phase-title', 'pml-phase-body', DATA.phase[a.q1_phase]))   show('pml-phase');
      if (fill('pml-sector-title', 'pml-sector-body', DATA.sector[a.q2_business])) show('pml-sector');
      if (fill('pml-topic-title', 'pml-topic-body', DATA.topic[a.q3_topic]))   show('pml-topic');

      var topicName = DATA.topicNames[a.q3_topic] || 'questo argomento';
      document.querySelectorAll('.pml-topic-label').forEach(function (el) {
        el.textContent = topicName;
      });

      show('pml-cta');
      show('pml-events');
      show('pml-nav');
    })();
    </script>
    <?php
    return ob_get_clean();
});

// ── Elementor: disable Hello default header/footer ─────────────────────
// Hello Elementor uses its own header/footer when theme locations are empty.
// Our child theme provides header.php / footer.php so this is fine.
add_filter('hello_elementor_page_title', '__return_false');
