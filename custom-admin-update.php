<?php
/**
 * Plugin Name:       Custom Admin Update
 * Plugin URI:        https://wordpress.org/plugins/custom-admin-update
 * Description:       Allows administrators to display Custom Updates in the WordPress admin area. This can be helpful for site-wide announcements or important information.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nishita Joshi
 * Author URI:        https://www.linkedin.com/in/nishita-joshi-1bb5b6217?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-admin-update
 */

if (!defined('ABSPATH')) {
    exit; 
}

// Function to display the custom admin update
function cdup_admin_update() {
    $notice_text = get_option('custom_admin_notice_text', '');

    // Check if update text is not empty
    if (!empty($notice_text)) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($notice_text); ?></p>
        </div>
        <?php
    }
}

// Hook to display the custom admin update
add_action('admin_notices', 'cdup_admin_update');

// Function to display the settings page content
function cdup_admin_settings_page() {
    // Check if the user has the capability to activate plugins (typically administrators)
    if (current_user_can('activate_plugins')) {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('custom_admin_notices_settings_group'); ?>
                <?php do_settings_sections('custom_admin_notices_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Custom Admin Update Text</th>
                        <td>
                            <textarea name="custom_admin_notice_text" rows="5" cols="50">
                                <?php echo esc_textarea(get_option('custom_admin_notice_text', '')); ?>
                            </textarea>
                            <br>
                            <?php
                            $notice_text = get_option('custom_admin_notice_text', '');
                            if (!empty($notice_text)) {
                                ?>
                                <button type="button" class="button" onclick="removeNotice()">Remove Update</button>
                                <script>
                                    function removeNotice() {
                                        if (confirm('Are you sure you want to remove the Update?')) {
                                            document.querySelector('[name="custom_admin_notice_text"]').value = '';
                                            document.querySelector('.notice-success').remove();
                                        }
                                    }
                                </script>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    } else {
        echo '<p>You do not have permission to edit this Update.</p>';
    }
}

// Function to add the custom settings page as an individual menu page
function cdup_admin_menu() {
    if (current_user_can('activate_plugins')) {
        add_menu_page(
            'Custom Admin Updates',
            'Custom Updates',
            'read', // Change this capability to restrict access
            'custom-admin-updates',
            'cdup_admin_settings_page',
            'dashicons-megaphone',
            3
        );
    }
}

// Hook to add the custom settings menu page
add_action('admin_menu', 'cdup_admin_menu');


// Hook to register settings
add_action('admin_init', 'cdup_admin_updates_settings_init');

function cdup_admin_updates_settings_init() {
    register_setting(
        'custom_admin_notices_settings_group',
        'custom_admin_notice_text',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );
}