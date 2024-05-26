<?php
/*
Plugin Name: Custom Data Table with SVG Map
Description: A plugin to manage a custom data table and display an interactive SVG map based on the table data.
Version: 1.0
Author: Your Name
*/

// Create custom database table on plugin activation
register_activation_hook(__FILE__, 'cdt_create_table');
function cdt_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data_table';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        start datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        region varchar(100) NOT NULL,
        type varchar(100) NOT NULL,
        impact varchar(100) NOT NULL,
        end datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        source varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Add menu item to admin panel
add_action('admin_menu', 'cdt_add_menu');
function cdt_add_menu() {
    add_menu_page('Custom Data Table', 'Data Table', 'manage_options', 'custom-data-table', 'cdt_admin_page', 'dashicons-table', 6);
}

function cdt_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data_table';

    // Handle form submission
    if (isset($_POST['cdt_submit'])) {
        $start = sanitize_text_field($_POST['start']);
        $region = sanitize_text_field($_POST['region']);
        $type = sanitize_text_field($_POST['type']);
        $impact = sanitize_text_field($_POST['impact']);
        $end = sanitize_text_field($_POST['end']);
        $source = esc_url($_POST['source']);

        $wpdb->insert($table_name, array(
            'start' => $start,
            'region' => $region,
            'type' => $type,
            'impact' => $impact,
            'end' => $end,
            'source' => $source
        ));
    }

    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, array('id' => $id));
    }

    // Handle edit form submission
    if (isset($_POST['cdt_update'])) {
        $id = intval($_POST['id']);
        $start = sanitize_text_field($_POST['start']);
        $region = sanitize_text_field($_POST['region']);
        $type = sanitize_text_field($_POST['type']);
        $impact = sanitize_text_field($_POST['impact']);
        $end = sanitize_text_field($_POST['end']);
        $source = esc_url($_POST['source']);

        $wpdb->update($table_name, array(
            'start' => $start,
            'region' => $region,
            'type' => $type,
            'impact' => $impact,
            'end' => $end,
            'source' => $source
        ), array('id' => $id));
    }

    // Fetch data for display and edit
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    $edit_row = null;
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $edit_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_GET['id'])));
    }
    ?>
    <div class="wrap">
        <h2>Custom Data Table</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="start">Start</label></th>
                    <td><input name="start" type="datetime-local" id="start" value="<?php echo esc_attr($edit_row ? date('Y-m-d\TH:i', strtotime($edit_row->start)) : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="region">Region</label></th>
                    <td>
                        <select name="region" id="region">
                            <option value="Barishal" <?php selected($edit_row && $edit_row->region == 'Barishal'); ?>>Barishal</option>
                            <option value="Chittagong" <?php selected($edit_row && $edit_row->region == 'Chittagong'); ?>>Chittagong</option>
                            <option value="Dhaka" <?php selected($edit_row && $edit_row->region == 'Dhaka'); ?>>Dhaka</option>
                            <option value="Khulna" <?php selected($edit_row && $edit_row->region == 'Khulna'); ?>>Khulna</option>
                            <option value="Mymensingh" <?php selected($edit_row && $edit_row->region == 'Mymensingh'); ?>>Mymensingh</option>
                            <option value="Rajshahi" <?php selected($edit_row && $edit_row->region == 'Rajshahi'); ?>>Rajshahi</option>
                            <option value="Rangpur" <?php selected($edit_row && $edit_row->region == 'Rangpur'); ?>>Rangpur</option>
                            <option value="Sylhet" <?php selected($edit_row && $edit_row->region == 'Sylhet'); ?>>Sylhet</option>
                            <option value="Bangladesh" <?php selected($edit_row && $edit_row->region == 'Bangladesh'); ?>>Whole Country</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="type">Type</label></th>
                    <td>
                        <select name="type" id="type">
                            <option value="Mobile Data Shutdown" <?php selected($edit_row && $edit_row->type == 'Mobile Data Shutdown'); ?>>Mobile Data Shutdown</option>
                            <option value="Social Media Block" <?php selected($edit_row && $edit_row->type == 'Social Media Block'); ?>>Social Media Block</option>
                            <option value="Throttling" <?php selected($edit_row && $edit_row->type == 'Throttling'); ?>>Throttling</option>
                            <option value="Total Blockout" <?php selected($edit_row && $edit_row->type == 'Total Blockout'); ?>>Total Blockout</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="impact">Impact</label></th>
                    <td>
                        <select name="impact" id="impact">
                            <option value="Regional" <?php selected($edit_row && $edit_row->impact == 'Regional'); ?>>Regional</option>
                            <option value="Countrywide" <?php selected($edit_row && $edit_row->impact == 'Countrywide'); ?>>Countrywide</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="end">End</label></th>
                    <td><input name="end" type="datetime-local" id="end" value="<?php echo esc_attr($edit_row ? date('Y-m-d\TH:i', strtotime($edit_row->end)) : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="source">Source</label></th>
                    <td><input name="source" type="url" id="source" value="<?php echo esc_attr($edit_row ? $edit_row->source : ''); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php if ($edit_row): ?>
                <input type="hidden" name="id" value="<?php echo esc_attr($edit_row->id); ?>">
                <p class="submit"><input type="submit" name="cdt_update" id="submit" class="button button-primary" value="Update"></p>
            <?php else: ?>
                <p class="submit"><input type="submit" name="cdt_submit" id="submit" class="button button-primary" value="Add Data"></p>
            <?php endif; ?>
        </form>
        <h2>Data Entries</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Start</th>
                    <th>Region</th>
                    <th>Type</th>
                    <th>Impact</th>
                    <th>End</th>
                    <th>Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->start); ?></td>
                        <td><?php echo esc_html($row->region); ?></td>
                        <td><?php echo esc_html($row->type); ?></td>
                        <td><?php echo esc_html($row->impact); ?></td>
                        <td><?php echo esc_html($row->end); ?></td>
                        <td><a href="<?php echo esc_url($row->source); ?>" target="_blank">Source</a></td>
                        <td>
                            <a href="?page=custom-data-table&action=edit&id=<?php echo esc_attr($row->id); ?>" class="button">Edit</a>
                            <a href="?page=custom-data-table&action=delete&id=<?php echo esc_attr($row->id); ?>" class="button" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Shortcode to display the data table on the frontend
