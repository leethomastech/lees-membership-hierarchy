<?php
function lmh_organisation_page() {
    $org_label = get_option('lmh_org_label', 'Organisation');
    $use_expiry = get_option('lmh_use_org_expiry', 1);
    $status_label = get_option('lmh_status_label', 'Status');

    $status_raw = get_option('lmh_status_options', []);
    $status_options = is_array($status_raw) ? $status_raw : explode(',', $status_raw);
    $status_options = array_unique(array_merge(['Active', 'Inactive'], array_filter(array_map('trim', $status_options))));

    echo '<div class="wrap">';
    echo '<h1>' . esc_html($org_label) . ' Management</h1>';

    $orgs = get_option('lmh_organisations', []);

    if (isset($_GET['delete_org']) && check_admin_referer('lmh_manage_orgs')) {
        $delete_index = intval($_GET['delete_org']);
        if (isset($orgs[$delete_index])) {
            unset($orgs[$delete_index]);
            $orgs = array_values($orgs);
            update_option('lmh_organisations', $orgs);
            echo '<div class="updated"><p>' . esc_html($org_label) . ' deleted.</p></div>';
        }
    }

    if (isset($_POST['lmh_update_org_index']) && check_admin_referer('lmh_manage_orgs')) {
        $index = intval($_POST['lmh_update_org_index']);
        $orgs[$index]['name'] = isset($_POST['lmh_org_name']) ? sanitize_text_field(wp_unslash($_POST['lmh_org_name'])) : '';
        if ($use_expiry && isset($_POST['lmh_org_expiry'])) {
            $orgs[$index]['expiry'] = sanitize_text_field(wp_unslash($_POST['lmh_org_expiry']));
        }
        $orgs[$index]['status'] = isset($_POST['lmh_org_status']) ? sanitize_text_field(wp_unslash($_POST['lmh_org_status'])) : '';
        update_option('lmh_organisations', $orgs);
        echo '<div class="updated"><p>' . esc_html($org_label) . ' updated.</p></div>';
    }

    if (isset($_POST['lmh_add_org']) && check_admin_referer('lmh_manage_orgs')) {
        $name = isset($_POST['lmh_org_name']) ? sanitize_text_field(wp_unslash($_POST['lmh_org_name'])) : '';
        $expiry = isset($_POST['lmh_org_expiry']) ? sanitize_text_field(wp_unslash($_POST['lmh_org_expiry'])) : '';
        $status = isset($_POST['lmh_org_status']) ? sanitize_text_field(wp_unslash($_POST['lmh_org_status'])) : '';
        $orgs[] = [
            'name' => $name,
            'expiry' => $expiry,
            'status' => $status
        ];
        update_option('lmh_organisations', $orgs);
        echo '<div class="updated"><p>' . esc_html($org_label) . ' added.</p></div>';
    }

    $show_edit = isset($_GET['edit_org']);
    echo '<form method="post" style="margin-bottom: 30px;">';
    wp_nonce_field('lmh_manage_orgs');

    if ($show_edit && isset($orgs[intval($_GET['edit_org'])])) {
        $index = intval($_GET['edit_org']);
        $editing_org = $orgs[$index];
        echo '<h2>' . esc_html__('Edit', 'lees-membership-hierarchy') . ' ' . esc_html($org_label) . '</h2>';
        echo '<input type="hidden" name="lmh_update_org_index" value="' . esc_attr($index) . '" />';
    } else {
        echo '<h2>' . esc_html__('Add New', 'lees-membership-hierarchy') . ' ' . esc_html($org_label) . '</h2>';
    }

    echo '<table class="form-table">';
    echo '<tr><th>Name</th><td><input type="text" name="lmh_org_name" value="' . esc_attr($editing_org['name'] ?? '') . '" required /></td></tr>';

    if ($use_expiry) {
        echo '<tr><th>Expiry Date</th><td><input type="date" name="lmh_org_expiry" value="' . esc_attr($editing_org['expiry'] ?? '') . '" /></td></tr>';
    }

    echo '<tr><th>' . esc_html($status_label) . '</th><td><select name="lmh_org_status">';
    foreach ($status_options as $status) {
        $status = trim($status);
        $selected = (!empty($editing_org) && $status === $editing_org['status']) ? 'selected' : '';
        echo '<option value="' . esc_attr($status) . '" ' . ($selected ? 'selected="selected"' : '') . '>' . esc_html($status) . '</option>';
    }
    echo '</select></td></tr>';
    echo '</table>';

    if ($show_edit) {
        echo '<input type="submit" class="button button-primary" value="Update ' . esc_attr($org_label) . '" />';
    } else {
        echo '<input type="submit" name="lmh_add_org" class="button button-secondary" value="Add ' . esc_attr($org_label) . '" />';
    }

    echo '</form>';

    $search_query = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    if (!empty($search_query)) {
        $orgs = array_filter($orgs, function ($org) use ($search_query) {
            return stripos($org['name'], $search_query) !== false;
        });
    }

    $sort_by = isset($_GET['sort_by']) ? sanitize_key(wp_unslash($_GET['sort_by'])) : '';
    $sort_order = isset($_GET['order']) ? sanitize_text_field(wp_unslash($_GET['order'])) : 'asc';

    if ($sort_by) {
        usort($orgs, function ($a, $b) use ($sort_by, $sort_order) {
            $a_val = strtolower($a[$sort_by] ?? '');
            $b_val = strtolower($b[$sort_by] ?? '');
            if ($sort_by === 'expiry') {
                $a_val = strtotime($a[$sort_by]);
                $b_val = strtotime($b[$sort_by]);
            }
            return $sort_order === 'asc' ? $a_val <=> $b_val : $b_val <=> $a_val;
        });
    }

    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 10;
    $total_orgs = count($orgs);
    $total_pages = ceil($total_orgs / $per_page);
    $orgs = array_slice($orgs, ($page - 1) * $per_page, $per_page);

    echo '<form method="get" style="margin-bottom: 1em;">';
    echo '<input type="hidden" name="page" value="lmh-orgs">';
    echo '<input type="text" name="s" value="' . esc_attr($search_query) . '" placeholder="Search ' . esc_attr($org_label) . 's..." />';
    echo '<input type="submit" class="button" value="Search" />';
    echo '</form>';

    if (!empty($orgs)) {
        $base_url = admin_url('admin.php?page=lmh-orgs');
        $next_order = $sort_order === 'asc' ? 'desc' : 'asc';

        echo '<table class="widefat fixed striped"><thead><tr>';
        echo '<th><a href="' . esc_url(add_query_arg(['sort_by' => 'name', 'order' => $next_order], $base_url)) . '">Name</a></th>';
        if ($use_expiry) {
            echo '<th><a href="' . esc_url(add_query_arg(['sort_by' => 'expiry', 'order' => $next_order], $base_url)) . '">Expiry Date</a></th>';
        }
        echo '<th><a href="' . esc_url(add_query_arg(['sort_by' => 'status', 'order' => $next_order], $base_url)) . '">' . esc_html($status_label) . '</a></th>';
        echo '<th>Action</th></tr></thead><tbody>';

        foreach ($orgs as $i => $org) {
            $expired = $use_expiry && !empty($org['expiry']) && strtotime($org['expiry']) < strtotime('today');
            $inactive = strtolower(trim($org['status'])) === 'inactive';
            $row_class = ($expired || $inactive) ? 'lmh-expired' : '';

            echo '<tr class="' . esc_attr($row_class) . '"><td>' . esc_html($org['name']) . '</td>';
            if ($use_expiry) {
                $formatted_expiry = !empty($org['expiry']) ? gmdate('d-m-Y', strtotime($org['expiry'])) : '';
                echo '<td>' . esc_html($formatted_expiry) . '</td>';
            }
            echo '<td>' . esc_html($org['status'] ?? '') . '</td>';
            echo '<td>';
            echo '<a class="button" href="' . esc_url(add_query_arg(['page' => 'lmh-orgs', 'edit_org' => $i], admin_url('admin.php'))) . '">Edit</a> ';
            echo '<a class="button" href="' . esc_url(wp_nonce_url(add_query_arg(['page' => 'lmh-orgs', 'delete_org' => $i], admin_url('admin.php')), 'lmh_manage_orgs')) . '" onclick="return confirm(\'Are you sure?\')">Delete</a>';
            echo '</td></tr>';
        }

        echo '</tbody></table>';

        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            for ($p = 1; $p <= $total_pages; $p++) {
                $class = ($p === $page) ? 'current-page' : '';
                echo '<a class="' . esc_attr($class) . '" href="' . esc_url(add_query_arg('paged', $p, $base_url)) . '">' . esc_html($p) . '</a> ';
            }
            echo '</div></div>';
        }
    }

    echo '<style>.lmh-expired td { color: red !important; }</style>';
    echo '</div>';
}
