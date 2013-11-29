=== Marketing Optimizer for Wordpress ===
Contributors: activeinternetmarketing  
Donate link: 
Tags: a b test, a b testing, a/b test, a/b testing, ab test, abtesting, analytics, click tracking, content experiments, conversion pages, conversion optimization, conversion rate optimization, cpa, goal tracking, marketing optimizer, multivariate, multivariate test, landing page, landing pages, split testing, active internet marketing, cro, call tracking, statistics, stats, conversions, analytics, testing, experiments, metrics, testing
Requires at least: 3.3
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Optimize conversion rates by A/B testing your Wordpress site: test all your content for its effect on conversions, including landing pages.

== Description ==
= *New* Gravity Forms Integration = 
Now you can use the awesome Gravity Forms form builder on your WordPress site, and easily post all your form data directly into your Marketing Optimizer web application. 
= Conversion Rate Optimization Plugin =
A/B testing is the process of showing visitors one of two (or more) versions of the same web page, and then tracking which one created the most revenue, leads, signups, downloads, purchases, registrations, or comments. Every page on your Wordpress site contributes to conversion rates, not just the landing page or pages with a feedback form. To really bring your marketing 'A-Game', you need to test every important page to see how it contributes to your bottom line. 
= A/B Test Multiple Pages =
Landing pages are the obvious choice for A/B testing. You are already sending traffic to them, and if converting visitors on the landing page is your goal, testing different version is a must. Every page on your website that receives traffic contributes to conversions, and it pays to test those, too. Your report will include the results for each page variant and the conversion rate of visitors that saw that version. 
= Track Conversions Accurately =
Chances are you have at least one page that acts as your 'confirmation' or 'thank you' page for visitors that have just converted. You can now track all of these conversions with a simple shortcode that lets the system know that the visitor has just completed a successful conversion, and the proper version of all the web pages that visitor saw is credited with a conversion. This allows you to have many different conversion goal pages and still track all your conversions in properly.
= Automatically Promote Winners =
When you have completed your test, and there is a clear winner, just click the 'Promote' button. This replaces the old page with the content from the winning variation automatically. 
= Adjustable Multi-Armed Bandit =
The 'Multi-Armed Bandit', or Epsilon Greedy, method of rotating variations has been proven to be the very fastest way to determine a winner. Not only that, but it's also, by far, the most profitable way to display your test pages. Here's how it works: the page with the highest conversion rate gets 90% of the traffic ('Exploitation'), and the other variations are randomly rotated through the remaining 10% of the time ('Exploration'). In other words, 90% of the time the system chooses the best version of your page. The rest of the time it explores new variations in the hopes that they will prove to be a better solution.
Since sometimes it makes more sense to explore more than 10%, you have the ability to simply move the slider to explore between  10% and 100%. 
= Create Variations Quickly =
To make things easier and faster we have built-in the ability to clone either the control (original version of your page) or a variation as a new test. Take this copy of your existing page and make any changes you think might improve conversion rates. You don't have to start from scratch, but you have the freedom to make any changes you want. 
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
3. Check the 'Enable A/B Testing' checkbox in Settings to turn on the system for page variation experiments.

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
3. Shortcodes
4. Experiments

== Changelog ==
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
