# 🌀 Spindle Product – WooCommerce API Sync Plugin

**A powerful WordPress plugin that fetches, stores, and syncs products from an external API directly into WooCommerce. Built to handle large catalogs (20,000+ products).**

---

## 🔧 Core Features

### 1. Custom API Integration
- Fetches product data from a custom JSON API
- Decodes and processes the API response automatically

### 2. Custom Database Table
- Creates a custom table: `wp_spindle_api_products`
- Stores each product in its own row with structured columns:
  - `api_product_id`, `title`, `price`, `description`, `category`, `image`, `rating_rate`, `rating_count`, `created_at`

### 3. Automatic Data Refresh (Every 24 Hours)
- Deletes old data daily
- Refetches and repopulates the table with fresh product data

### 4. WooCommerce Product Sync
- Inserts or updates WooCommerce products using API data
- Matches products using SKU format: `spindle-{api_id}`
- Syncs:
  - Product Title
  - Price
  - Description
  - Category
  - Status: Published
  - Type: Simple

### 5. Image Syncing
- Downloads product images from the API
- Uploads to Media Library
- Sets as product featured image

### 6. Product Visibility in WooCommerce
- Synced products appear in:
  - **WooCommerce → Products**
  - Storefront (Shop Page, Category, Filters, Search)
- Optionally adds a **“Spindle”** product tag

---

## 🔁 Automation

### ✔️ Daily Cron Job
- Automatically syncs data every 24 hours via `wp_schedule_event`

### ✔️ Activation & Deactivation Cleanup
- On Activation:
  - Creates custom database table
- On Deactivation:
  - Deletes custom table to keep your DB clean

---


## 🚀 Installation

1. Upload the plugin folder to `/wp-content/plugins/spindle-product`
2. Activate it via **Plugins** in the WordPress dashboard
3. WooCommerce products will begin syncing automatically (daily)

---

## 📦 Requirements

- WordPress 5.0+
- WooCommerce 4.0+
- PHP 7.4+

---

## 📜 License

GPLv2 or later – [GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html)

---

## 🙌 Credits

Made with ❤️ for high-performance WooCommerce stores.
