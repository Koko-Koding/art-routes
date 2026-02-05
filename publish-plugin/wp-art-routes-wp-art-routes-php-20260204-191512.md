# Plugin Check Report

**Plugin:** WP Art Routes
**Generated at:** 2026-02-04 19:15:12


## `wp-art-routes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | plugin_header_invalid_network | The "Network" header in the plugin file is not valid. Can only be set to true, and should be left out when not needed. | [Docs](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/#header-fields) |
| 0 | 0 | WARNING | trademarked_term | The plugin name includes a restricted term. Your chosen plugin name - "WP Art Routes" - contains the restricted term "wp" which cannot be used at all in your plugin name. |  |
| 0 | 0 | WARNING | trademarked_term | The plugin slug includes a restricted term. Your plugin slug - "wp-art-routes" - contains the restricted term "wp" which cannot be used at all in your plugin slug. |  |
| 36 | 5 | ERROR | PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound | load_plugin_textdomain() has been discouraged since WordPress version 4.6. When your plugin is hosted on WordPress.org, you no longer need to manually include this function call for translations under your plugin slug. WordPress will automatically load the translations for you as needed. | [Docs](https://make.wordpress.org/core/2016/07/06/i18n-improvements-in-4-6/) |

## `includes/settings.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 143 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 143 | 66 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 153 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 524 | 40 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 565 | 48 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;custom_icon_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 569 | 13 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;custom_icon_file&#039;] |  |
| 664 | 14 | ERROR | Generic.PHP.ForbiddenFunctions.Found | The use of function move_uploaded_file() is forbidden |  |

## `includes/meta-boxes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 209 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 210 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 216 | 75 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 379 | 74 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 441 | 48 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 789 | 32 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_path&#039;] |  |

## `includes/import-export.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 40 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 40 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 50 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 50 | 66 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 138 | 13 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 258 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 272 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 350 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 356 | 41 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 366 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 498 | 43 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 524 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce&#039;]. Check that the array index exists before using it. |  |
| 571 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;] |  |
| 594 | 20 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fopen | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fopen(). |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;] |  |
| 602 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 615 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 800 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 876 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 877 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 912 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 913 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 937 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 938 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 964 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 965 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1000 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 1001 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1019 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce_gpx&#039;]. Check that the array index exists before using it. |  |
| 1072 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 1089 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 1089 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;] |  |
| 1100 | 49 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 1100 | 49 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;] |  |
| 1452 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1476 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1493 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1502 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1507 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1602 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1727 | 10 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$gpx'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `includes/post-types.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 446 | 28 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$artist_titles'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 537 | 30 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 537 | 75 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 16 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;edition_filter&#039;] |  |
| 573 | 41 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/edition-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 117 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 124 | 34 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 124 | 75 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 435 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 436 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 458 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 459 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 500 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 501 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 575 | 50 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;value&#039;] |  |

## `templates/shortcode-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 64 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_types[$route['type']]'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 95 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route['type']'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 110 | 62 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `templates/shortcode-route-icons.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 26 | 100 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 28 | 105 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 30 | 78 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `templates/shortcode-edition-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 138 | 83 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `templates/single-artwork.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 90 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 91 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `templates/shortcode-multiple-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 20 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 170 | 86 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `readme.txt`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | outdated_tested_upto_header | Tested up to: 6.6 < 6.9. The "Tested up to" value in your plugin is not set to the current version of WordPress. This means your plugin will not show up in searches, as we require plugins to be compatible and documented as tested up to the most recent version of WordPress. | [Docs](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/#readme-header-information) |
| 0 | 0 | WARNING | readme_parser_warnings_too_many_tags | One or more tags were ignored. Please limit your plugin to 5 tags. |  |
| 0 | 0 | WARNING | trademarked_term | The plugin name includes a restricted term. Your chosen plugin name - "WP Art Routes" - contains the restricted term "wp" which cannot be used at all in your plugin name. |  |

## `templates/art-route-map-template.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | missing_direct_file_access_protection | PHP file should prevent direct access. Add a check like: if ( ! defined( 'ABSPATH' ) ) exit; | [Docs](https://developer.wordpress.org/plugins/wordpress-org/common-issues/#direct-file-access) |
| 10 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 10 | 47 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/editions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 430 | 78 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_terminology&#039;] |  |
| 683 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 684 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 694 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 695 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 705 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 706 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 760 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 761 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 825 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 826 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 840 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 841 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 855 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 856 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |

## `includes/scripts.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 76 | 27 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 76 | 66 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/ajax-handlers.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 188 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;updated_points&#039;] |  |
| 217 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;removed_points&#039;] |  |
| 232 | 34 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;new_points&#039;] |  |

## `includes/shortcodes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 41 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_query | Detected usage of meta_query, possible slow query. |  |
| 102 | 9 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude | Using exclusionary parameters, like exclude, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |
| 183 | 15 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in | Using exclusionary parameters, like post__not_in, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |

## `includes/template-functions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 616 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 617 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 649 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 650 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 746 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 747 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
