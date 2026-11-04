=== WpPopPop ===
Contributors: cygnusorbit
Donate link: https://github.com/cygnusorbit/wppoppop
Tags: popup, modal, popup builder, optin, lead generation
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 3.0.836
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A flexible, modular popup and modal builder for WordPress.

== Description ==

WpPopPop is a decomposed, modular popup builder designed to create responsive, high-converting modals and overlays without slowing down your site.

= Key Features =
* Visual builder with real-time responsive canvas preview.
* Precise layout scaling across mobile and desktop displays.
* Customizable trigger options (delay, user interaction).
* Fully modular settings and schema architecture.
* 100% GPL-compliant and privacy focused.

== Installation ==

1. Upload the `wppoppop` folder to the `/wp-content/plugins/` directory, or install directly via the WordPress Plugins menu.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **WpPopPop** in your admin sidebar to create your first popup.

== Frequently Asked Questions ==

= Does WpPopPop track visitors? =
No. WpPopPop runs locally on your WordPress installation and does not track personal data without configuration.

= Can I use custom CSS? =
Yes, custom CSS and styling rules can be applied directly to individual layers and popup containers.

= How do I translate WpPopPop? =

The plugin is fully internationalized (text domain `wppoppop`, domain path `/languages`). Use `languages/wppoppop.pot` as the template, save your `.po`/`.mo` as `languages/wppoppop-{locale}.po/.mo`, then run `wp i18n make-json languages --no-purge` to create the JSON files used by the JavaScript strings.

== Changelog ==

= 3.0.836 =
* Initial WordPress.org release with modular canvas architecture.
