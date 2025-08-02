Art Routes is a WordPress plugin that allows a WordPress dashboard user to configure routes with points of interest (artworks) on a map.

The plugin uses Leaflet.js version 1.9.4 (loaded from cdn.jsdelivr.net) in combination with OpenStreetMap to display the map and the routes.

The plugin is designed to be used by organizations that organize art routes / events, such as Woest & Bijster in the Netherlands, and want to display them on their website. It will probably be used by non-technical users who are more or less familiar with WordPress.

The map will likely be used on smaller devices, and by non-technical, sometimes older people as well, so the plugin should be responsive and mobile-friendly.

Please refer to `README.md` and `CHANGELOG.md` for more information about the specific features of the plugin and how to use them. Use semantic versioning for the plugin.

In the languages directory, you will find the translation files for the plugin. The plugin is currently available in English and Dutch. You can add more languages by creating a new `.po` file in the `languages` directory and adding the corresponding translations. To compile the `.po` file into a, refer to the `bin/translate` script.

Whenever you make changes to the plugin, make sure to:

1. Update the version number in the main plugin file (`wp-art-routes.php`)
2. Add the new version with description in the `CHANGELOG.md` file, and

3. If possible, check if there are any non-documented commits since the last release / tag and add them to the changelog as well.

4. Update the `README.md` file accordingly.
5. Update the translation files in the `languages` directory if necessary.
6. You can also propose to commit the changes to the `main` branch of the repository with a descriptive and "conventional" commit message and subsequently tag the commit with the new version number in the format of `vX.Y.Z`.
7. Execute the `bin/build-release` script to build the release files and create a zip file of the plugin.

## GitHub CLI & Repository

You can use the GitHub CLI to manage your repository and perform various tasks directly from the command line.

For example, you can manage issues for the current repository by using the following command:

```bash
gh issue list
```

You can also create a new issue with the following command:

```bash
gh issue create --title "Issue Title" --body "Description of the issue"
```

## Code Style & Conventions

- Please don't use unsafe printing functions like `_e()` or `print_r()` in the plugin code. Instead, use `esc_html()`, `esc_attr()`, or other appropriate escaping functions to ensure that the output is safe and secure.

### Code Quality Tools

The plugin includes Docker-based code quality tools that enforce WordPress coding standards and automatically fix common issues:

```bash
bin/dev-tools check     # See all remaining issues
bin/dev-tools fix       # Auto-fix what's possible  
bin/dev-tools security  # Security-focused scan only
bin/dev-tools compile   # Build translation files
```

These tools use PHP_CodeSniffer (PHPCS) with WordPress coding standards to:
- Automatically fix unsafe printing functions like `_e()` → `esc_html_e()`
- Enforce proper output escaping and input sanitization
- Check nonce verification and security best practices
- Validate internationalization compliance
- Fix code formatting and style issues

Run `bin/dev-tools fix` before committing changes to automatically resolve most coding standard violations. Use `bin/dev-tools security` to focus specifically on security-related issues.