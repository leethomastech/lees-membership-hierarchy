<?php
/*
Plugin Name: Lee's Membership Hierarchy
Plugin URI: https://lee-t.com/lees-membership-hierarchy/
Description: A custom membership hierarchy plugin allowing organisations, membership types, expiry dates, and status tracking per user.
Version: 1.0.0
Author: Lee Thomas
Author URI: https://leethomas.tech
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: lees-membership-hierarchy
Domain Path: /languages
*/



if (!defined('ABSPATH')) exit;

class LeesMembershipHierarchy {
    public function __construct() {
        // Admin menu
        add_action('admin_menu', [$this, 'add_plugin_menu']);
        
        // Settings
        require_once plugin_dir_path(__FILE__) . 'options.php';

        // Users
        require_once plugin_dir_path(__FILE__) . 'users.php';

        // Organisations
        require_once plugin_dir_path(__FILE__) . 'organisation.php';

        // User List
        require_once plugin_dir_path(__FILE__) . 'list.php';
    }

    public function add_plugin_menu() {
        add_menu_page(
            get_option('lmh_menu_title', "Membership Hierarchy"),
            get_option('lmh_menu_title', "Membership Hierarchy"),
            'manage_options',
            'lmh-main',
            [$this, 'redirect_to_user_list'],
            'dashicons-networking',
            26
        );

        add_submenu_page('lmh-main', 'Options', 'Options', 'manage_options', 'lmh-options', 'lmh_options_page');
        add_submenu_page('lmh-main', 'Organisations', get_option('lmh_org_label', 'Organisations'), 'manage_options', 'lmh-orgs', 'lmh_organisation_page');
        add_submenu_page('lmh-main', 'User List', 'User List', 'manage_options', 'lmh-user-list', 'lmh_user_list_page');
    }

    public function redirect_to_user_list() {
        wp_safe_redirect(admin_url('admin.php?page=lmh-user-list'));
        exit;
    }
}

new LeesMembershipHierarchy();