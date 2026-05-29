# Route Scout — Quick Start Guide

Get Route Scout running in 5 minutes.

## Installation

### Step 1: Copy to WordPress
```bash
# If you have WordPress installed locally
cp -r route-scout /path/to/wordpress/wp-content/plugins/
```

Or manually:
1. Download/clone the plugin to your WordPress plugins directory
2. Your path should be: `/wp-content/plugins/route-scout/route-scout.php`

### Step 2: Activate
```bash
# Via WP-CLI (fastest)
wp plugin activate route-scout

# Via wp-admin
# 1. Go to Plugins → Installed Plugins
# 2. Find "Route Scout"
# 3. Click "Activate"
```

### Step 3: Access
Navigate to **Tools → Route Scout** in your WordPress admin dashboard.

## Basic Usage

### Browsing Routes

1. The left panel lists all registered REST API routes
2. Routes are grouped by namespace (e.g., `wp/v2`, custom namespaces)
3. Each route shows:
   - Method badges (GET=blue, POST=green, DELETE=red)
   - Full route path
4. **Search**: Type in the search box to filter by path or namespace

### Testing a Route

1. **Click a route** in the left panel
   - Method and path auto-populate
   - Parameters appear in the center panel

2. **Add parameters** (if needed)
   - Click the "Params" tab
   - Fill in parameter values

3. **Send the request**
   - Click "Send Request" button
   - Status code and response appear on the right

### Examples

#### Get All Posts
1. Click `/wp/v2/posts` (GET method)
2. Click "Send Request"
3. Response shows array of posts

#### Create a Draft Post
1. Click `/wp/v2/posts` (POST method)
2. In Params tab, set `title` = "My New Post"
3. Click "Send Request"
4. Response shows the created post with ID

#### Delete a Post
1. Click `/wp/v2/posts/{id}` (DELETE method)
2. In Params tab, set `id` = post number (e.g., 42)
3. Click "Send Request"
4. Response shows deleted post data or 200 status

## Saving Requests

### Save
1. After sending a request, click "Save Current Request"
2. Enter a name (e.g., "Get Recent Posts")
3. Click Save
4. Request is saved in your browser

### Load
1. Click the "Saved" tab
2. Click "Load" on any saved request
3. Fields auto-populate
4. Send it again without re-typing

### Delete
1. Click the "Saved" tab
2. Click "Delete" on a saved request
3. It's removed from your collection

**Note**: Collections are stored in your browser's localStorage. They persist across sessions but are local to this browser/computer.

## Troubleshooting

### I see "No routes found"
- The REST API may not be enabled
- Try accessing `/wp-json/wp/v2/posts` directly in your browser
- You should see JSON post data
- If not, REST API is disabled

### I get a 403 error
- The endpoint requires specific capabilities
- You must be logged in as an Administrator
- Some endpoints require custom capabilities

### Response is empty
- Some routes may return 404 if the resource doesn't exist
- Try a core route like `/wp/v2/posts` first

### Saved requests disappeared
- They're stored in browser localStorage
- Clearing browser cache/cookies deletes them
- Use browser data export (download) to back them up

## Tips & Tricks

### Test Custom Endpoints
Route Scout auto-discovers all REST routes, including:
- Plugin custom endpoints
- Theme-registered endpoints
- WooCommerce endpoints (if WooCommerce is installed)

Just search in the left panel!

### Use for Documentation
Save your most common API requests and share the exported collection with your team:
1. Save multiple requests
2. In "Saved" tab, export collection as JSON
3. Share with teammates

### Debug Permission Issues
If you get 403 errors:
1. Check the endpoint's `permission_callback`
2. Route Scout shows the required capability
3. Log in as a higher-privileged user if needed

### Monitor Response Times
Each response shows `time_ms` (milliseconds to execute):
- < 100ms: Excellent
- 100-500ms: Good
- 500-1000ms: Slow (may need optimization)
- \> 1000ms: Very slow (investigate index/query)

## What Route Scout Can't Do

- ❌ Modify WordPress core (it's read-only)
- ❌ Test requests from frontend (admin-only tool)
- ❌ Upload files (no multipart form support yet)
- ❌ Test endpoints that require external auth (OAuth, etc.)

## Next Steps

- Read [SETUP.md](SETUP.md) for advanced configuration
- Check [WORDPRESS-ORG-STANDARDS.md](WORDPRESS-ORG-STANDARDS.md) for compliance details
- Review [BUILD-SUMMARY.md](BUILD-SUMMARY.md) for architecture

## Keyboard Shortcuts

(None currently, but ideas for future versions!)

## Support

- For bugs/feature requests: [WordPress.org plugin support forum](https://wordpress.org/support/plugin/route-scout/)
- For questions: Check the FAQ section in readme.txt

---

**That's it!** You're ready to browse and test your WordPress REST API. Happy coding! 🚀
