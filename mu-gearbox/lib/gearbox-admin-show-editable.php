<?php
/**
 * Show only entries of the current user.
 *
 * @param \WP_Query $query
 * @link https://wp-includes.org/486/hide-other-users-posts-wp-admin/
 */
function show_only_editable( $query ) {
  if ( ! $query->is_admin
       || ! $query->is_main_query()
       || ! $query->get( 'post_type' )
       || current_user_can( 'edit_others_posts' )
  ) {
    return;
  }

  $query->set( 'author', get_current_user_id() );
  add_filter( 'wp_count_posts', 'fix_count_orders', PHP_INT_MAX, 3 );
}

function hide_others_posts_feature() {
  add_filter( 'pre_get_posts', 'show_only_editable' );
}

add_action( 'admin_init', 'hide_others_posts_feature' );

/**
 * Fix counts
 *
 * @link https://wordpress.stackexchange.com/a/274792
 */
function fix_count_orders( $counts, $type, $perm ) {
    global $wpdb;

    if ( ! post_type_exists( $type ) ) {
        return new stdClass();
    }

    $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";

    $post_type_object = get_post_type_object( $type );

    // adds condition to respect `$perm`. (3)
    if ( $perm === 'readable' && is_user_logged_in() ) {
        if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
            $query .= $wpdb->prepare(
                " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
                get_current_user_id()
            );
        }
    }

    // limits only author's own posts. (6)
    if ( is_admin() && ! current_user_can ( $post_type_object->cap->edit_others_posts ) ) {
        $query .= $wpdb->prepare( ' AND post_author = %d', get_current_user_id() );
    }

    $query .= ' GROUP BY post_status';

    $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
    $counts  = array_fill_keys( get_post_stati(), 0 );

    foreach ( $results as $row ) {
        $counts[ $row['post_status'] ] = $row['num_posts'];
    }

    $counts    = (object) $counts;
    $cache_key = _count_posts_cache_key( $type, 'readable' );

    // caches the result. (2)
    // although this is not so efficient because the cache is almost always deleted.
    wp_cache_set( $cache_key, $counts, 'counts' );

    return $counts;
}
