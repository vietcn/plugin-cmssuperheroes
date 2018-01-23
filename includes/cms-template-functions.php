<?php
/**
 * @Template: cms-template-functions.php
 * @since: 1.0.0
 * @author: KP
 * @descriptions:
 * @create: 19-Jan-18
 */

function cms_get_grid_term_list($post_type)
{
    $taxonomy_objects = get_object_taxonomies($post_type, 'names');
    $term_list = array();
    foreach ($taxonomy_objects as $tax) {
        $terms = get_terms(
            array(
                'taxonomy'   => $tax,
                'hide_empty' => false,
            )
        );
        foreach ($terms as $term) {
            $term_list['terms'][] = $term->term_id . '|' . $tax;
            $term_list['auto_complete'][] = array(
                'value' => $term->term_id . '|' . $tax,
                'label' => $term->name,
            );
        }
    }
    return $term_list;
}

function cms_get_type_posts_data($post_type = 'post')
{
    $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type'      => $post_type,
    ));
    $result = array();
    foreach ($posts as $post) {
        $result[] = array(
            'value' => $post->ID,
            'label' => $post->post_title,
        );
    }
    return $result;
}

function cms_get_term_of_post_to_class($post_id, $tax = array())
{
    $term_list = array();
    foreach ($tax as $taxo) {
        $term_of_post = wp_get_post_terms($post_id, $taxo);
        foreach ($term_of_post as $term) {
            $term_list[] = $term->slug;
        }
    }
    return implode(' ', $term_list);
}

function cms_get_posts_of_grid($post_type = 'post', $atts = array())
{
    extract($atts);
    if (!empty($post_ids)) {
        $posts = get_posts(
            array(
                'post_type' => $post_type,
                'include'   => array_map('intval', explode(',', $post_ids))
            )
        );
    } else {
        $args = array(
            'post_type'       => $post_type,
            'posts_per_page ' => !empty($limit) ? intval($limit) : 6,
            'order'           => !empty($order) ? $order : 'DESC',
            'orderby'         => !empty($orderby) ? $orderby : 'date',
            'tax_query'       => array(
                'relation' => 'OR',
            )
        );
        //default categories selected
        $source = !empty($source) ? $source : '';
        // if select term on custom post type, move term item to cat.
        if (!empty($source)) {
            $source_a = explode(',', $source);
            foreach ($source_a as $terms) {
                $tmp = explode('|', $terms);
                if (isset($tmp[0]) && isset($tmp[1])) {
                    $args_lol['tax_query'][] = array(
                        'taxonomy' => $tmp[1],
                        'field'    => 'term_id',
                        'operator' => 'IN',
                        'terms'    => array($tmp[0]),
                    );
                }
            }
        }
        if (get_query_var('paged')) {
            $cms_paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $cms_paged = get_query_var('page');
        } else {
            $cms_paged = 1;
        }
        $cms_query = new WP_Query($args);
//        if($paged > 1){
//            $cms_query->set('paged', $paged);
//        }
        $cms_query->set('paged', intval($cms_paged));
        $cms_query->set('posts_per_page', !empty($limit) ? intval($limit) : 6);
        $query = $cms_query->query($cms_query->query_vars);
        $posts = $query;
    }

    if (!empty($source)) {
        $categories = explode(',', $source);
    } else {
        $source_new = cms_get_grid_term_list($post_type);
        $categories = $source_new['terms'];
    }
    global $paged;
    $paged = $cms_paged;
    $categories = is_array($categories) ? $categories : array();
    return array(
        'posts'      => $posts,
        'categories' => $categories,
        'query'      => $cms_query,
        'paged'      => $paged,
        'max'        => $cms_query->max_num_pages,
        'next_link'  => next_posts($cms_query->max_num_pages, false),
        'total'      => $cms_query->found_posts
    );
}