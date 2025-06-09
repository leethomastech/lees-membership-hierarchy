<?php
// list.php

if (!defined('ABSPATH')) exit;

if (!function_exists('lmh_user_list_page')) {
    function lmh_user_list_page() {
        $org_label = get_option('lmh_org_label', 'Organisation');
        $membership_label = get_option('lmh_membership_label', 'Membership Type');
        $status_label = get_option('lmh_status_label', 'Status');
        $use_org_expiry = get_option('lmh_use_org_expiry', 1);
        $use_org_status = get_option('lmh_use_org_status', 0);
        $orgs = get_option('lmh_organisations', []);

        echo '<div class="wrap">';
        echo '<h1>User Membership List</h1>';

        $users = get_users();

        echo '<table class="widefat fixed striped">';
        echo '<thead><tr>
            <th>Username</th>
            <th>Email</th>
            <th>' . esc_html($org_label) . '</th>
            <th>' . esc_html($membership_label) . '</th>
            <th>Expiry Date</th>
            <th>' . esc_html($status_label) . '</th>
            <th>Action</th>
        </tr></thead><tbody>';

        foreach ($users as $user) {
            $org = get_user_meta($user->ID, 'lmh_organisation', true);
            $type = get_user_meta($user->ID, 'lmh_membership_type', true);
            $user_expiry = get_user_meta($user->ID, 'lmh_expiry_date', true);
            $user_status = get_user_meta($user->ID, 'lmh_status', true);

            $expiry = $user_expiry;
            $status = $user_status;

            foreach ($orgs as $org_obj) {
                if ($org_obj['name'] === $org) {
                    if ($use_org_expiry) {
                        $expiry = $org_obj['expiry'];
                    }
                    if ($use_org_status) {
                        $status = $org_obj['status'] ?? '';
                    }
                    break;
                }
            }

            $expired = ($expiry && strtotime($expiry) < time()) ? 'style="color:red;"' : '';
            $formatted_expiry = (!empty($expiry) && strtotime($expiry)) ? gmdate('d-m-Y', strtotime($expiry)) : '';

            echo '<tr ' . esc_attr($expired) . '>
                <td>' . esc_html($user->user_login) . '</td>
                <td>' . esc_html($user->user_email) . '</td>
                <td>' . esc_html($org) . '</td>
                <td>' . esc_html($type) . '</td>
                <td>' . esc_html($formatted_expiry) . '</td>
                <td>' . esc_html($status) . '</td>
                <td><a class="button" href="' . esc_url(get_edit_user_link($user->ID)) . '">Edit</a></td>
            </tr>';
        }

        echo '</tbody></table>';
        echo '</div>';
    }
}