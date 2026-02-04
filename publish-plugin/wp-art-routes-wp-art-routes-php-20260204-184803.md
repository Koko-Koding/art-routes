# Plugin Check Report

**Plugin:** WP Art Routes
**Generated at:** 2026-02-04 18:48:03


## `includes/editions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 231 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 257 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 290 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 301 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 311 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 337 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 361 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 374 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_settings_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 374 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_settings_nonce&#039;] |  |
| 390 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_default_location_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 390 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_default_location_icon&#039;] |  |
| 413 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_terminology_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 413 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_terminology_nonce&#039;] |  |
| 429 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_terminology&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 429 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_terminology&#039;] |  |
| 445 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_dates_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 445 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_dates_nonce&#039;] |  |
| 461 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_start_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 474 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_end_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 625 | 32 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 626 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 679 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 680 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 690 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 691 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 701 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 702 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 755 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 756 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 819 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 820 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 834 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 835 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 849 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 850 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |

## `includes/ajax-handlers.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 18 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 18 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 71 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 71 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;nonce&#039;] |  |
| 76 | 56 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;term&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 77 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;post_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 173 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 180 | 74 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_length&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 187 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;updated_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 187 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;updated_points&#039;] |  |
| 214 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;removed_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 214 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;removed_points&#039;] |  |
| 227 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;new_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 227 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;new_points&#039;] |  |
| 235 | 63 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 235 | 66 | ERROR | WordPress.WP.I18n.UnorderedPlaceholdersText | Multiple placeholders in translatable strings should be ordered. Expected "%1$s, %2$s", but got "%s, %s" in 'New Artwork near %s, %s'. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#variables) |
| 235 | 133 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 235 | 136 | ERROR | WordPress.WP.I18n.UnorderedPlaceholdersText | Multiple placeholders in translatable strings should be ordered. Expected "%1$s, %2$s", but got "%s, %s" in 'New Info Point near %s, %s'. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#variables) |
| 385 | 41 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 385 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 385 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 387 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 393 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 400 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 415 | 10 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$gpx_content'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 438 | 16 | ERROR | WordPress.WP.AlternativeFunctions.strip_tags_strip_tags | strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead. |  |

## `includes/template-functions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 415 | 73 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_types[$route_data['type']]'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 415 | 109 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_data['type']'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 427 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 443 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 449 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 454 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 458 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 458 | 115 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 532 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 540 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 548 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 557 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 562 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 581 | 28 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 584 | 28 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 614 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 615 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 647 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 648 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 744 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 745 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |

## `templates/single-edition.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 63 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |

## `build/wp-art-routes/includes/editions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 231 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 257 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 290 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 301 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 311 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 337 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 361 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 374 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_settings_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 374 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_settings_nonce&#039;] |  |
| 390 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_default_location_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 390 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_default_location_icon&#039;] |  |
| 413 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_terminology_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 413 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_terminology_nonce&#039;] |  |
| 429 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_terminology&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 429 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_terminology&#039;] |  |
| 445 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_dates_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 445 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_dates_nonce&#039;] |  |
| 461 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_start_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 474 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_end_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 625 | 32 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 626 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 679 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 680 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 690 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 691 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 701 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 702 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 755 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 756 | 17 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 819 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 820 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 834 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 835 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 849 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 850 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |

## `build/wp-art-routes/includes/ajax-handlers.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 18 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 18 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 71 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 71 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;nonce&#039;] |  |
| 76 | 56 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;term&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 77 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;post_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 173 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 180 | 74 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_length&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 187 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;updated_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 187 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;updated_points&#039;] |  |
| 214 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;removed_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 214 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;removed_points&#039;] |  |
| 227 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;new_points&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 227 | 18 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;new_points&#039;] |  |
| 235 | 63 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 235 | 66 | ERROR | WordPress.WP.I18n.UnorderedPlaceholdersText | Multiple placeholders in translatable strings should be ordered. Expected "%1$s, %2$s", but got "%s, %s" in 'New Artwork near %s, %s'. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#variables) |
| 235 | 133 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 235 | 136 | ERROR | WordPress.WP.I18n.UnorderedPlaceholdersText | Multiple placeholders in translatable strings should be ordered. Expected "%1$s, %2$s", but got "%s, %s" in 'New Info Point near %s, %s'. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#variables) |
| 385 | 41 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 385 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 385 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 387 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 393 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 400 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 415 | 10 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$gpx_content'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 438 | 16 | ERROR | WordPress.WP.AlternativeFunctions.strip_tags_strip_tags | strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead. |  |

