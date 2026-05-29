# Route Scout — Development Setup

## Installation for Testing

### Option 1: Local WordPress with WP-CLI

```bash
# Create a fresh WordPress install
wp core download --path=~/wordpress-test
cd ~/wordpress-test
wp config create --dbname=wordpress_test --dbuser=root --dbpass=password
wp db create
wp core install --url=http://localhost:8000 --title="Route Scout Test" --admin_user=admin --admin_password=password --admin_email=test@test.com

# Copy Route Scout into plugins directory
cp -r ../route-scout wp-content/plugins/

# Activate the plugin
wp plugin activate route-scout

# Start PHP server
php -S localhost:8000
```

Then visit `http://localhost:8000/wp-admin` and navigate to **Tools → Route Scout**.

### Option 2: Docker (Recommended)

```bash
# In the root directory with docker-compose.yml
docker-compose up -d

# Install WordPress
docker-compose exec wordpress wp core install \
  --url=http://localhost:8000 \
  --title="Route Scout Test" \
  --admin_user=admin \
  --admin_password=password \
  --admin_email=test@test.com

# Activate plugin
docker-compose exec wordpress wp plugin activate route-scout
```

Visit `http://localhost:8000/wp-admin`.

## Testing Checklist

1. **Endpoint Discovery**
   - [ ] Navigate to Tools → Route Scout
   - [ ] Verify route list populates (should show `/wp/v2/posts`, `/wp/v2/users`, etc.)
   - [ ] Search for "posts" — should filter routes
   - [ ] Verify namespaces group correctly (wp/v2, wp/v3, custom namespaces, etc.)

2. **Request Tester**
   - [ ] Click a GET route (e.g., `/wp/v2/posts`)
   - [ ] Click "Send Request"
   - [ ] Verify response appears (status 200, formatted JSON)
   - [ ] Try a POST route (e.g., `/wp/v2/posts`) with title param
   - [ ] Verify draft post is created

3. **Collections**
   - [ ] Save current request with name "Test Posts"
   - [ ] Refresh the page
   - [ ] Verify saved request still appears
   - [ ] Click "Load" and verify fields repopulate
   - [ ] Delete request

4. **Edge Cases**
   - [ ] Try invalid path — should error gracefully
   - [ ] Try request with invalid params — should return 400 or filtered response
   - [ ] Test DELETE route on a post (create one first, get its ID)
   - [ ] Test with custom REST endpoint (if you have a plugin with one)

5. **Styles & Responsive**
   - [ ] Check layout on full desktop (three columns)
   - [ ] Resize browser to tablet width — columns should stack
   - [ ] Verify no layout breaks or overlapping elements

## Code Quality Checks

### PHP Standards (WordPress.org)

```bash
# Install phpcs with WordPress standards
composer global require "squizlabs/php_codesniffer" "phpcompatibility/php-compatibility"
wp standards-ci install

# Run check
phpcs --standard=WordPress route-scout.php includes/ --colors
```

Expected output: **0 errors, 0 warnings**.

### Manual Checks

- [ ] No `var_dump()`, `print_r()`, or debug output
- [ ] All translatable strings use `__()`, `esc_html__()`, etc.
- [ ] All escaping correct: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- [ ] Nonces verified with `wp_verify_nonce()`
- [ ] Capability checks: `current_user_can( 'manage_options' )`
- [ ] No external HTTP requests
- [ ] No inline JavaScript (all in `route-scout.js`)
- [ ] No hardcoded URLs — use `ROUTE_SCOUT_PLUGIN_URL`

## Ready for WordPress.org Submission

Once all tests pass:

1. Run `phpcs` — should be 0 errors
2. Test on WordPress 6.0+ and 6.6
3. Test on PHP 8.0+
4. Create SVN directory structure:
   ```
   /tags/1.0.0/
   /branches/
   /trunk/
   ```
5. Submit via [WordPress.org plugin submission form](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
