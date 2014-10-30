=== Marketing Optimizer for Wordpress ===
Contributors: activeinternetmarketing
Donate link:
Tags: a b test, a b testing, a/b test, a/b testing, ab test, abtesting, analytics, click tracking, content experiments, conversion pages, conversion optimization, conversion rate optimization, cpa, goal tracking, marketing optimizer, multivariate, multivariate test, landing page, landing pages, split testing, active internet marketing, cro, call tracking, statistics, stats, conversions, analytics, testing, experiments, metrics, gravity forms
Requires at least: 3.3
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily the most complete A/B testing and conversion rate optimization software for WordPress.

== Description ==
= *New* Completely Updated for 2014 =
We listened to your feedback and improved the interface and functionality to make testing your content easier than ever. Here's a patial list of the improvements we added with this update:

* Automatic Tracking of All WordPress Pages
* Test WordPress Pages from your Page List
* Create WordPress Page Variations from the Page Detail Interface
* All New Landing Page Testing Suite
* All New Free Landing Page Templates
* Manage Variations Easily in One Place
* Faster Page Loads
* Better Cache Compatibility

= Gravity Forms Integration =
Use the awesome Gravity Forms form builder on your WordPress site, and easily post all your form data directly into your Marketing Optimizer web application.
= Conversion Rate Optimization Plugin =
A/B testing is the process of showing visitors one of two (or more) versions of the same web page, and then tracking which one created the most revenue, leads, signups, downloads, purchases, registrations, or comments. Every page on your Wordpress site contributes to conversion rates, not just the landing page or pages with a feedback form. To really bring your marketing 'A-Game', you need to test every important page to see how it contributes to your bottom line.
= A/B Test Multiple Pages =
Landing pages are the obvious choice for A/B testing. You are already sending traffic to them, and if converting visitors on the landing page is your goal, testing different version is a must. Every page on your website that receives traffic contributes to conversions, and it pays to test those, too. Your report will include the results for each page variant and the conversion rate of visitors that saw that version.
= Track Conversions Accurately =
Chances are you have at least one page that acts as your 'confirmation' or 'thank you' page for visitors that have just converted. You can now track all of these conversions with a simple shortcode that lets the system know that the visitor has just completed a successful conversion, and the proper version of all the web pages that visitor saw is credited with a conversion. This allows you to have many different conversion goal pages and still track all your conversions in properly.

= Adjustable Multi-Armed Bandit =
The 'Multi-Armed Bandit', or Epsilon Greedy, method of rotating variations has been proven to be the very fastest way to determine a winner. Not only that, but it's also, by far, the most profitable way to display your test pages. Here's how it works: the page with the highest conversion rate gets 90% of the traffic ('Exploitation'), and the other variations are randomly rotated through the remaining 10% of the time ('Exploration'). In other words, 90% of the time the system chooses the best version of your page. The rest of the time it explores new variations in the hopes that they will prove to be a better solution.
Since sometimes it makes more sense to explore more than 10%, you have the ability to simply move the slider to explore between  10% and 100%.
= Create Variations Quickly =
When editing any of your WordPress pages or a Landing Page, just click the 'Add a Variation' tab and the system automatically creates a new variation of the page you are working on. Test any content, with any template, easily and quickly.
Some common changes that have been shown to increase conversion rates are:

* Changes to headlines
* Changes to site layout
* Changes to layout
* Change images
* Adding testimonials
* Button types and styles
* Adding trust symbols
* Changes to feedback forms
* Call to action placement

= Marketing Optimizer Integration =
This plugin is a must for current subscribers to the Marketing Optimizer software. It includes automatic Marketing Optimizer javascript publishing code, phone tracking integration, and more.

= 100% Cache Compatible =
Tested to be compatible with W3 Total Cache, WP Super Cache, Quick Cache, and probably all WordPress caching plugins.
Tested to be compatible with WordPress hosting services, like WP Engine and Pagely, which cache your content on the server level. This plugin doesn't just 'bust' your caching, but utilizes your caching solution to retain the performance enhancements from them.

**Related Links:**