add_shortcode('cdt_frontend_table', 'cdt_display_frontend_table');
function cdt_display_frontend_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data_table';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    ob_start();
    ?>
    <div class="cdt-frontend-table">
        <h2>Internet Shutdown Logs</h2>
        <table>
            <thead>
                <tr>
                    <th>Start</th>
                    <th>Region</th>
                    <th>Type</th>
                    <th>Impact</th>
                    <th>End</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html(date('d.m.Y', strtotime($row->start))); ?></td>
                        <td><?php echo esc_html($row->region); ?></td>
                        <td><?php echo esc_html($row->type); ?></td>
                        <td><?php echo esc_html($row->impact); ?></td>
                        <td><?php echo esc_html(date('d.m.Y', strtotime($row->end))); ?></td>
                        <td><a href="<?php echo esc_url($row->source); ?>" target="_blank"><img src="<?php echo plugins_url('info-icon.png', __FILE__); ?>" alt="Source"></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

// Enqueue custom scripts and styles
add_action('wp_enqueue_scripts', 'cdt_enqueue_scripts');
function cdt_enqueue_scripts() {
    wp_enqueue_style('cdt-styles', plugins_url('style.css', __FILE__));
    wp_enqueue_script('cdt-scripts', plugins_url('script.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('cdt-scripts', 'cdt_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'regions' => cdt_get_region_counts(),
    ));
}

// Get region counts
function cdt_get_region_counts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data_table';
    $regions = ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh'];
    $counts = [];

    foreach ($regions as $region) {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE region = %s", $region));
        $counts[$region] = $count;
    }

    return $counts;
}

// AJAX handler to get region data
add_action('wp_ajax_nopriv_get_region_data', 'cdt_get_region_data');
add_action('wp_ajax_get_region_data', 'cdt_get_region_data');
function cdt_get_region_data() {
    echo json_encode(cdt_get_region_counts());
    wp_die();
}

// Add shortcode to display the SVG map
add_shortcode('cdt_svg_map', 'cdt_display_svg_map');
function cdt_display_svg_map() {
    ob_start();
    ?>
    <div class="cdt-svg-container">
        <?php include plugin_dir_path(__FILE__) . 'bangladesh-map.svg'; ?>
    </div>
    <?php
    return ob_get_clean();
}
