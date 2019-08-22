=== Biltorvet Dealer Tools ===
Contributors: biltorvetas
Donate link: http://biltorvet.as
Tags: cars, biltorvet, autodesktop, biler, biltorvet web
Requires at least: 4.9.7
Tested up to: 5.1.1
Stable tag: 5.1.1
Requires PHP: 7.2
License: GPLv2 or later

Biltorvet Dealer Tools makes it possible to show and search in the cars, that is created in the sales software AutoDesktop. AutoDesktop is also provided by Biltorvet A/S. 

Med Biltorvet Dealer Tools bliver det muligt at få vist sine biler fra AutoDesktop på sin hjemmeside. Dine besøgende får mulighed for at søge blandt jeres biler og se deltajer om dem.

== Description ==

Biltorvet Dealer Tools er fuld integreret med AutoDesktop, så din hjemmeside kan sende leads direkte til salgsprogrammet AutoDesktop.


== Installation ==

1. Install the plugin through wordpress plugin repository
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Biltorvet to configure the plugin
4. Enter the API-key provided by Biltorvet A/S and configure the settings.


== Screenshots ==

1. Car detailpage
2. Car search
3. Plugin settings

== Changelog ==

= 1.1.0 =
* Refactor major parts of the plugin
* Adds even more plugin settings
* Fixes bug with some price calculations

= 1.0.23 =
* Adds plugin settings for controlling prices shown
* Adds debug menu, when wp_debug are enabled

= 1.0.22 =
* Adds new shortcodes
* Refactor part of codebase
* Fixes missing data from leads parsed to AD

= 1.0.21 =
* Adds filters to url path.
* Fixes bug with propellants
* Adds default api key (demo) for new plugin installations

= 1.0.20 =
* Fixing issues with systemmails throwing an exception. Fixing CTA mail formatting.

= 1.0.19 =
* Fixing Santander widget for some clients, by introducing brandingid shortcode attribute.

= 1.0.18 =
* Adding new options that allow to filter out all AutoDesktop or Bilinfo vehicles, for cases where duplicates would appear (100% Autotjek's clients).

= 1.0.17 =
* Fixing a typo (Eksklusive Moms -> Eksklusiv Moms)

= 1.0.16 =
* (When sending a lead e-mail): Some e-mail clients don't respect the reply-to header, and then we lose the information about sender. For this reason, we are gluing the sender e-mail back to the e-mail body.

= 1.0.15 =
* Correcting the e-mail address passed to the ADT Lead, from the "to" e-mail (which was the email of the website owner), to the "reply-to" email (which is the email of the sender).

= 1.0.14 =
* Refactoring to add templates, which can be overwritten in your child theme. Just create a folder called biltorvet-dealer-tools in your child theme, and copy all files from the templates folder in this plugin.

= 1.0.13 =
* Showing "Exclusive VAT" for vehicles without VAT; not only for vehicles that are being financed or leased.
* Adding chars required to get Škoda and Citroën properly escaped in URLs.

= 1.0.12 =
* Stability fix.

= 1.0.11 =
* Removing Ekskl. Moms from all Motorcycles.

= 1.0.10 =
* Adding [bdt_prop p="company"] /for vehicle detail/, so you can show the address where the vehicle is located.

= 1.0.9 =
* Refactoring the vehicle detail slider to TypeScript, changing namespace, and loading externally. Added fullscreen support. The slider can now be controlled by swipe on mobile, and by keys on your keyboard (left/right arrow keys - navigate, up/down - fullscreen toggle, space = play/pause video)
* Improving compatibility of range sliders in the vehicle search - in case of conflict with another .slider(), fx. loaded in Avada, the vehicle search would stop working.
* Improved handling of the template pages, so now you can point the Vehicle Search to any page in any depth, fx. biler/brugte-biler.
* Adjusted translations to match both vehicles and motorcycles.

= 1.0.8 =
* Removing SVG files, creating a webfont instead.
* Updating the Byttepris widget embed code to support custom color and logo attributes.
* Fixing a bug with wrong urls to car detail in case the shortcode is placed outside of the expected page.

= 1.0.7 =
* Adding option to hide sold cars, 
* Removing sold cars from the recommended cars shortcode results
* Adding Featured cars shortcode.

= 1.0.6 =
* Adding support for ADT videos to a vehicle detail, 
* Adding support for different pricing models (cash, leasing, and financing)
* Adding widget shortcodes

= 1.0.5 =
* First public version.

== Upgrade Notice ==

Please use our ticketing system when submitting your logs.  Please do not post to the forums.