## `build/wp-art-routes/includes/template-functions.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 415 | 73 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_types[$route_data['type']]'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 415 | 109 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_data['type']'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 427 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 443 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 449 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 454 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 458 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 458 | 115 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 532 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 540 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 548 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 557 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 562 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 581 | 28 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 584 | 28 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 614 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 615 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 647 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 648 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 744 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 745 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |

## `build/wp-art-routes/templates/single-edition.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 63 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |

## `build/wp-art-routes-2.1.1.zip`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | compressed_files | Gecomprimeerde bestanden zijn niet toegestaan. |  |

## `@bin/route-info-rest-client/.env.example`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | hidden_files | Verborgen bestanden zijn niet toegestaan. |  |

## `bin/concat-plugin-files.sh`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | application_detected | Toepassingsbestanden zijn niet toegestaan. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-40.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP79.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Start.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-3.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Rolstoel.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP79.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1a.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-2.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-verrrekijker.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-41.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-bord Lunteren.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-13.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-9.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-12.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1d.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-10.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-11.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Horeca.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-5.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 78.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 78.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-11.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-39.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-15.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1c.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Rolstoel.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Informatie.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-4.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 80.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1b.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-14.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-10.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 75.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-9.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-8.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 80.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-8.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1a.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 76.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-5.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 60.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Fietsverhuur.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Pointer.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-4.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Start.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Parkeren.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1d.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-6.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-7.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-7.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 01.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-13.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-bord Ede.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-12.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-3.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 75.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 61.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Horeca.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-bord Ede.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-6.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 60.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-2.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Kinderwagen.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 76.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 77.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 61.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 77.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Informatie.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Fietsverhuur.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Trein.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-39.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Kinderwagen.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-bord Lunteren.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-15.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Parkeren.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Trein.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1b.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-1c.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-Pointer.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-verrrekijker.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-40.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-14.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-KP 01.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `build/wp-art-routes/assets/icons/legacy/WB plattegrond-41.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-40.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP79.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Start.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-3.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Rolstoel.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP79.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1a.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-2.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-verrrekijker.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-41.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-bord Lunteren.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-13.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-9.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-12.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1d.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-10.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-11.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Horeca.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-5.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 78.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 78.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-11.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-39.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-15.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1c.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Rolstoel.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Informatie.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-4.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 80.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1b.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-14.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-10.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 75.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-9.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-8.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 80.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-8.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1a.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 76.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-5.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 60.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Fietsverhuur.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Pointer.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-4.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Start.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Parkeren.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1d.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-6.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-7.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-7.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 01.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-13.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-bord Ede.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-12.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-3.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 75.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 61.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Horeca.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-bord Ede.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-6.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 60.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-2.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Kinderwagen.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 76.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 77.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 61.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 77.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Informatie.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Fietsverhuur.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Trein.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-39.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Kinderwagen.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-bord Lunteren.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-15.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Parkeren.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Trein.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1b.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-1c.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-Pointer.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-verrrekijker.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-40.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-14.webp`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-KP 01.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `assets/icons/legacy/WB plattegrond-41.svg`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | badly_named_files | Bestands- en mapnamen mogen geen spaties of speciale karakters bevatten. |  |

