
=== Custom Taxonomy Order ===
 Contributors: msicknick
 Tags: taxonomy, custom, order, terms, reorder, ordering, 
 Donate link: https://paypal.me/MSicknick
 Requires at least: 4.0
 Tested up to: 4.9.8
 Stable: trunk
 Requires PHP: 5.6
 License: GPLv2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html

 Allows for a custom order of taxonomies.

== Description ==
 The plugin allows users to turn on taxonomies they would like to order and drag and drop to a desired position.

== Installation ==
  Upload CCustom Taxonomy Order to your WordPress blog and activate it.
  Click on Settings (or go to Tools -> Taxonomy Order) and enable taxonomies for ordering.
  Go to each taxonomy and drag and drop them for a desired order.
  
  Note: When using a custom WP_Query, add 'meta_key' => 'position', 'orderby' => 'position' to display the terms in the set order.

== Changelog ==
 = Version 1.0.0 (10/24/2018) =
 * Initial release