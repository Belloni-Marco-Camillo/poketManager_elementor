<?php

function pm_register_acf_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_pocket_manager',
        'title'    => 'Profilo Pocket Manager',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'pocket_manager']]],
        'fields'   => [
            ['key' => 'field_pm_photo',      'name' => 'pm_photo',      'label' => 'Foto',                        'type' => 'image',      'return_format' => 'array', 'preview_size' => 'medium'],
            ['key' => 'field_pm_job_title',  'name' => 'pm_job_title',  'label' => 'Qualifica / Job Title',       'type' => 'text'],
            ['key' => 'field_pm_subtitle',   'name' => 'pm_subtitle',   'label' => 'Sottotitolo',                 'type' => 'textarea',   'rows' => 2],
            ['key' => 'field_pm_team_type',  'name' => 'pm_team_type',  'label' => 'Ruolo nel team',              'type' => 'select',
                'choices' => ['fondatrice' => 'Fondatrice', 'consulente' => 'Consulente'],
                'default_value' => 'consulente', 'return_format' => 'value'],
            ['key' => 'field_pm_location',   'name' => 'pm_location',   'label' => 'Sede operativa',              'type' => 'text'],
            ['key' => 'field_pm_s1',         'name' => 'pm_da_dove_arrivo',   'label' => 'Da dove arrivo',              'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0],
            ['key' => 'field_pm_s2',         'name' => 'pm_specializzazione', 'label' => 'Area di specializzazione',    'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0],
            ['key' => 'field_pm_s3',         'name' => 'pm_chi_si_chi_no',    'label' => 'Chi sì / chi no',             'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0],
            ['key' => 'field_pm_s4',         'name' => 'pm_metodo_personale', 'label' => 'Metodo declinato su di me',   'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0],
            ['key' => 'field_pm_s5',         'name' => 'pm_perche_pocket',    'label' => 'Perché ho scelto Pocket',     'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0],
            ['key' => 'field_pm_extra_show', 'name' => 'pm_extra_show', 'label' => 'Mostra sezione extra?',        'type' => 'true_false', 'default_value' => 0],
            ['key' => 'field_pm_extra',      'name' => 'pm_extra',      'label' => 'Extra personalizzabili',       'type' => 'wysiwyg',
                'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0,
                'conditional_logic' => [[['field' => 'field_pm_extra_show', 'operator' => '==', 'value' => '1']]]],
        ],
    ]);
}
add_action('acf/init', 'pm_register_acf_fields');