## `wp-art-routes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | plugin_header_invalid_network | De "Network" header in het plugin bestand is niet geldig. Kan alleen worden ingesteld op waar, en moet worden weggelaten als het niet nodig is. | [Docs](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/#header-fields) |
| 0 | 0 | WARNING | trademarked_term | De plugin naam bevat een beperkte term. Je gekozen plugin naam - "WP Kunstroutes" - Bevat de beperkte term "wp" die helemaal niet gebruikt kan worden in je plugin naam. |  |
| 0 | 0 | WARNING | trademarked_term | De plugin slug bevat een beperkte term. Je plugin slug - "wp-art-routes" - Bevat de beperkte term "wp" die helemaal niet gebruikt kan worden in je plugin slug. |  |
| 36 | 5 | ERROR | PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound | load_plugin_textdomain() has been discouraged since WordPress version 4.6. Wanneer je plugin gehost wordt op WordPress.org, hoef je deze functieaanroep voor vertalingen onder je plugin slug niet meer handmatig op te nemen. WordPress zal de vertalingen automatisch voor je laden wanneer dat nodig is. | [Docs](https://make.wordpress.org/core/2016/07/06/i18n-improvements-in-4-6/) |

## `includes/settings.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 143 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 143 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 153 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 208 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 242 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 248 | 50 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 254 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 276 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 330 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 341 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 356 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 372 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 383 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 406 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 408 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 416 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 423 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 427 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 434 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 438 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 452 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 40 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 555 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;upload_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 555 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;upload_icon_nonce&#039;] |  |
| 565 | 48 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;custom_icon_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 569 | 13 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;custom_icon_file&#039;] |  |
| 653 | 14 | ERROR | Generic.PHP.ForbiddenFunctions.Found | The use of function move_uploaded_file() is forbidden |  |
| 672 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 672 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 682 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;filename&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 699 | 14 | ERROR | WordPress.WP.AlternativeFunctions.unlink_unlink | unlink() is discouraged. Use wp_delete_file() to delete a file. |  |

## `includes/scripts.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 76 | 27 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 76 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 301 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 326 | 56 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 329 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 330 | 68 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 332 | 30 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 338 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 339 | 91 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 357 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 363 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 364 | 105 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 365 | 97 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 368 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 369 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 376 | 98 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 377 | 94 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `includes/meta-boxes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 148 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 155 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 158 | 127 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 159 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 165 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 168 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 180 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 189 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 192 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 209 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 210 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 216 | 75 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 238 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 249 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 258 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 263 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 266 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 271 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 278 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 284 | 78 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 303 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 310 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 316 | 78 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 344 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 347 | 46 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 351 | 30 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 353 | 44 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 366 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 379 | 74 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 387 | 42 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 441 | 48 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 473 | 81 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 513 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 518 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 528 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 536 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 564 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 592 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 597 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 607 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 615 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 643 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 675 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 679 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 688 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 696 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 720 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 731 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_details_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 731 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_details_nonce&#039;] |  |
| 747 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_length&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 752 | 75 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_duration&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 757 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 776 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 776 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_path_nonce&#039;] |  |
| 789 | 34 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 789 | 34 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_path&#039;] |  |
| 829 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[$nonce_name] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 829 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[$nonce_name] |  |
| 835 | 77 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_latitude&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 840 | 78 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_longitude&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 847 | 79 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_number&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 852 | 81 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_location&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 873 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_artists_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 873 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;artwork_artists_nonce&#039;] |  |
| 904 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 904 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;artwork_icon_nonce&#039;] |  |
| 916 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 931 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;info_point_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 931 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;info_point_icon_nonce&#039;] |  |
| 943 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;info_point_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 958 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 958 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_icon_nonce&#039;] |  |
| 974 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 993 | 37 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 1002 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 1012 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_selector_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1012 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_selector_nonce&#039;] |  |

## `includes/import-export.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 40 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 40 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 50 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 50 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 138 | 13 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 144 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 153 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 157 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 163 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 166 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 172 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 177 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 188 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 202 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 204 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 211 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 215 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 217 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 222 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 223 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 224 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 230 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 231 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 235 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 236 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 240 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 245 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 250 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 251 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 258 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 264 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 265 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 269 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 272 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 281 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 284 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 293 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 297 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 303 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 306 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 312 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 317 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 323 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 334 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 340 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 342 | 43 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 350 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 356 | 41 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 366 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 370 | 43 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 437 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 440 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 450 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 454 | 56 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 465 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 469 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 470 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 473 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 482 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 498 | 43 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 509 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce&#039;]. Check that the array index exists before using it. |  |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;_wpnonce&#039;] |  |
| 534 | 82 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;import_edition_id&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 540 | 89 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;csv_new_edition_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 571 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;] |  |
| 594 | 20 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fopen | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fopen(). |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;] |  |
| 602 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 615 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 800 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 851 | 9 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 878 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 879 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 914 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 915 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 939 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 940 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 966 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 967 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1002 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 1003 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce_gpx&#039;]. Check that the array index exists before using it. |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;_wpnonce_gpx&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;_wpnonce_gpx&#039;] |  |
| 1031 | 86 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;gpx_import_edition_id&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1037 | 89 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;gpx_new_edition_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1074 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 1091 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 1091 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;] |  |
| 1097 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 1097 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;] |  |
| 1427 | 9 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1444 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1444 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 1445 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1450 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1474 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1485 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1485 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 1486 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1491 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1500 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1505 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1600 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1624 | 20 | ERROR | WordPress.WP.AlternativeFunctions.strip_tags_strip_tags | strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead. |  |
| 1725 | 10 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$gpx'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `includes/post-types.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 446 | 28 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$artist_titles'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 537 | 30 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 537 | 64 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 16 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 44 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 573 | 30 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/edition-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 117 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 124 | 34 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 124 | 64 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 128 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 131 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 133 | 40 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 141 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 150 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 151 | 98 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 152 | 100 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 153 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 161 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 167 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 168 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 169 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 170 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 172 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 174 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 175 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 176 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 184 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 185 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 189 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 199 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 205 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 206 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 207 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 208 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 210 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 212 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 213 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 214 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 221 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 222 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 223 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 224 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 225 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 226 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 227 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 231 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 247 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 248 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 249 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 250 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 252 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 254 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 256 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 263 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 264 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 265 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 266 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 267 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 268 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 272 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 282 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 288 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 289 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 292 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 296 | 77 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 304 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 305 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 308 | 81 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 311 | 68 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 322 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 323 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 327 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 329 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 335 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 336 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 343 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 344 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 351 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 352 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 359 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 360 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 371 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 381 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 435 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 436 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 458 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 459 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 500 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 501 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 575 | 39 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;value&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 575 | 39 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;value&#039;] |  |
| 842 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;start_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 843 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;end_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 849 | 82 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;default_location_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 861 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;terminology&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 861 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;terminology&#039;] |  |

