# Route Scout

**A WordPress REST API testing tool built into your wp-admin dashboard.**

Route Scout lets you browse every REST API endpoint registered on your WordPress site, send test requests with a click, and inspect responses — all without leaving the admin panel or setting up an external tool like Postman.

| | |
|---|---|
| **Version** | 1.0.0 |
| **Requires WordPress** | 6.0 or higher |
| **Requires PHP** | 8.0 or higher |
| **License** | GPL-2.0-or-later |
| **Access** | Administrators only (`manage_options`) |

---

## Table of Contents

1. [What Is Route Scout?](#what-is-route-scout)
2. [Who Is It For?](#who-is-it-for)
3. [Installation](#installation)
4. [How to Use Route Scout](#how-to-use-route-scout)
   - [Opening the Tool](#opening-the-tool)
   - [Browsing Endpoints](#browsing-endpoints)
   - [Sending a Request](#sending-a-request)
   - [Reading the Response](#reading-the-response)
   - [Saving Requests](#saving-requests)
5. [Features in Detail](#features-in-detail)
6. [Real-World Examples](#real-world-examples)
7. [How It Works Under the Hood](#how-it-works-under-the-hood)
8. [Security](#security)
9. [Frequently Asked Questions](#frequently-asked-questions)
10. [File Structure](#file-structure)
11. [Compatibility](#compatibility)
12. [Roadmap](#roadmap)

---

## What Is Route Scout?

When you build custom WordPress REST API endpoints, you need a way to test them. The usual workflow looks like this:

1. Write your endpoint code
2. Open Postman (or Insomnia, or a browser tab)
3. Manually set up the authentication headers (nonces, cookies)
4. Type out the full URL
5. Send the request
6. Switch back to WordPress to check if it worked

Route Scout eliminates all of that. It lives directly inside your WordPress admin panel, already knows your site's authentication, and automatically discovers every endpoint registered on your installation — including your custom ones.

Think of it as **Postman, but built into WordPress and pre-authenticated**.

---

## Who Is It For?

Route Scout is designed for **WordPress developers** who:

- Build custom REST API endpoints for plugins or themes
- Work on headless or decoupled WordPress sites
- Debug permission issues on REST endpoints
- Want to explore what endpoints WordPress core and installed plugins expose
- Need a quick way to test API calls without leaving wp-admin

You do not need to know how to use Postman or any external API tool. If you can navigate the WordPress admin, you can use Route Scout.

---

## Installation

### Option 1: Manual Installation

1. Download or clone the `route-scout` folder
2. Copy the entire folder to your WordPress plugins directory:
   ```
   /wp-content/plugins/route-scout/
   ```
3. In your WordPress admin, go to **Plugins → Installed Plugins**
4. Find **Route Scout** and click **Activate**

### Option 2: WP-CLI

```bash
# Copy to plugins directory
cp -r route-scout /path/to/wordpress/wp-content/plugins/

# Activate via WP-CLI
wp plugin activate route-scout
```

### Verify It's Working

After activation, go to **Tools → Route Scout** in your WordPress admin menu. If you see the Route Scout interface with a list of endpoints, everything is working correctly.

---

## How to Use Route Scout

### Opening the Tool

After activating the plugin, navigate to:

**WordPress Admin → Tools → Route Scout**

You will see a three-panel layout:

```
┌─────────────────┬────────────────────┬──────────────────────┐
│   ENDPOINTS     │     REQUEST        │      RESPONSE        │
│   (left panel)  │   (center panel)   │    (right panel)     │
│                 │                    │                      │
│  Browse all     │  Build and send    │  View the result     │
│  REST routes    │  your request      │  of your request     │
└─────────────────┴────────────────────┴──────────────────────┘
```

---

### Browsing Endpoints

The **left panel** shows every REST API endpoint registered on your WordPress installation.

**What you see:**

- Routes are grouped by namespace (e.g., `wp/v2` for core, `wc/v3` for WooCommerce, or your custom namespace)
- Each route shows colored method badges indicating which HTTP methods it accepts:
  - `GET` — blue
  - `POST` — green
  - `PUT` — orange
  - `DELETE` — red
  - `PATCH` — teal
- The full route path (e.g., `/wp/v2/posts`, `/wp/v2/posts/(?P<id>[\d]+)`)

**Searching:**

Use the search box at the top of the left panel to filter routes by name. For example, typing `posts` will show all routes that include "posts" in the path.

**Selecting a route:**

Click any route to load it into the center panel. The method and path will be pre-filled automatically.

---

### Sending a Request

The **center panel** is where you build your request before sending it.

**Step 1 — Choose the HTTP method**

Use the dropdown on the left to select the method:
- `GET` — Retrieve data (e.g., fetch a list of posts)
- `POST` — Create new data (e.g., create a new post)
- `PUT` — Replace existing data completely
- `DELETE` — Remove data (e.g., delete a post)
- `PATCH` — Update part of existing data

**Step 2 — Set the path**

The path field shows the endpoint URL. It is pre-filled when you click a route in the left panel, but you can also type directly. For example:

```
/wp/v2/posts
/wp/v2/posts/42
/my-plugin/v1/orders
```

**Step 3 — Add parameters (Params tab)**

When you click a route, its available parameters appear as input fields automatically. Fill in the values you want to send. For example:

- For `GET /wp/v2/posts`: you might set `per_page` = `5` to fetch 5 posts
- For `POST /wp/v2/posts`: you would set `title` = `My New Post` and `status` = `draft`

**Step 4 — Add a body (Body tab)**

For `POST`, `PUT`, and `PATCH` requests, you can also provide a raw JSON body. Click the **Body** tab and paste or type JSON:

```json
{
  "title": "My New Post",
  "content": "Hello world",
  "status": "draft"
}
```

**Step 5 — Send**

Click the **Send Request** button. Route Scout handles authentication automatically — no need to add nonces or cookies manually.

---

### Reading the Response

The **right panel** displays the result after you send a request.

**Response info bar** shows:
- **Status code** — e.g., `200` (success), `201` (created), `400` (bad request), `403` (forbidden), `404` (not found)
- **Response time** — how many milliseconds the request took to execute

**Formatted tab** shows the response body as indented, readable JSON:

```json
{
  "id": 42,
  "title": {
    "rendered": "My New Post"
  },
  "status": "draft",
  "author": 1
}
```

**Raw tab** shows the response as a single line of JSON — useful for copying or inspecting the exact output.

**Common status codes and what they mean:**

| Code | Meaning | What to do |
|------|---------|------------|
| `200` | Success | Request worked as expected |
| `201` | Created | New resource was created successfully |
| `400` | Bad Request | Check your parameters — something is missing or invalid |
| `401` | Unauthorized | You are not logged in |
| `403` | Forbidden | You do not have the required capability for this endpoint |
| `404` | Not Found | The route path is incorrect, or the resource does not exist |
| `500` | Server Error | Something went wrong on the server — check error logs |

---

### Saving Requests

The **Saved** tab in the center panel lets you save requests for later reuse.

**To save a request:**

1. Set up your method, path, and parameters
2. Click the **Saved** tab
3. Click **Save Current Request**
4. Enter a name (e.g., `Get Latest Posts`, `Create Draft Post`)
5. Click OK

**To load a saved request:**

1. Click the **Saved** tab
2. Find the request by name
3. Click **Load**
4. The method, path, and params will be restored
5. Click **Send Request** to run it

**To delete a saved request:**

1. Click the **Saved** tab
2. Click **Delete** next to the request you want to remove

**Where are saved requests stored?**

In your browser's `localStorage`. This means:
- They persist across browser sessions automatically
- They are private to your browser — not shared with other users or devices
- Clearing your browser's site data will remove them

---

## Features in Detail

### Endpoint Browser

- Automatically discovers **all registered REST API endpoints** on your WordPress site — including WordPress core, installed plugins (WooCommerce, ACF, Yoast, etc.), and your custom endpoints
- Groups routes by namespace so you can easily find what you are looking for
- Color-coded HTTP method badges (GET, POST, PUT, DELETE, PATCH)
- Live search filters routes as you type
- Click any route to instantly populate the request builder

### Request Builder

- Supports all five HTTP methods: `GET`, `POST`, `PUT`, `DELETE`, `PATCH`
- Path field is editable — you can manually type a route or modify the one from the browser
- **Params tab**: Parameter names are auto-detected from the selected route; just fill in the values
- **Body tab**: For write operations, paste raw JSON directly
- One-click **Send Request** with automatic authentication

### Response Inspector

- Shows HTTP status code and response time in milliseconds
- **Formatted view**: Pretty-printed JSON with indentation for easy reading
- **Raw view**: Single-line JSON for copying or exact inspection
- Handles all response types — arrays, objects, empty responses, and error messages

### Request Collections

- Save any request with a custom name
- Reload saved requests with one click
- Delete individual saved requests
- Stored in browser `localStorage` — persists across sessions

### Search & Filter

- Search box filters the endpoint list in real time
- Works across namespaces, methods, and path names
- Useful when a site has dozens or hundreds of registered routes

### Authentication Handling

- Route Scout uses `wp.apiFetch` with WordPress REST nonces under the hood
- You do not need to set up API keys, tokens, or any headers manually
- As long as you are logged in as an administrator, all requests are automatically authenticated

---

## Real-World Examples

### Example 1 — Fetch a list of posts

1. Search for `posts` in the left panel
2. Click `/wp/v2/posts` (GET)
3. In the Params tab, set `per_page` = `3`
4. Click **Send Request**
5. Response shows the 3 most recent posts as JSON

### Example 2 — Create a new draft post

1. Click `/wp/v2/posts` in the left panel, select `POST` method
2. In the Params tab, set `title` = `My Test Post` and `status` = `draft`
3. Click **Send Request**
4. Response shows the newly created post with its ID (e.g., `"id": 87`)
5. Check your WordPress Posts list — the draft will be there

### Example 3 — Debug a 403 on a custom endpoint

1. Your endpoint `/my-plugin/v1/orders` is returning 403
2. Search for `orders` in the left panel
3. Click the route
4. Click **Send Request** while logged in as Admin
5. If it returns 200, the issue is with the user role, not the code
6. Try the same request logged in as a lower-privilege user to confirm

### Example 4 — Test a WooCommerce endpoint

1. Search for `wc/` in the left panel
2. Browse available WooCommerce endpoints
3. Click `/wc/v3/products` (GET)
4. Click **Send Request**
5. Response shows your WooCommerce products as JSON

### Example 5 — Save frequently-used requests

1. Set up a request you use often (e.g., `GET /wp/v2/users?per_page=10`)
2. Click the **Saved** tab → **Save Current Request** → name it `All Users`
3. Next time, open the Saved tab, click **Load**, and send immediately

---

## How It Works Under the Hood

Understanding the internals helps you use the tool more effectively and trust it completely.

### Route Discovery

When Route Scout loads, it makes one internal REST API call to its own discovery endpoint:

```
GET /wp-json/route-scout/v1/routes
```

This endpoint runs on the server and calls `rest_get_server()->get_routes()` — the same WordPress function that powers the REST API index. It returns every registered route with its methods, accepted parameters, and namespace. The result is what populates the left panel.

### Request Proxying

When you click **Send Request**, the JavaScript does **not** make a direct call to your endpoint. Instead, it sends your request details to a secure internal proxy:

```
POST /wp-json/route-scout/v1/proxy
```

The proxy receives your method, path, and parameters, then executes the request **server-side** using WordPress's `rest_do_request()` function. This is the same function WordPress uses internally, which means:

- Authentication is handled automatically by WordPress
- No extra HTTP round-trips
- No credentials ever leave the server
- The response is returned to the browser with status code and timing

### Why a proxy instead of a direct call?

A direct call from your browser to, say, `/wp-json/wp/v2/posts` would work for `GET` requests but would require manually managing nonces for write operations. Using the proxy means Route Scout handles authentication identically for all methods, with no setup from you.

### Saved Collections

Request collections are stored in your browser's `localStorage` as a JSON array. Nothing is saved to the WordPress database. The data never leaves your browser.

---

## Security

Route Scout is built with security as a first principle.

### Who can access it?

Only users with the `manage_options` capability (WordPress Administrators by default). The plugin enforces this at two levels:
1. The admin menu page itself checks `manage_options` before rendering
2. Both REST API endpoints (`/routes` and `/proxy`) check `manage_options` before executing

### Does it make external requests?

No. All requests stay on your own server. The proxy uses `rest_do_request()`, which is a direct internal PHP function call — not an HTTP request. No data is sent to any third-party service.

### Does it store sensitive data?

No. Saved request collections are stored only in your browser's `localStorage`. The plugin does not write anything to the WordPress database.

### Is it safe on production?

Route Scout is safe to run on production — it cannot be accessed by non-administrators, and it does not modify your site's data unless you intentionally send a write request (POST, PUT, DELETE) to an endpoint that does so. That said, it is designed for development use. If you are not actively using it on production, deactivate it to reduce your attack surface.

---

## Frequently Asked Questions

**Q: I don't see my custom endpoint in the list. Why?**

Your endpoint must be registered via `register_rest_route()` inside a `rest_api_init` hook. If it is registered after that hook fires, or conditionally in a way that does not run during the admin request, it will not appear. Check that your endpoint registration runs unconditionally on `rest_api_init`.

**Q: I'm getting a 403 error on an endpoint that should work.**

A 403 means the `permission_callback` for that endpoint returned `false` for your current user. Common causes:
- The endpoint requires a capability your admin account has, but a test user does not
- The endpoint checks for a custom capability that has not been assigned
- The `permission_callback` checks `current_user_can()` but your user does not meet the condition

**Q: Why does the endpoint list show routes with `(?P<id>[\d]+)` in the path?**

That is a URL parameter pattern used by the WordPress REST API. It means the route accepts a numeric ID in that position. For example, `/wp/v2/posts/(?P<id>[\d]+)` matches `/wp/v2/posts/42`. When testing this route, enter the actual ID (e.g., `42`) in the `id` parameter field.

**Q: Can I use Route Scout on WordPress Multisite?**

Yes. Route Scout works on individual sites within a Multisite network. Each site has its own REST API and its own set of registered routes. Access each site's admin panel separately to test its endpoints.

**Q: My saved requests disappeared. What happened?**

Saved requests are stored in browser `localStorage`. They are cleared when you clear your browser's site data, cookies, or cache. They also do not transfer between different browsers or devices.

**Q: Does Route Scout work with the WooCommerce REST API?**

Yes. If WooCommerce is installed and active, its endpoints (`/wc/v3/...`) will appear in the endpoint browser automatically.

**Q: Can I send file uploads?**

Not in the current version. Route Scout supports JSON params and raw JSON body, but not multipart form data. File upload support is planned for a future version.

**Q: Does it work with JWT or OAuth authentication?**

Route Scout uses WordPress's built-in cookie/nonce authentication. If your endpoint requires a different authentication method (e.g., JWT), you would need to configure that separately. For endpoints that use WordPress's standard `permission_callback`, Route Scout handles authentication automatically.

---

## File Structure

```
route-scout/
│
├── route-scout.php                   Plugin entry point and bootstrap
│
├── includes/
│   ├── class-admin.php               Registers admin menu page and loads assets
│   ├── class-rest-proxy.php          Proxies test requests to REST endpoints
│   └── class-route-discovery.php     Discovers and returns all registered routes
│
├── assets/
│   ├── js/
│   │   └── route-scout.js            All user interface logic (Vanilla JavaScript)
│   └── css/
│       └── route-scout.css           Admin panel styles (responsive)
│
├── readme.txt                        WordPress.org plugin directory listing
├── README.md                         This file
└── .gitignore                        Git configuration
```

**Reference documentation** (in `/docs` folder, not required for the plugin to run):

```
docs/
├── QUICK-START.md                    5-minute setup guide
├── SETUP.md                          Local development setup
├── BUILD-SUMMARY.md                  Architecture and design decisions
├── WORDPRESS-ORG-STANDARDS.md        WordPress.org compliance checklist
├── SUBMISSION-CHECKLIST.md           Step-by-step submission guide
└── INDEX.md                          Documentation index
```

---

## Compatibility

| Component | Supported versions |
|-----------|-------------------|
| WordPress | 6.0 and above |
| PHP | 8.0 and above |
| Chrome | Latest 2 versions |
| Firefox | Latest 2 versions |
| Safari | Latest 2 versions |
| Edge | Latest 2 versions |
| WordPress Multisite | Yes |
| WooCommerce REST API | Yes |
| Custom plugin REST endpoints | Yes |
| Custom theme REST endpoints | Yes |

---

## Roadmap

These features are not in v1.0 but are planned for future releases:

- Export saved collections as Postman or Insomnia format
- Import collections from a JSON file
- Request history log (persistent across sessions)
- Schema viewer — read the `get_item_schema` for any endpoint
- Response body search and filtering
- WP-CLI command (`wp route-scout list`)
- Dark mode

---

## Support

- **Bug reports and questions**: [WordPress.org plugin support forum](https://wordpress.org/support/plugin/route-scout/)
- **Security vulnerabilities**: Report privately via the WordPress.org security disclosure form — do not post publicly

---

## License

Route Scout is free and open source software, released under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license.
