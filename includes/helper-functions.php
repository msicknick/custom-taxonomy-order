<?php

/**
 * Add 'position' metadata for taxonimies
 * 
 * @since 1.0.0
 */
function ms_cto_add_position_value($curr_screen) {
    if (isset($curr_screen) && isset($curr_screen->taxonomy)) {
        $terms = get_terms($curr_screen->taxonomy, array('hide_empty' => false));

        $x = 1;
        foreach ($terms as $term) {
            if (!get_term_meta($term->term_id, 'position', true)) {
                update_term_meta($term->term_id, 'position', $x);
                $x++;
            }
        }
    }
}

/**
 * Make sure 'position' metadata exists for a specific taxonomy
 * 
 * @since 1.0.0
 * @return: true/false
 */
function ms_cto_position_meta_exists($taxonomy_name) {
    $res = false;
    $taxonomy_obj = get_taxonomy($taxonomy_name);
    if ($taxonomy_obj && is_object($taxonomy_obj)) {

        $settings = get_option('cto_order_options', array());

        $enabled_taxonomies = isset($settings['enabled_taxonomies']) ? $settings['enabled_taxonomies'] : array();

        if (isset($taxonomy_obj->position) && $taxonomy_obj->position || in_array($taxonomy_name, $enabled_taxonomies)) {
            $res = true;
        } else {
            $res = false;
        }
    }
    return $res;
}