## `templates/shortcode-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 64 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_types[$route['type']]'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 95 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route['type']'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 104 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 110 | 62 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 120 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 126 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 131 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 135 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 135 | 111 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

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
| 35 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 41 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 68 | 165 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 74 | 165 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 90 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 91 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 121 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `templates/shortcode-multiple-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 20 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 104 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 156 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 166 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 170 | 86 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 175 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 486 | 48 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `build/wp-art-routes/includes/settings.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 143 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 143 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 153 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 208 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 242 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 248 | 50 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 254 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 276 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 330 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 341 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 356 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 372 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 383 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 406 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 408 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 416 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 423 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 427 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 434 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 438 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 452 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 40 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 555 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;upload_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 555 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;upload_icon_nonce&#039;] |  |
| 565 | 48 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;custom_icon_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 569 | 13 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;custom_icon_file&#039;] |  |
| 653 | 14 | ERROR | Generic.PHP.ForbiddenFunctions.Found | The use of function move_uploaded_file() is forbidden |  |
| 672 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 672 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 682 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;filename&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 699 | 14 | ERROR | WordPress.WP.AlternativeFunctions.unlink_unlink | unlink() is discouraged. Use wp_delete_file() to delete a file. |  |

## `build/wp-art-routes/includes/scripts.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 76 | 27 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 76 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 301 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 326 | 56 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 329 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 330 | 68 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 332 | 30 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 338 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 339 | 91 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 357 | 27 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 363 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 364 | 105 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 365 | 97 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 368 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 369 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 376 | 98 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 377 | 94 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `build/wp-art-routes/includes/meta-boxes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 148 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 155 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 158 | 127 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 159 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 165 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 168 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 180 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 189 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 192 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 209 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 210 | 18 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 216 | 75 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 238 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 249 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 258 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 263 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 266 | 41 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 271 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 278 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 284 | 78 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 303 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 310 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 316 | 78 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 344 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 347 | 46 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 351 | 30 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 353 | 44 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 366 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 379 | 74 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 387 | 42 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 441 | 48 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 473 | 81 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 513 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 518 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 528 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 536 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 564 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 592 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 597 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 607 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 615 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 643 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 675 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 679 | 36 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 688 | 34 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 696 | 38 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 720 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 731 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_details_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 731 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_details_nonce&#039;] |  |
| 747 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_length&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 752 | 75 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_duration&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 757 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 776 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 776 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_path_nonce&#039;] |  |
| 789 | 34 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_path&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 789 | 34 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_path&#039;] |  |
| 829 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[$nonce_name] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 829 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[$nonce_name] |  |
| 835 | 77 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_latitude&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 840 | 78 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_longitude&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 847 | 79 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_number&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 852 | 81 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_location&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 873 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_artists_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 873 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;artwork_artists_nonce&#039;] |  |
| 904 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 904 | 66 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;artwork_icon_nonce&#039;] |  |
| 916 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;artwork_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 931 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;info_point_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 931 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;info_point_icon_nonce&#039;] |  |
| 943 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;info_point_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 958 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_icon_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 958 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;route_icon_nonce&#039;] |  |
| 974 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;route_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 993 | 37 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 1002 | 15 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 1012 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;edition_selector_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1012 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;edition_selector_nonce&#039;] |  |

