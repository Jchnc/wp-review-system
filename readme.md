# Review System

**Contributors**: Jean C. Navarro C. (Jchnc)  
**Tags**: reviews, rating, feedback, testimonial, stars, shortcode  
**Requires at least**: 6.0  
**Tested up to**: 6.7  
**Requires PHP**: 7.4  
**Stable tag**: 1.0.0  
**License**: GPLv2 or later  
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html  

A lightweight and modern review system plugin for WordPress. Collect, manage, and display star-based reviews with front-end forms, average rating breakdown, and template override support.

## Description

**Review System** is a fast, modern, and customizable plugin to collect and display user reviews on your WordPress site. Designed for flexibility and performance, it supports shortcodes, star ratings, custom approval workflow, admin moderation, and full template overrides via your theme.

**Key Features**:

- ⭐ Collect reviews with a front-end form via shortcode
- 📊 Display average rating and breakdown charts
- 🧾 List individual reviews with pagination
- 🧠 Admin interface to approve/reject reviews
- 🧩 Template override system so developers can fully customize output
- 🛡️ Secure form submissions with nonce validation and XSS prevention

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Use the following shortcodes anywhere on your site:

```
[review_form]
[review_breakdown]
[review_list per_page="5"]
```


## Template Overrides

This plugin supports **theme-level template overrides**, similar to how WooCommerce works.

To override any template, copy it from the plugin's `/templates/` folder into your active theme inside a `review-system/` directory.

### Available Templates

| Template | Description |
|----------|-------------|
| `review-form.php` | Frontend review submission form |
| `review-breakdown.php` | Star rating average and breakdown display |
| `review-list.php` | Paginated list of approved reviews |

### How to Override

Example: Override the review form template:

1. Create a folder in your active theme:  
`wp-content/themes/your-theme/review-system/`

2. Copy the file:  
`wp-content/plugins/review-system/templates/review-form.php`

3. Paste it into your theme folder:  
`wp-content/themes/your-theme/review-system/review-form.php`

4. Modify the copied file freely — HTML structure, styles, fields, etc.

**The plugin will automatically use your theme version if it exists. Meaning it will display nothing if the file is empty!**

## Shortcodes

- `[review_form]` — Display the review submission form
- `[review_breakdown]` — Show average rating and star breakdown
- `[review_list per_page="5"]` — List approved reviews with pagination

## FAQ

### Can users leave multiple reviews?

By default, yes. You can customize this via custom logic or hooks.

### Can I moderate reviews?

Yes, reviews are saved with `pending` status and must be approved in the admin panel.

### Can I style it with my own CSS framework?

Absolutely. The markup is clean and minimal. You can override templates and apply Tailwind, Bootstrap, or custom CSS as needed.

### Are there REST API endpoints?

Not yet, but they are planned for a future release.

## Changelog

### 1.0.0
* Initial release with full core features
* Shortcodes for form, breakdown, and review list
* Template override system implemented
* Admin review moderation

## Upgrade Notice

### 1.0.0
Initial release. Safe to install on production sites.

## Screenshots

1. Review submission form (default)
2. Star rating breakdown with average
3. Paginated list of user reviews

## License

This plugin is licensed under the GPLv2 or later.

Copyright (c) 2025