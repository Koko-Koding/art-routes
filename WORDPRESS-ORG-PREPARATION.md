# WordPress.org Publication Checklist

This document outlines the steps needed to prepare WP Art Routes for WordPress.org publication.

## ✅ Completed Items

- [x] Updated plugin header with proper URIs and metadata
- [x] Created required `readme.txt` file with WordPress.org format
- [x] Added uninstall hook for proper cleanup
- [x] Updated PHP requirements to be consistent (7.4+)
- [x] Security audit passed (proper nonces, sanitization, escaping)
- [x] Translation-ready with Dutch translations included

## 📋 Required Before Submission

### 1. Assets for WordPress.org

Create these files in `.wordpress-org/assets/` directory:

- `banner-1544x500.png` - High-res banner for plugin directory
- `banner-772x250.png` - Standard banner for plugin directory  
- `icon-128x128.png` - Plugin icon (128x128)
- `icon-256x256.png` - Plugin icon (256x256)
- `screenshot-1.png` - Route editor interface
- `screenshot-2.png` - Frontend map display
- `screenshot-3.png` - Artwork management interface
- `screenshot-4.png` - Multiple routes map
- `screenshot-5.png` - Mobile responsive interface

### 2. Final Code Review

- [ ] Test on fresh WordPress installation
- [ ] Test with various PHP versions (7.4, 8.0, 8.1, 8.2, 8.3)
- [ ] Test with latest WordPress version (6.6)
- [ ] Verify all features work without JavaScript errors
- [ ] Test on mobile devices

### 3. Documentation

- [ ] Ensure all features are documented in readme.txt
- [ ] Add FAQ entries for common questions
- [ ] Verify shortcode examples work correctly

### 4. Distribution Package

Files to INCLUDE in WordPress.org submission:

```
wp-art-routes.php
readme.txt
assets/ (CSS, JS, icons, images)
includes/ (PHP files)
languages/ (translation files)
templates/ (template files)
```

Files to EXCLUDE from WordPress.org submission:

```
@route-info-rest-client/
bin/
Dockerfile
.git/
.gitignore
node_modules/
*.log
.DS_Store
DISTRIBUTION.md
README.md (optional - readme.txt is the main one)
CHANGELOG.md (optional - changelog in readme.txt is sufficient)
```

### 5. Pre-submission Testing

- [ ] Install from zip file on clean WordPress site
- [ ] Test activation/deactivation
- [ ] Test uninstall (data cleanup)
- [ ] Verify no PHP errors or warnings
- [ ] Test with common themes (Twenty Twenty-Four, etc.)
- [ ] Test with common plugins for conflicts

## 🚀 Submission Process

1. **Package the plugin** (exclude development files)
2. **Submit to WordPress.org** via the plugin submission form
3. **Wait for review** (typically 7-14 days)
4. **Address reviewer feedback** if any
5. **Plugin approval and publication**

## 📊 WordPress.org Guidelines Compliance

✅ **Security**: Proper nonces, sanitization, and capability checks
✅ **Coding Standards**: Follows WordPress coding conventions  
✅ **Internationalization**: Translation-ready with text domain
✅ **Licensing**: GPL v2 compatible
✅ **No Premium Features**: Plugin is fully functional without paid upgrades
✅ **External Services**: Only uses OpenStreetMap (open/free service)
✅ **Performance**: Efficient code with proper asset loading

## 🔧 Post-Publication Maintenance

- Keep plugin updated with WordPress releases
- Monitor and respond to support forum questions
- Regular security updates if needed
- Feature updates based on user feedback

## 📞 Support Preparation

Be prepared to provide support via:

- WordPress.org support forums
- GitHub issues (if linking to repository)
- Documentation updates based on common questions
