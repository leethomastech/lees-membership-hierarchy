<?php
// options.php

if (!defined('ABSPATH')) exit;

function lmh_register_settings() {
    register_setting('lmh_options_group', 'lmh_org_label', 'sanitize_text_field');
    register_setting('lmh_options_group', 'lmh_membership_label', 'sanitize_text_field');
    register_setting('lmh_options_group', 'lmh_use_org_expiry', 'lmh_sanitize_checkbox');
    register_setting('lmh_options_group', 'lmh_use_org_status', 'lmh_sanitize_checkbox');
    register_setting('lmh_options_group', 'lmh_membership_types', 'lmh_sanitize_array_of_text');
    register_setting('lmh_options_group', 'lmh_status_options', 'lmh_sanitize_array_of_text');
    register_setting('lmh_options_group', 'lmh_status_label', 'sanitize_text_field');
}
add_action('admin_init', 'lmh_register_settings');

function lmh_sanitize_checkbox($input) {
    return ($input == '1') ? 1 : 0;
}

function lmh_sanitize_array_of_text($input) {
    if (!is_array($input)) {
        return [];
    }
    return array_map('sanitize_text_field', array_filter($input));
}

function lmh_options_page() {
    $membership_types_raw = get_option('lmh_membership_types', ['Member', 'Admin', 'Delegate']);
    $membership_types = is_array($membership_types_raw) ? $membership_types_raw : explode(',', $membership_types_raw);

    $status_label = get_option('lmh_status_label', 'Status');
    $status_options_raw = get_option('lmh_status_options', []);
    $status_options = is_array($status_options_raw) ? $status_options_raw : explode(',', $status_options_raw);
    $status_options = array_unique(array_merge(['Active', 'Inactive'], array_filter(array_map('trim', $status_options))));
?>
    <div class="wrap">
        <h1>Membership Hierarchy Plugin Options</h1>
        <form method="post" action="options.php">
            <?php settings_fields('lmh_options_group'); ?>
            <?php do_settings_sections('lmh_options_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Organisation Label</th>
                    <td><input type="text" name="lmh_org_label" value="<?php echo esc_attr(get_option('lmh_org_label', 'Organisation')); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Membership Type Label</th>
                    <td><input type="text" name="lmh_membership_label" value="<?php echo esc_attr(get_option('lmh_membership_label', 'Membership Type')); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Users expiry date taken from Organisation</th>
                    <td><input type="checkbox" name="lmh_use_org_expiry" value="1" <?php checked(1, get_option('lmh_use_org_expiry', 1)); ?> /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Users status taken from Organisation</th>
                    <td><input type="checkbox" name="lmh_use_org_status" value="1" <?php checked(1, get_option('lmh_use_org_status', 0)); ?> /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Membership Types</th>
                    <td>
                        <ul id="membership-types-list">
                            <?php foreach ($membership_types as $i => $type): ?>
                                <li>
                                    <input type="text" name="lmh_membership_types[]" value="<?php echo esc_attr(trim($type)); ?>" />
                                    <button type="button" onclick="this.parentNode.remove();">Delete</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" onclick="addField('membership-types-list', 'lmh_membership_types[]')">Add</button>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Status Options</th>
                    <td>
                        <ul id="status-options-list">
                            <li><strong>Active</strong> (fixed)</li>
                            <li><strong>Inactive</strong> (fixed)</li>
                            <?php foreach ($status_options as $status): ?>
                                <?php if (!in_array($status, ['Active', 'Inactive'])): ?>
                                    <li>
                                        <input type="text" name="lmh_status_options[]" value="<?php echo esc_attr($status); ?>" />
                                        <button type="button" onclick="this.parentNode.remove();">Delete</button>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" onclick="addField('status-options-list', 'lmh_status_options[]')">Add</button>
                        <input type="hidden" name="lmh_status_label" value="<?php echo esc_attr($status_label); ?>" />
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>
        <h2>Feedback</h2>
        <p>If you have any feedback, please email: <a href="mailto:mail@lee-t.com">mail@lee-t.com</a></p>
        <p><a href="https://buymeacoffee.com/leethomastech" target="_blank">Buy me a coffee ☕</a></p>
    </div>

    <script>
        function addField(listId, inputName) {
            var ul = document.getElementById(listId);
            var li = document.createElement('li');
            li.innerHTML = '<input type="text" name="' + inputName + '" /> <button type="button" onclick="this.parentNode.remove();">Delete</button>';
            ul.appendChild(li);
        }
    </script>
<?php
}
