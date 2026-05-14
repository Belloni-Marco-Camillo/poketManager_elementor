<?php

function pm_register_post_types(): void
{
    register_post_type('pocket_manager', [
        'labels' => [
            'name'               => 'Pocket Manager',
            'singular_name'      => 'Pocket Manager',
            'add_new'            => 'Nuovo',
            'add_new_item'       => 'Aggiungi Pocket Manager',
            'edit_item'          => 'Modifica Pocket Manager',
            'new_item'           => 'Nuovo Pocket Manager',
            'view_item'          => 'Vedi Pocket Manager',
            'search_items'       => 'Cerca Pocket Manager',
            'not_found'          => 'Nessun Pocket Manager trovato',
            'not_found_in_trash' => 'Nessun Pocket Manager nel cestino',
            'all_items'          => 'Tutti i Pocket Manager',
            'menu_name'          => 'Pocket Manager',
        ],
        'public'        => true,
        'has_archive'   => 'pocket-managers',
        'rewrite'       => ['slug' => 'pocket-manager'],
        'menu_icon'     => 'dashicons-id-alt',
        'menu_position' => 5,
        'supports'      => ['title', 'thumbnail'],
        'show_in_rest'  => false,
        'orderby'       => 'menu_order',
        'order'         => 'ASC',
    ]);
}
add_action('init', 'pm_register_post_types');
