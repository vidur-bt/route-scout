=== Route Scout ===
Contributors: route-scout
Tags: rest-api, rest, api, testing, debugging, developer
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Browse, test, and document your WordPress REST API endpoints without leaving wp-admin.

== Description ==

Route Scout is a developer-focused tool for exploring and testing WordPress REST API endpoints directly from the admin dashboard.

**Features:**

* **Endpoint Browser** — Auto-discovers all registered REST API routes with methods, parameters, and documentation
* **Request Tester** — Send GET, POST, PUT, DELETE, and PATCH requests with automatic WordPress authentication
* **Request Collections** — Save and reload requests for later use
* **Response Inspector** — View formatted or raw JSON responses with status codes and timing data
* **Route Search** — Quickly find endpoints by namespace or path

Perfect for:
- Custom REST endpoint development
- Headless WordPress / decoupled site work
- API integration testing
- Documentation of endpoints
- Debugging permission issues

Route Scout requires `manage_options` capability and is hidden from non-administrators for security.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/route-scout/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Tools → Route Scout**

== Frequently Asked Questions ==

= Can I use Route Scout on production sites? =

Yes, but it's designed for development. Only administrators can access it, and all requests are executed server-side without external calls.

= Does Route Scout store my data? =

Request collections are saved in your browser's localStorage. No data is sent to external servers.

= What versions of WordPress does Route Scout support? =

Route Scout requires WordPress 6.0 or newer and PHP 8.0+.

= Can I test protected endpoints? =

Yes. Route Scout automatically handles WordPress nonces and authentication for all admin-capable users.

= Does this work with custom REST namespaces? =

Yes. Route Scout auto-discovers all registered REST routes, including custom namespaces.

== Changelog ==

= 1.0.0 =
* Initial release
* Endpoint browser with search
* Request tester (GET, POST, PUT, DELETE, PATCH)
* Response inspector (formatted/raw JSON)
* Save and load request collections
* WordPress.org standards compliance

== Support ==

For bug reports, feature requests, or support, please visit the plugin page on WordPress.org.
