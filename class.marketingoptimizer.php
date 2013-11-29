<?php
class marketingoptimizer {
	public $marketing_optimizer;
	public $account_id;
	public $phone_publish_cls;
	public $phone_tracking;
	public $phone_tracking_thank_you_url;
	public $phone_tracking_default_number;
	public $form_default_id;
// 	public $google_analytics;
// 	public $google_analytics_account_id;
// 	public $google_analytics_cross_domain;
// 	public $google_analytics_domains;
	public $variation_pages;
	public $variation_conversion_page;
	public $variation_id;
	public $variation_percentage;
	public $cache_compatible;
	public $track_admin;
	public function __construct($acct_id = 0) {
		if ($acct_id > 0) {
			$this->account_id = $acct_id;
			$this->setMarketingOptimizer(get_option('mo_marketing_optimizer'));
			$this->setFormDefaultId(get_option('mo_form_default_id'));
			$this->setPhonePublishingClass(get_option('mo_phone_publish_cls'));
			$this->setPhoneTrackingDefaultNumber(get_option('mo_phone_tracking_default_number'));
			$this->setPhoneTrackingThankYouUrl(get_option('mo_phone_tracking_thank_you_url'));
			$this->setUsingPhoneTracking(get_option('mo_phone_tracking'));
			$this->setVariationPages(get_option('mo_variation_pages'));
			$this->setVariationPercentage(get_option('mo_variation_percentage'));
			$this->setCacheCompatible(get_option('mo_cache_compatible'));
			$this->setTrackAdmin(get_option('mo_track_admin'));
		}
	}
	public function _getWebsiteTrackingCode() {
		$websiteTrackingJs = '';
		$websiteTrackingJs .= "<!-- Start of Asynchronous Tracking Code --> \n";
		$websiteTrackingJs .= "<script type='text/javascript'> \n";
		$websiteTrackingJs .= "var _apVars = _apVars || []; \n";
		$websiteTrackingJs .= "_apVars.push(['_trackPageview']); \n";
		$websiteTrackingJs .= "_apVars.push(['_setAccount',\"$this->account_id\"]); \n";
		
		if($this->getVariationId()){
			$websiteTrackingJs .= "_apVars.push([ '_trackVariation',". $this->getVariationId()."]); \n";
		}
		if ($this->getUsingPhoneTracking () == 'true') {
			$websiteTrackingJs .= "_apVars.push([ '_publishPhoneNumber' ]); \n";
			if ($this->getPhonePublishingClass ()) {
				$websiteTrackingJs .= "_apVars.push([ '_setPhonePublishCls', '" . $this->getPhonePublishingClass () . "' ]); \n";
			} else {
				$websiteTrackingJs .= "_apVars.push([ '_setPhonePublishCls', 'phonePublishCls' ]); \n";
			}
			if($this->getPhoneTrackingDefaultNumber()){
				$websiteTrackingJs .="_apVars.push([ '_setDefaultPhoneNumber', '".$this->getPhoneTrackingDefaultNumber() . "' ]);\n";
			}
			if ($this->getPhoneTrackingThankYouUrl ()) {
				$websiteTrackingJs .= "_apVars.push([ '_redirectConversionUrl','" . $this->getPhoneTrackingThankYouUrl () . "']); \n";
			}
		}
		$websiteTrackingJs .= "(function(d){ \n";
		$websiteTrackingJs .= "var t = d.createElement(\"script\"), s = d.getElementsByTagName(\"script\")[0]; \n";
		$websiteTrackingJs .= "t.src =  \"//app.marketingoptimizer.com/remote/ap.js\"; \n";
		$websiteTrackingJs .= "s.parentNode.insertBefore(t, s); \n";
		$websiteTrackingJs .= "})(document); \n";
		$websiteTrackingJs .= "</script> \n";
		$websiteTrackingJs .= "<!-- End of Asynchronous Tracking Code --> \n";
		return $websiteTrackingJs;
	}

