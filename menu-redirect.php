<?php
// menu-redirect.php

if (!defined('ABSPATH')) exit;

function lmh_main_page_redirect() {
    // Redirect the base plugin menu to the user list
    wp_safe_redirect(admin_url('admin.php?page=lmh-user-list'));
    exit;
}

add_action('admin_menu', function () {
    add_menu_page(
        get_option('lmh_menu_title', "Membership Hierarchy"),
        get_option('lmh_menu_title', "Membership Hierarchy"),
        'manage_options',
        'lmh-main',
        'lmh_main_page_redirect',
        'dashicons-networking',
        26
    );
});
