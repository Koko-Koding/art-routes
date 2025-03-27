Art Routes is a WordPress plugin that allows a WordPress dashboard user to configure routes with points of interest (artworks) on a map.

The plugin uses Leaflet.js version 1.9.4 (loaded from cdn.jsdelivr.net) in combination with OpenStreetMap to display the map and the routes.

The plugin is designed to be used by organizations that organize art routes / events, such as Woest & Bijster in the Netherlands, and want to display them on their website. It will probably be used by non-technical users who are more or less familiar with WordPress.

The map will likely be used on smaller devices, and by non-technical, sometimes older people as well, so the plugin should be responsive and mobile-friendly.

Please refer to `README.md` and `CHANGELOG.md` for more information about the specific features of the plugin and how to use them. Use semantic versioning for the plugin.

In the languages directory, you will find the translation files for the plugin. The plugin is currently available in English and Dutch. You can add more languages by creating a new `.po` file in the `languages` directory and adding the corresponding translations.

Whenever you make changes to the plugin, make sure to
1. Update the version number in the main plugin file (`wp-art-routes.php`)
2. Add the new version with description in the `CHANGELOG.md` file, and
3. Update the `README.md` file accordingly.
4. Update the translation files in the `languages` directory if necessary.