	public function _getGoogleAnalyticsCode(){
		$domain = get_bloginfo('url');
		$domain  = str_replace('http://','',$domain);
		$googleAnalyticsTrackingJs = '';
		$googleAnalyticsTrackingJs .= "<!-- Start of Google Analytics Tracking Code --> \n";
		$googleAnalyticsTrackingJs .= "<script type=\"text/javascript\"> \n";
		$googleAnalyticsTrackingJs .= "var _gaq = _gaq || []; \n";
		$googleAnalyticsTrackingJs .= "_gaq.push(['_setAccount', '".$this->getGoogleAnalyticsAccountId()."']); \n";
		if(get_option('mo_google_analytics_cross_domain') == 'true' && get_option('mo_google_analytics_domains')){
			$googleAnalyticsTrackingJs .= "_gaq.push(['_setDomainName', '".$domain."']);\n";
			$googleAnalyticsTrackingJs .= "_gaq.push(['_setAllowLinker', true]);\n";
		}
		$googleAnalyticsTrackingJs .= "_gaq.push(['_trackPageview']); \n";
		$googleAnalyticsTrackingJs .= "(function() { \n";
		$googleAnalyticsTrackingJs .= "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; \n";
		$googleAnalyticsTrackingJs .= "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; \n";
		$googleAnalyticsTrackingJs .= "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); \n";
		$googleAnalyticsTrackingJs .= "})(); \n";
		$googleAnalyticsTrackingJs .= "</script>";
		return $googleAnalyticsTrackingJs;
	}
	
	public function setUsingPhoneTracking($phone_tracking) {
		return $this->phone_tracking = $phone_tracking;
	}
	public function getUsingPhoneTracking() {
		return $this->phone_tracking;
	}
	public function getPhonePublishingClass() {
		return $this->phone_publish_cls;
	}
	public function setPhonePublishingClass($phone_publish_cls) {
		return $this->phone_publish_cls = $phone_publish_cls;
	}
	public function getPhoneTrackingThankYouUrl() {
		return $this->phone_tracking_thank_you_url;
	}
	public function setPhoneTrackingThankYouUrl($phone_tracking_thank_you_url) {
		return $this->phone_tracking_thank_you_url = $phone_tracking_thank_you_url;
	}
	public function getPhoneTrackingDefaultNumber() {
		return $this->phone_tracking_default_number;
	}
	public function setPhoneTrackingDefaultNumber($phone_tracking_default_number) {
		return $this->phone_tracking_default_number = $phone_tracking_default_number;
	}
	public function getFormDefaultId() {
		return $this->form_default_id;
	}
	public function setFormDefaultId($form_default_id) {
		return $this->form_default_id = $form_default_id;
	}
	public function getUsingGoogleAnalytics() {
		return $this->google_analytics;
	}
	public function setUsingGoogleAnalytics($google_analytics) {
		return $this->google_analytics = $google_analytics;
	}
	public function getGoogleAnalyticsAccountId() {
		return $this->google_analytics_account_id;
	}
	public function setGoogleAnalyticsAccountId($google_analytics_account_id) {
		return $this->google_analytics_account_id = $google_analytics_account_id;
	}
	public function getGoogleAnalyticsCrossDomain() {
		return $this->google_analytics_cross_domain;
	}
	public function setGoogleAnalyticsCrossDomain($google_analytics_cross_domain) {
		return $this->google_analytics_cross_domain = $google_analytics_cross_domain;
	}
	public function getGoogleAnalyticsDomains() {
		return $this->google_analytics_domains;
	}
	public function setGoogleAnalyticsDomains($google_analytics_domains) {
		return $this->google_analytics_domains = $google_analytics_domains;
	}
	public function getVariationPages() {
		return $this->variation_pages;
	}
	public function setVariationPages($variation_pages) {
		return $this->variation_pages = $variation_pages;
	}
	public function getVariationId() {
		return $this->variation_id;
	}
	public function setVariationId($variation_id) {
		return $this->variation_id = $variation_id;
	}
	public function getMarketingOptimizer() {
		return $this->marketing_optimizer;
	}
	public function getTrackAdmin() {
		return $this->track_admin;
	}
	public function setMarketingOptimizer($marketing_optimizer) {
		return $this->marketing_optimizer = $marketing_optimizer;
	}
	public function getVariationPercentage() {
		return $this->variation_percentage;
	}
	public function setVariationPercentage($variation_percentage) {
		return $this->variation_percentage = $variation_percentage;
	}
	public function getCacheCompatible() {
		return $this->cache_compatible;
	}
	public function setCacheCompatible($cache_compatible) {
		return $this->cache_compatible = $cache_compatible;
	}
	public function setTrackAdmin($track_admin) {
		return $this->track_admin = $track_admin;
	}
}
// <!-- Start of Asynchronous Tracking Code -->
//     <script type="text/javascript">
//         // Any lines in this script that begin with "//" are comments and will
//         // not be executed. You may leave them as-is, delete them, or uncomment
//         // them by removing the "//" as you deem necessary. We recommend that
//         // delete them all when you have successfully installed the code on your site.