[Marketing Optimizer for Wordpress Plugin Homepage](http://www.marketingoptimizer.com/wordpress/)

[Marketing Optimizer Homepage](http://www.marketingoptimizer.com)




== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory, or download and install automatically through your admin panel.
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= Which pages should I create variations of first =

You should create variations of your pages in the following order:

1. Landing pages
2. Conversion pages (Contact Us)
3. Highly trafficked pages

= Which pages should I test first =

You should test as many pages as possible as soon as possible, and all at once. The system will accurately track any experiments you want to run, all concurrently. This will allow you to start accumulating data more quickly, which will show you the winner more quickly.

== Screenshots ==

1. General Settings
2. Marketing Optimizer Integration
3. Gravity Forms Integration Settings
4. Pages A/B Testing
5. Landing Pages A/B Testing
6. Landing Pages Templates

== Changelog ==
= Version 20141030 =
* Added shortcode usage and description to settings.
= Version 20141028 =
* #bugFix fixed an issue with marketing optimizer integration code wasn't being output on every post type.
= Version 20140708 =
* #bugFix fixed an issue with gravity form field mappings not saving.
= Version 20140617 =
* #bugFix fixed an issue with javascript tracking code method for pages causing error on other types of posts.
= Version 20140613 =
* #bugFix fixed an issue with marketing optimizer tracking javascript only working on pages.
= Version 20140611 =
* #bugFix fixed issue with form shortcodes.
= Version 20140604 =
* #bugFix fixed an issue with squeeze page settings not saving.
= Version 20140603 =
* #bugFix fixed issue with squeeze page templates not loading on servers restricting allow url fopen and file_get_contents.
* #bugFix fixed issue with javascript templates other than  squeeze pages.
= Version 20140530 =
* #bugFix fixed an issue with marketing optimizer tracking code not tracking squeeze pages correctly.
* Squeeze page templates now automatically fill out the modal height and width.
= Version 20140529 =
* #bugFix fixed an issue with squeeze page save and preview feature always creating an new modal on click.
* #bugFix fixed an issue with save and preview feature always opening a new window, now opens a new tab and refreshes the preview tab when you save and preview.
* Added 2 new squeeze page templates.
= Version 20140527 =
* Code refactoring.
= Version 20140523 =
* #bugFix fixed an issue with previewing pages, landing pages, and squeeze pages not showing correct preview.
* #bugFix fixed an issue with squeeze page close button was sometimes behind the modal.
= Version 20140517 =
* #bugFix fixed an issue with landing page/squeeze page templates not loading on servers with allow_url_fopen = 0
= Version 20140515 =
* #bugFix fixed an issue when selecting a squeeze page template when the editor was in text view was causing an error.
= Version 20140514 =
* Changed squeeze page modal to use jquery ui dialog to fix issue with chrome browser not showing squeeze page content.
* Removed squeeze page cache compatible option as it is not needed
* added all variations stats to metaboxes
* Changed edit squeeze page settings modal length to height
= Version 20140509 =
* #bugFix fixed an issue with stats not being tracked in some instances.
* #bugFix fixed an issue with squeeze pages not displaying.
= Version 20140508 =
* #bugFix fixed an issue with meta titles not being displayed in some cases.
= Version 20140501 =
* #hotFix fixed an issue with landing pages when defualt theme template was chosen not working with some templates.
= Version 20140430 =
* #bugFix fixed an issue with the admin toolbar being removed.
* #bugFix fixed an issue with when selecting a template the contents not being added to the wordpress editor when in visual view.
= Version 20140424 =
* #bugFix fixed and issue with cache compatability showing ajax content.
* Added newsletter squeeze page template;
= Version 20140423 =
* #featureAdded Squeeze pages, added the ability to create squeeze pages and a/b test them.
* #bugFix fixed an issue with broken images for templates and icons.
* #optimization general code optimization.
= Version 20140325 =
* A complete rewrite of the plugin, adding support for dedicated landing pages, new landing page templates, and a completely new user interface.
= Version 20131129 =
* #bugFix fixed an issue when you have no experiments or variations was causing an error.
* #featureAdded added ability to turn on/off tracking of admin users.
= Version 20131126 =
* #bugFix fixed an issue a/b testing slider not showing up due to wordpress update.
= Version 20131107 =
* #bugFix fixed an issue with multisite configurations not cascading to all network sites.
* #bugFix fixed an issue with admin user being tracked by experiments.
* #bugFix fixed an issue with search engines not being able to index experiments.
= Version 20131107 =
* #bugFix fixed an issue with multisite compatability.
= Version 20131106 =
* #bugFix fixed an issue with promoting variations not retaining some meta data.
* #bugFix fixed an issue previewing variations.
= Version 20131030 =
* #bugFix fixed an issue with cache compatibility and search engine indexing.
* #bugFix fixed an issue with experiments confidence scoring.
* #bugFix fixed an issue with permalinks for experiments/variations.
= Version 20130924 =
* #bugFix fixed an issue with get parameters and cache compatibility.
= Version 20130920 =
* #bugFix fixed A/B testing compatability issue with PHP version < 5.3.
* #featureRequest added ability to submit multi page gravity forms after each page.
= Version 20130918 =
* #bugFix fixed an issue with A/B testing cache compatibility and Internet Explorer.
* #bugFix removed html field type form gravity forms field mapping.
* #featureRequest added ability to duplicate control page.
= Version 20130916 =
* #bugFix fixed an issue with the gravity forms integration where non form fields were showing up in the field mapping.
= Version 20130913 =
* Added Marketing Optimizer integration with Gravity Forms
= Version 20130911 =
* Added cache compatible A/B testing
= Version 2013.08.27 =
* #bugFix fixed an issue with all testing stats were not being reset when resetting all stats.
* Added total columns to experiments page.
* #bugFix fixed an issue with post meta data not being correct when displaying a variation.
= Version 2013.08.22 =
* #bugFix fixed an issue with variation sometimes being tracked incorrectly.
* Removed some unnecessary text from the settings ui.
= Version 2013.08.21 =
* #bugFix fixed an error division by zero when viewing experiments with no stats.
* removed use page title from variation pages.
* #cleanup removed deprecated code.
= Version 2013.08.20 =
* #bugFix fixed an issue when getting a variation on an experiment with only one variation was causing an error.
= Version 2013.08.09 =
* Initial Release


== Upgrade notice ==
No upgrade notice.