## `build/wp-art-routes/includes/import-export.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 40 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 40 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 15 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 45 | 60 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 50 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 50 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 138 | 13 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 144 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 153 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 157 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 163 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 166 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 172 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 177 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 188 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 202 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 204 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 211 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 215 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 217 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 222 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 223 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 224 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 230 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 231 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 235 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 236 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 240 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 245 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 246 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 250 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 251 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 258 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 264 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 265 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 269 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 272 | 25 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 281 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 284 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 293 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 297 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 303 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 306 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 312 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 317 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 323 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 334 | 60 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 340 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 342 | 43 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 350 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 356 | 41 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 366 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 370 | 43 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 437 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 440 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 450 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 454 | 56 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 465 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 469 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 470 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 473 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 482 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 498 | 43 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'admin_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 509 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce&#039;]. Check that the array index exists before using it. |  |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 524 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;_wpnonce&#039;] |  |
| 534 | 82 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;import_edition_id&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 540 | 89 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;csv_new_edition_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 571 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 588 | 36 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;name&#039;] |  |
| 594 | 20 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fopen | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fopen(). |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 594 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_csv_file&#039;][&#039;tmp_name&#039;] |  |
| 602 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 615 | 9 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 800 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 851 | 9 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 878 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 879 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 914 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 915 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 939 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 940 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 966 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 967 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1002 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 1003 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;_wpnonce_gpx&#039;]. Check that the array index exists before using it. |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;_wpnonce_gpx&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1021 | 26 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;_wpnonce_gpx&#039;] |  |
| 1031 | 86 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;gpx_import_edition_id&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1037 | 89 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;gpx_new_edition_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1074 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;error&#039;]. Check that the array index exists before using it. |  |
| 1091 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;]. Check that the array index exists before using it. |  |
| 1091 | 43 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;name&#039;] |  |
| 1097 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;]. Check that the array index exists before using it. |  |
| 1097 | 38 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;import_gpx_file&#039;][&#039;tmp_name&#039;] |  |
| 1427 | 9 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1444 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1444 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 1445 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1450 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1474 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1485 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 1485 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 1486 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1491 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1500 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1505 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 1600 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 1624 | 20 | ERROR | WordPress.WP.AlternativeFunctions.strip_tags_strip_tags | strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead. |  |
| 1725 | 10 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$gpx'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `build/wp-art-routes/includes/post-types.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 446 | 28 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$artist_titles'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 537 | 30 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 537 | 64 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 16 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 569 | 44 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 573 | 30 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `build/wp-art-routes/includes/edition-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 117 | 16 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 124 | 34 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 124 | 64 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 128 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 131 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 133 | 40 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 141 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 150 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 151 | 98 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 152 | 100 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 153 | 95 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 161 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 167 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 168 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 169 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 170 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 172 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 174 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 175 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 176 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 183 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 184 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 185 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 189 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 199 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 205 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 206 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 207 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 208 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 210 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 212 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 213 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 214 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 221 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 222 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 223 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 224 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 225 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 226 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 227 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 231 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 241 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 247 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 248 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 249 | 57 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 250 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 252 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 254 | 83 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 255 | 84 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 256 | 86 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 263 | 64 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 264 | 65 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 265 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 266 | 62 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 267 | 63 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 268 | 66 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 272 | 72 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 282 | 55 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 288 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 289 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 292 | 79 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 296 | 77 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 304 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 305 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 308 | 81 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 311 | 68 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 322 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 323 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 327 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 328 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 329 | 51 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 335 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 336 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 343 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 344 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 351 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 352 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 359 | 59 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 360 | 74 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 371 | 39 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 381 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 435 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 436 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 458 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 459 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 500 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |
| 501 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_value | Detected usage of meta_value, possible slow query. |  |
| 575 | 39 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;value&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 575 | 39 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;value&#039;] |  |
| 842 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;start_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 843 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;end_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 849 | 82 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;default_location_icon&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 861 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;terminology&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 861 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;terminology&#039;] |  |

## `build/wp-art-routes/templates/shortcode-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 64 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_types[$route['type']]'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 92 | 95 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route['type']'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 104 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 110 | 62 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 120 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 126 | 58 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 131 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 135 | 18 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 135 | 111 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `build/wp-art-routes/templates/shortcode-route-icons.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 26 | 100 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 28 | 105 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 30 | 78 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$route_title'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `build/wp-art-routes/templates/shortcode-edition-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 138 | 83 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `build/wp-art-routes/templates/single-artwork.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 41 | 47 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 68 | 165 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 74 | 165 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 90 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 91 | 31 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 121 | 31 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `build/wp-art-routes/templates/shortcode-multiple-map.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 20 | 46 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 104 | 23 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 156 | 35 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 166 | 52 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 170 | 86 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$container_style'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 175 | 22 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 486 | 48 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |

## `build/wp-art-routes/wp-art-routes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 36 | 5 | ERROR | PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound | load_plugin_textdomain() has been discouraged since WordPress version 4.6. Wanneer je plugin gehost wordt op WordPress.org, hoef je deze functieaanroep voor vertalingen onder je plugin slug niet meer handmatig op te nemen. WordPress zal de vertalingen automatisch voor je laden wanneer dat nodig is. | [Docs](https://make.wordpress.org/core/2016/07/06/i18n-improvements-in-4-6/) |

## `readme.txt`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | outdated_tested_upto_header | Getest tot: 6.6 < 6.9. De "Tested up to" waarde in je plugin is niet ingesteld op de huidige versie van WordPress. Dit betekent dat je plugin niet zal verschijnen in zoekopdrachten, aangezien we vereisen dat plugins compatibel zijn en gedocumenteerd als getest tot de meest recente versie van WordPress. | [Docs](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/#readme-header-information) |
| 0 | 0 | WARNING | readme_parser_warnings_too_many_tags | Eén of meer tags zijn genegeerd. Beperk je plugin tot 5 tags. |  |
| 0 | 0 | WARNING | trademarked_term | De plugin naam bevat een beperkte term. Je gekozen plugin naam - "WP Art Routes" - Bevat de beperkte term "wp" die helemaal niet gebruikt kan worden in je plugin naam. |  |

## `templates/art-route-map-template.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | missing_direct_file_access_protection | PHP file should prevent direct access. Add a check like: if ( ! defined( 'ABSPATH' ) ) exit; | [Docs](https://developer.wordpress.org/plugins/wordpress-org/common-issues/#direct-file-access) |
| 10 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 10 | 47 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `build/wp-art-routes/templates/art-route-map-template.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | missing_direct_file_access_protection | PHP file should prevent direct access. Add a check like: if ( ! defined( 'ABSPATH' ) ) exit; | [Docs](https://developer.wordpress.org/plugins/wordpress-org/common-issues/#direct-file-access) |
| 10 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 10 | 47 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `@bin/route-info-rest-client/.gitignore`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | hidden_files | Verborgen bestanden zijn niet toegestaan. |  |

## `.gitignore`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | hidden_files | Verborgen bestanden zijn niet toegestaan. |  |

## `.claude`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | ai_instruction_directory | AI instruction directory ".claude" detected. These directories should not be included in production plugins. |  |

## `.github`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | github_directory | GitHub workflow directory ".github" detected. This directory should not be included in production plugins. |  |

## `DISTRIBUTION.md`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | unexpected_markdown_file | Unexpected markdown file "DISTRIBUTION.md" detected in plugin root. Only specific markdown files are expected in production plugins. |  |

## `WORDPRESS-ORG-PREPARATION.md`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | unexpected_markdown_file | Unexpected markdown file "WORDPRESS-ORG-PREPARATION.md" detected in plugin root. Only specific markdown files are expected in production plugins. |  |

## `CLAUDE.md`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | unexpected_markdown_file | Unexpected markdown file "CLAUDE.md" detected in plugin root. Only specific markdown files are expected in production plugins. |  |

## `includes/shortcodes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 41 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_query | Detected usage of meta_query, possible slow query. |  |
| 102 | 9 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude | Using exclusionary parameters, like exclude, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |
| 183 | 15 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in | Using exclusionary parameters, like post__not_in, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |

## `build/wp-art-routes/includes/shortcodes.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 41 | 9 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_query | Detected usage of meta_query, possible slow query. |  |
| 102 | 9 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude | Using exclusionary parameters, like exclude, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |
| 183 | 15 | WARNING | WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in | Using exclusionary parameters, like post__not_in, in calls to get_posts() should be done with caution, see https://wpvip.com/documentation/performance-improvements-by-removing-usage-of-post__not_in/ for more information. |  |