//         var _apVars = _apVars || [];
//         _apVars.push(["_trackPageview"]);

//         // Your account ID. This is required.
//         _apVars.push(["_setAccount", "1"]);

//         // BEGIN A/B AND MULTIVARIATE TESTING
//         // Uncomment the next statement to activate webite experiments.
//         // The "_trackVariation" setting below is also required for the
//         // testing and experiments tracking to function.
//         //
//          // _apVars.push([ "_trackVariation", "_your_variation_id_ ]);
//         //
//          // END A/B AND MULTIVARIATE TESTING
        

//          // BEGIN INBOUND PHONE TRACKING
//          // Uncomment the next statement to activate inbound phone tracking.
//          // The "_setPhonePublishCls" setting below is also required for the
//          // phone tracking to function.
//          //
//          // _apVars.push([ "_publishPhoneNumber" ]);

//          // Set the CSS selector to identify the DOM element that will have the
//          // phone number inserted. For example, add the following to your page
//          //  in the exact location you would like the phone number to display:
//          //
//          //  	<span class="phonePublishCls">(231) 946-2334</span>.
//          //
//          // The content of this element will be replaced with the correct routing
//          // number. It is a good idea to put your default routing number in the
//          // <span> (or <div> or <p> or <any other element you want>) so that
//          // there is always a telephone number displayed, regardless of the time
//          // it takes to load the number.
//          //
//          // _apVars.push([ "_setPhonePublishCls", "phonePublishCls" ]);

//          // Uncomment the next statement to override the phone number .
//          // that displays by default when there is no campaign associated with
//          // the current visit. Note: If you are using the visitor-level phone tracking
//          //  this setting is not necessary.
//          //
//          //_apVars.push([ "_setDefaultPhoneNumber", "(231) 946-2334" ]);
//          //

//          // Uncomment the next statement if you like to redirect visitors who call
//          // to a confirmation page. This will allow you to use conversion tracking
//          // from any other analytics program that tracks conversion based on the
//          // loading of a specific URL. Put your "Thank You" URL instead of the
//          // example shown below.
//          //
//          // _apVars.push([ "_redirectConversionUrl", "http://www.example.com/thank-you/"]);
//          //
//          // END INBOUND PHONE TRACKING
        
//         // Uncomment the next statement and replace the "0" with the visitor
//         // or contact id in the system to force the system to recognize the
//         // visitor id without using cookies or looking up the IP address.
//         // _apVars.push(["_setVisitorId", "0"]);

//         // Uncomment the next statement to track this pageview as a conversion.
//         // This is not necessary when using the built-in form publishing code in
//         // your system. It is useful when you want to track conversions that do not
//         //  come from a feedback form submissions. You may want to change this
//         // value on your "Thank You" and form submission confirmation pages.
//         // _apVars.push(["_setConversion", "1"]);

//         // Uncomment the next statement and change the "0" to the amount of
//         // revenue generated. This is generally most useful for shopping cart
//         // purchases where an actual transaction has taken place.
//         // _apVars.push(["_setRevenue","0"]);

//         // Uncomment the next statement and change "0" to the alphanumeric
//         // value of the product id that was sold. This is generally most useful
//         // for shopping cart purchases where an actual transaction has taken place.
//         // _apVars.push(["_setRetailProductId", "0"]);

//         // Uncomment the next statement and change "0" to the alphanumeric
//         // value of the customer associated with the conversion. This is generally
//         // most useful for shopping cart purchases where an actual transaction
//         // has taken place.
//         // _apVars.push(["_setCustomerId","0"]);

//         (function(d){
//             var t = d.createElement("script"), s = d.getElementsByTagName("script")[0];
//             t.src =  "//app.marketingoptimizer.com/remote/ap.js";
//             s.parentNode.insertBefore(t, s);
//         })(document);
//     </script>
// <!-- End of Asynchronous Tracking Code -->