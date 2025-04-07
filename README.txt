=== Spindle Product ===
Contributors: yourname  
Tags: woocommerce, api, product sync, custom api, cron job  
Requires at least: 5.0  
Tested up to: 6.5  
Requires PHP: 7.4  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

A custom WooCommerce integration that imports and syncs products from a custom API to a dedicated database table, with automatic syncing and cleanup.

== Description ==

Spindle Product is a WooCommerce extension that connects your store to a custom API (e.g., https://fakestoreapi.com/products), imports the products, and displays them in WooCommerce automatically. Ideal for large catalogs (20,000+ products), it uses a custom database table and supports image syncing, cron automation, and WooCommerce-ready product entries.

---

== 🔧 CORE FEATURES ==

1. **Custom API Integration**
- Fetches product data from a custom JSON API
- Parses and processes JSON data structure

2. **Custom Database Table**
- Creates a custom table: `wp_spindle_api_products`
- Stores each product in structured columns:
  - `api_product_id`
  - `title`
  - `price`
  - `description`
  - `category`
  - `image`
  - `rating_rate`
  - `rating_count`
  - `created_at`

3. **Automatic Table Maintenance**
- Deletes old data every 24 hours
- Prevents duplication and stale entries
- Fetches and inserts fresh API data automatically

4. **WooCommerce Product Sync**
- Inserts or updates WooCommerce products
- Matches products by SKU: `spindle-{api_id}`
- Sets:
  - Title
  - Price
  - Description
  - Category
  - Status: `publish`
  - Product Type: `simple`

5. **Image Syncing**
- Downloads product image from the API
- Uploads it to the Media Library
- Sets as product’s featured image

6. **WooCommerce Product Visibility**
- Products show up in:
  - WooCommerce → Products
  - Your front-end store
  - Search, filter, category pages
- Optionally tags synced products with “Spindle”

---

== 🔁 AUTOMATION FEATURES ==

7. **Daily Cron Sync**
- Automatically syncs once daily
- Uses WordPress Cron (`wp_schedule_event`)

8. **Plugin Activation & Deactivation**
- On activation: creates the custom database table
- On deactivation: cleans up and deletes the table

---



== Installation ==

1. Upload the plugin to the `/wp-content/plugins/spindle-product` directory.
2. Activate it through the 'Plugins' menu in WordPress.
3. The plugin will create a table and automatically start syncing.

== Frequently Asked Questions ==

= Does this work with large catalogs (20K+ products)? =
Yes! It uses a custom database table for better performance and WooCommerce sync.

= Does it support scheduled syncing? =
Yes. WordPress cron runs the sync once per day.

= Can I remove data when the plugin is disabled? =
Yes. The table is deleted on deactivation.

== Changelog ==

= 1.0 =
* Initial release with full API-to-WooCommerce sync
* Daily cron integration
* Image downloading and attachment
* WooCommerce product creation and update

== Upgrade Notice ==

= 1.0 =
First release. Fully tested with WooCommerce 8.x and WordPress 6.5.

== License ==

GPLv2 or later
