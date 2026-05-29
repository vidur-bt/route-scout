# WordPress.org Plugin Submission Checklist

Before uploading Route Scout to WordPress.org, complete this checklist.

## Pre-Submission (Complete Before Uploading)

### Code Quality
- [ ] All PHP files pass `phpcs --standard=WordPress`
  ```bash
  phpcs --standard=WordPress route-scout.php includes/
  ```
  Expected: 0 errors, 0 warnings

- [ ] No PHP notices/warnings when plugin is activated
- [ ] Tested on WordPress 6.0, 6.5, 6.6 (latest 3 versions)
- [ ] Tested on PHP 8.0, 8.1, 8.2, 8.3

### Security & Best Practices
- [ ] All REST endpoints require `manage_options` capability
- [ ] All user input validated/sanitized
- [ ] All output properly escaped
- [ ] No `eval()`, `create_function()`, or dynamic code execution
- [ ] No external HTTP requests
- [ ] No database table creation (uses WordPress core tables only)
- [ ] No `wp_mail()` or sending external data
- [ ] No obfuscated code

### Functionality
- [ ] Plugin activates without errors
- [ ] Plugin deactivates without leaving data behind
- [ ] All features work as expected
- [ ] Request collections save/load correctly
- [ ] Route discovery works for custom namespaces
- [ ] Response formatting handles all data types
- [ ] Error handling is graceful (no white screens)

### Documentation
- [ ] readme.txt is properly formatted
- [ ] Plugin name matches slug (`route-scout`)
- [ ] Description is clear and non-marketing
- [ ] Installation instructions are accurate
- [ ] All translatable strings use correct text domain
- [ ] No spelling/grammar errors in readme.txt

### Screenshots (Optional but Recommended)
- [ ] Prepare 1-2 screenshots showing main UI
- [ ] Save as `screenshot-1.png` (600x450px minimum, max 8MB)
- [ ] Add short captions in readme.txt

## WordPress.org Account Setup

- [ ] Create developer account at [profiles.wordpress.org](https://profiles.wordpress.org)
- [ ] Verify email address
- [ ] Create plugin page via [submission form](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
  - Plugin name: Route Scout
  - Plugin slug: route-scout
  - Description: Browse, test, and document your WordPress REST API endpoints without leaving wp-admin.

## SVN Setup (After Plugin Approved)

Once WordPress.org approves the plugin:

1. **Install SVN**
   ```bash
   # macOS
   brew install subversion
   
   # Ubuntu
   sudo apt install subversion
   
   # Windows: Download from https://tortoisesvn.net/
   ```

2. **Checkout the repository**
   ```bash
   svn checkout https://plugins.svn.wordpress.org/route-scout/trunk
   cd trunk
   ```

3. **Add plugin files**
   ```bash
   cp -r route-scout/* .
   svn add .
   ```

4. **Commit the trunk**
   ```bash
   svn commit -m "Initial commit of Route Scout v1.0.0"
   ```

5. **Create the tag**
   ```bash
   svn copy \
     https://plugins.svn.wordpress.org/route-scout/trunk \
     https://plugins.svn.wordpress.org/route-scout/tags/1.0.0 \
     -m "Tag version 1.0.0"
   ```

## After Submission

### During Review (1-2 weeks)
- [ ] Monitor WordPress.org forum for plugin feedback
- [ ] Respond promptly to reviewer questions
- [ ] Do not make changes to code until approval

### After Approval
- [ ] Plugin appears on WordPress.org plugin directory
- [ ] Available for installation via wp-admin Plugins → Add New
- [ ] Monitor plugin ratings and reviews
- [ ] Respond to user support questions in forums
- [ ] Plan updates/improvements based on feedback

### Future Versions
For each update:
1. Increment version number in `route-scout.php` and `readme.txt`
2. Commit to trunk
3. Tag new version: `svn copy trunk tags/X.Y.Z`
4. Update changelog in readme.txt

## Support Channels

After approval, users can:
- Post support questions in [plugin support forum](https://wordpress.org/support/plugin/route-scout/)
- Report bugs via forum or [GitHub](https://github.com/your-account/route-scout) (if you create one)
- Request features

## Common Rejection Reasons (We're Safe!)

Route Scout **does not** have any of these issues:

- ❌ Hardcoded links to external sites
- ❌ Calls home without consent
- ❌ Unrequested changes to user data
- ❌ Missing README
- ❌ Closed-source components
- ❌ Poor code quality
- ❌ Security vulnerabilities
- ❌ Unclear descriptions

✅ All clear for submission!

## Estimated Timeline

| Phase | Duration | Action |
|-------|----------|--------|
| **Code finalization** | 1 day | Fix any found issues |
| **SVN setup** | 1 day | Register account, create plugin entry |
| **Initial queue** | 1-3 days | Waiting for first review |
| **Review** | 5-14 days | Reviewer checks code & functionality |
| **Approval** | 1 day | Go live on WordPress.org |

**Total: 1-3 weeks from submission to live.**

---

**Ready to submit?** Go to [WordPress.org Plugin Submission](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/) and create your plugin entry!
