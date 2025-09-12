# FM WooCommerce Product Category Exclusions

Sometimes we use the **WooCommerce product category taxonomy** for purely organizational purposes.  
For example, you might create categories to group products for backend reporting, admin workflows, or internal tagging—categories that you **don’t want customers to see** on product pages.  

By default, WooCommerce shows *all* product categories in the product meta section.  
**FM WooCommerce Product Category Exclusions** gives you a simple way to hide selected categories from public view.

---

## Features

- Adds a new **Category Exclusions** settings page under **WooCommerce → Category Exclusions**.
- Lets administrators select which categories should be excluded from product pages.
- Automatically removes excluded categories from:
  - The product meta section (`Category:` or `Categories:` list).
  - Any template or theme that calls:
    - `$product->get_category_ids()`
    - `$product->get_categories()`
    - `wc_get_product_category_list()`
- Works with Astra, GeneratePress, and other WooCommerce-compatible themes.
- Exclusions are **site-wide** and apply consistently without requiring theme edits.
- Stored as a single option in the database (`fm_wcpce_excluded_product_cat_ids`), with autoload disabled for performance.

---

## Installation

1. Upload the plugin folder to your `/wp-content/plugins/` directory  
   or install directly through the WordPress admin.

2. Activate the plugin through **Plugins → Installed Plugins**.

3. Go to **WooCommerce → Category Exclusions** in the WordPress admin menu.

---

## Usage

1. On the **Category Exclusions** page, you’ll see a scrollable list of all product categories.  
2. Check the categories you want to **hide from customers**.  
   - These categories will no longer appear on single product pages, or anywhere WooCommerce normally displays product categories.  
3. Click **Save Exclusions**.  
4. That’s it — the exclusions are now active across your store.

---

## Example Use Cases

- Create an internal-only category for products you’re testing but don’t want visible on the site.  
- Use categories for **stock management** or **internal workflows** without showing them to customers.  
- Hide **“Brands”** or **“Meta Categories”** that exist only for menu-building or filtering logic.  

---

## Technical Notes

- Exclusions are enforced using WooCommerce and WordPress filters:
  - `woocommerce_product_get_category_ids`
  - `woocommerce_product_get_categories`
  - `get_the_terms`
  - `wp_get_object_terms`
- This ensures excluded categories are **removed at the data level** before output.  
  Your theme doesn’t need to change its templates.  
- Option storage:
  - Name: `fm_wcpce_excluded_product_cat_ids`
  - Type: serialized array of category IDs
  - Autoload: **off**

---

## Requirements

- WordPress 6.6+
- WooCommerce 7.0+
- PHP 8.0 or later

---

## Support

For issues or feature requests, please open an issue at:  
[https://github.com/gcordner/fm-woocommerce-product-category-exclusions](https://github.com/gcordner/fm-woocommerce-product-category-exclusions)

---

## License

This plugin is licensed under the [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.txt).  
You are free to use, modify, and redistribute it under the same license.

