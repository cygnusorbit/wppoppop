=== WpPopPop ===
Contributors: cygnusorbit
Donate link: https://github.com/cygnusorbit/wppoppop
Tags: popup, modal, popup builder, optin, lead generation
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.0.924
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

= Does WpPopPop work on multisite? =
Yes. Network activate it and every site gets its own tables and settings; new sites are set up automatically and deleting a site removes its WpPopPop tables. Super admins can set defaults for new sites under Network Admin > WpPopPop.

== External services ==

WpPopPop only contacts a third-party service when the site owner turns the matching feature on. No visitor data is sent otherwise.

* **Mailchimp** - used only when the Mailchimp module is enabled. Sends the submitted form fields to the Mailchimp API. Terms: https://mailchimp.com/legal/terms/ - Privacy: https://mailchimp.com/legal/privacy/
* **GetResponse** - used only when the GetResponse module is enabled. Sends the submitted form fields to https://api.getresponse.com. Terms: https://www.getresponse.com/legal/terms-of-service - Privacy: https://www.getresponse.com/legal/privacy
* **Twilio** - used only when SMS alerts are enabled. Sends the alert text and the configured phone numbers to https://api.twilio.com. Terms: https://www.twilio.com/legal/tos - Privacy: https://www.twilio.com/legal/privacy
* **YouTube / Vimeo** - used only when a video element is placed in a popup. The visitor browser loads the embed from youtube.com or player.vimeo.com. YouTube: https://policies.google.com/privacy - Vimeo: https://vimeo.com/privacy
* **Webhooks** - used only when the site owner configures a webhook URL. Sends the submission payload to that URL.

== Frequently Asked Questions ==

= Does WpPopPop track visitors? =
No. WpPopPop runs locally on your WordPress installation and does not track personal data without configuration.

= Can I use custom CSS? =
Yes, custom CSS and styling rules can be applied directly to individual layers and popup containers.

= How do I translate WpPopPop? =

The plugin is fully internationalized (text domain `wppoppop`, domain path `/languages`). Use `languages/wppoppop.pot` as the template, save your `.po`/`.mo` as `wp-content/languages/plugins/wppoppop-{locale}.po/.mo`, then run `wp i18n make-json wp-content/languages/plugins --no-purge` to create the JSON files used by the JavaScript strings.

== External services ==

WpPopPop does not contact any third-party service on its own. All fonts (Inter, Roboto, Montserrat, Poppins, Open Sans, Lato) and the Font Awesome icons are bundled with the plugin, so no request is sent to a CDN and no visitor data leaves your site for them.

The services below are used only when you switch the related feature on:

= YouTube and Vimeo embeds =
When a popup contains a Video element, the visitor's browser loads the embedded player from `https://www.youtube.com` or `https://player.vimeo.com`, which receives the visitor's IP address and browser data. YouTube: [Terms](https://www.youtube.com/t/terms), [Privacy](https://policies.google.com/privacy). Vimeo: [Terms](https://vimeo.com/terms), [Privacy](https://vimeo.com/privacy).

= Webhook test =
The "test webhook" tool in the admin sends one sample JSON ping to the URL you type in. Nothing is sent until an administrator presses the button.

== Third-party libraries ==

* Font Awesome Free 6.5.1 - icons CC BY 4.0, fonts SIL OFL 1.1, code MIT - https://fontawesome.com/license/free (bundled in `assets/vendor/fontawesome/`).
* Inter, Roboto, Montserrat, Poppins, Open Sans and Lato - SIL Open Font License 1.1, packaged from Fontsource (bundled in `assets/vendor/fonts/`, licence file in each folder).

== Changelog ==

= 3.0.924 =
* Directory-submission compliance pass: readme, headers and external services disclosure.
* Multisite: network activation, automatic setup of new sites, clean-up when a site is deleted or the plugin is uninstalled.
* Network admin page with defaults for new sites.

= 3.0.912 =
* Directory-submission compliance pass: readme, headers and external services disclosure.

= 3.0.914 =
* Fonts and Font Awesome are now bundled with the plugin; no CDN requests.
* Webhook test tool no longer reaches private network addresses.
* Documented every external service in the readme.

= 3.0.836 =
* Initial WordPress.org release with modular canvas architecture.
