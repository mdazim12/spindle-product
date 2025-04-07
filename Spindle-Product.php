<?php
/**
 * Plugin Name: Spindle Product
 * Description: Syncs and stores external API product data in a custom table, then updates WooCommerce products.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Activation hook to create custom table
function spindle_create_custom_api_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'spindle_api_products';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        api_product_id BIGINT(20) NOT NULL,
        title TEXT NOT NULL,
        price FLOAT NOT NULL,
        description LONGTEXT,
        category VARCHAR(255),
        image TEXT,
        rating_rate FLOAT,
        rating_count INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'spindle_create_custom_api_table');

// Deactivation hook to drop the custom table
function spindle_delete_custom_api_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'spindle_api_products';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_deactivation_hook(__FILE__, 'spindle_delete_custom_api_table');

// Fetch and store API data if 24 hours passed
function spindle_fetch_api_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'spindle_api_products';

    // Check if last sync was within 24 hours
    $last_entry = $wpdb->get_row("SELECT created_at FROM $table_name ORDER BY created_at DESC LIMIT 1");
    if ($last_entry && strtotime($last_entry->created_at) > time() - 86400) {
        return;
    }

    // Clear old data
    $wpdb->query("TRUNCATE TABLE $table_name");

    $api_url = 'https://fakestoreapi.com/products';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) return;

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($data)) {
        foreach ($data as $item) {
            $wpdb->insert($table_name, [
                'api_product_id' => $item['id'],
                'title' => $item['title'],
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $item['category'],
                'image' => $item['image'],
                'rating_rate' => $item['rating']['rate'] ?? 0,
                'rating_count' => $item['rating']['count'] ?? 0
            ]);
        }
    }
}

// Attach image from URL to product
function spindle_attach_image_from_url($url, $post_id) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $tmp = download_url($url);
    if (is_wp_error($tmp)) return false;

    $file_array = [
        'name'     => basename($url),
        'tmp_name' => $tmp
    ];

    $id = media_handle_sideload($file_array, $post_id);

    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    return $id;
}

// Update or create WooCommerce products
function spindle_update_wc_products() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'spindle_api_products';

    $products = $wpdb->get_results("SELECT * FROM $table_name");

    foreach ($products as $item) {
        $title = $item->title;
        $price = $item->price;
        $sku = 'spindle-' . $item->api_product_id;
        $description = $item->description;
        $category_name = $item->category;
        $image_url = $item->image;

        if (!$sku || !$title || !$price) continue;

        $existing_id = wc_get_product_id_by_sku($sku);

        if ($existing_id) {
            $product = wc_get_product($existing_id);
        } else {
            $product = new WC_Product_Simple();
            $product->set_sku($sku);
        }

        $product->set_name($title);
        $product->set_regular_price($price);
        $product->set_description($description);
        $product->set_status('publish');
        $product_id = $product->save();

        if ($category_name) {
            wp_set_object_terms($product_id, $category_name, 'product_cat', false);
        }

        if ($image_url) {
            $image_id = spindle_attach_image_from_url($image_url, $product_id);
            if ($image_id) {
                set_post_thumbnail($product_id, $image_id);
            }
        }
    }
}

add_action('init', 'spindle_fetch_api_data');
add_action('init', 'spindle_update_wc_products');

// Scheduler (optional)
function spindle_schedule_product_sync() {
    if (!wp_next_scheduled('spindle_run_product_sync')) {
        wp_schedule_event(time(), 'daily', 'spindle_run_product_sync');
    }
}
add_action('wp', 'spindle_schedule_product_sync');
add_action('spindle_run_product_sync', 'spindle_fetch_api_data');
add_action('spindle_run_product_sync', 'spindle_update_wc_products');
