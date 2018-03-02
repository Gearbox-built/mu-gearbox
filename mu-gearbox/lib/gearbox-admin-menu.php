<?php

// Change Pages to Partners
function gear_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Posts';
    $labels->singular_name = 'Post';
    $labels->add_new = 'Add Post';
    $labels->add_new_item = 'Add Post';
    $labels->edit_item = 'Edit Post';
    $labels->new_item = 'Post';
    $labels->view_item = 'View Post';
    $labels->search_items = 'Search Posts';
    $labels->not_found = 'No Post found';
    $labels->not_found_in_trash = 'No Post found in Trash';
    $labels->all_items = 'All Posts';
    $labels->menu_name = 'Posts';
    $labels->name_admin_bar = 'Posts';
}

// add_action('init', 'gear_change_post_object');

// Remove menu items
function gear_remove_menu_items() {
    global $user_ID;
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');

    if(!current_user_can('edit_others_pages')) {
        remove_menu_page('upload.php');
        remove_menu_page('edit.php?post_type=project');
    }
}

// add_action('admin_menu', 'gear_remove_menu_items');

// Remove toolbar nodes
function gear_remove_post_nodes() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('new-post');

    if(!current_user_can('edit_others_pages')) {
        $wp_admin_bar->remove_node('new-media');
        $wp_admin_bar->remove_node('new-project');
    }
}

// add_action('admin_bar_menu', 'gear_remove_post_nodes', 999);
