<?php
function lmh_show_user_profile_fields($user) {
    $org_label = get_option('lmh_org_label', 'Organisation');
    $membership_label = get_option('lmh_membership_label', 'Membership Type');
    $use_org_expiry = get_option('lmh_use_org_expiry', 1);
    $use_org_status = get_option('lmh_use_org_status', 0);
    $orgs = get_option('lmh_organisations', []);

    $membership_types_raw = get_option('lmh_membership_types', ['Member', 'Admin', 'Delegate']);
    $membership_types = is_array($membership_types_raw) ? $membership_types_raw : explode(',', $membership_types_raw);

    $status_options_raw = get_option('lmh_status_options', []);
    $status_options = is_array($status_options_raw) ? $status_options_raw : explode(',', $status_options_raw);
    $status_options = array_unique(array_merge(['Active', 'Inactive'], array_filter(array_map('trim', $status_options))));

    $selected_org = get_user_meta($user->ID, 'lmh_organisation', true);
    $selected_type = get_user_meta($user->ID, 'lmh_membership_type', true);
    $selected_status = get_user_meta($user->ID, 'lmh_status', true);
    $user_expiry = get_user_meta($user->ID, 'lmh_expiry_date', true);
    $org_expiry = '';
    $org_status = '';
    foreach ($orgs as $org) {
        if ($org['name'] === $selected_org) {
            $org_expiry = $org['expiry'];
            $org_status = $org['status'];
            break;
        }
    }

    echo '<h2>Membership Details</h2><table class="form-table">';

    echo '<tr><th>' . esc_html($org_label) . '</th><td><select name="lmh_organisation">';
    echo '<option value="">-- Select --</option>';
    foreach ($orgs as $org) {
        $selected = ($org['name'] === $selected_org);
        echo '<option value="' . esc_attr($org['name']) . '" ' . selected($selected, true, false) . '>' . esc_html($org['name']) . '</option>';
    }
    echo '</select></td></tr>';

    echo '<tr><th>' . esc_html($membership_label) . '</th><td><select name="lmh_membership_type">';
    foreach ($membership_types as $type) {
        $type = trim($type);
        $selected = ($type === $selected_type);
        echo '<option value="' . esc_attr($type) . '" ' . selected($selected, true, false) . '>' . esc_html($type) . '</option>';
    }
    echo '</select></td></tr>';

    echo '<tr><th>Status</th><td>';
    if ($use_org_status) {
        echo '<input type="text" value="' . esc_attr($org_status) . '" readonly />';
    } else {
        echo '<select name="lmh_status">';
        foreach ($status_options as $status) {
            $selected = ($status === $selected_status);
            echo '<option value="' . esc_attr($status) . '" ' . selected($selected, true, false) . '>' . esc_html($status) . '</option>';
        }
        echo '</select>';
    }
    echo '</td></tr>';

    echo '<tr><th>Expiry Date</th><td>';
    if ($use_org_expiry) {
        $formatted_org_expiry = $org_expiry ? gmdate('d-m-Y', strtotime($org_expiry)) : '';
        echo '<input type="text" value="' . esc_attr($formatted_org_expiry) . '" readonly />';
    } else {
        $formatted_user_expiry = $user_expiry ? gmdate('Y-m-d', strtotime($user_expiry)) : '';
        echo '<input type="date" name="lmh_expiry_date" value="' . esc_attr($formatted_user_expiry) . '" />';
    }
    echo '</td></tr>';

    echo '</table>';
}

function lmh_save_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;
    
    check_admin_referer('update-user_' . $user_id);

    if (isset($_POST['lmh_organisation'])) {
        update_user_meta($user_id, 'lmh_organisation', sanitize_text_field(wp_unslash($_POST['lmh_organisation'])));
    }

    if (isset($_POST['lmh_membership_type'])) {
        update_user_meta($user_id, 'lmh_membership_type', sanitize_text_field(wp_unslash($_POST['lmh_membership_type'])));
    }

    if (!get_option('lmh_use_org_status', 0) && isset($_POST['lmh_status'])) {
        update_user_meta($user_id, 'lmh_status', sanitize_text_field(wp_unslash($_POST['lmh_status'])));
    }

    if (!get_option('lmh_use_org_expiry', 1) && isset($_POST['lmh_expiry_date'])) {
        update_user_meta($user_id, 'lmh_expiry_date', sanitize_text_field(wp_unslash($_POST['lmh_expiry_date'])));
    }
}

add_action('show_user_profile', 'lmh_show_user_profile_fields');
add_action('edit_user_profile', 'lmh_show_user_profile_fields');
add_action('personal_options_update', 'lmh_save_user_profile_fields');
add_action('edit_user_profile_update', 'lmh_save_user_profile_fields');
