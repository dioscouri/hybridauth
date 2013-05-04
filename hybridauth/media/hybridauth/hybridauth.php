<?php
/**
* HybridAuth
* 
* A Social-Sign-On PHP Library for authentication through identity providers like Facebook,
* Twitter, Google, Yahoo, LinkedIn, MySpace, Windows Live, Tumblr, Friendster, OpenID, PayPal,
* Vimeo, Foursquare, AOL, Gowalla, and others.
*
* Copyright (c) 2009-2011 (http://hybridauth.sourceforge.net) 
*/

// ------------------------------------------------------------------------
//	HybridAuth Config
// ------------------------------------------------------------------------

/**
 * - "base_url" is the url to HybridAuth EndPoint 'index.php'
 * - "providers" is the list of providers supported by HybridAuth
 * - "enabled" can be true or false; if you dont want to use a specific provider then set it to 'false'
 * - "keys" are your application credentials for this provider 
 * 		for example :
 *     		'id' is your facebook application id
 *     		'key' is your twitter application consumer key
 *     		'secret' is your twitter application consumer secret 
 * - To enable Logging, set debug_mode to true, then provide a path of a writable file on debug_file
 *  
 * Note: The HybridAuth Config file is not required, to know more please visit:
 *       http://hybridauth.sourceforge.net/userguide/Configuration.html
 */

return 
	array( 
		"base_url"         => "http://example.com/path/to/hybridauth/", 
 
		"providers"      => array (
			// openid
			"OpenID" 		=> 	array ( 
									"enabled" 	=> true 
								),

			// google
			"Google" 		=> 	array ( 
									"enabled" 	=> true 
								),

			// yahoo
			"Yahoo"             => 	array ( 
									"enabled" 	=> true 
								),

			// facebook
			"Facebook" 			=> array ( // 'id' is your facebook application id
									"enabled" 	=> true,
									"keys"	 	=> array ( "id" => "", "secret" => "" ) 
								),

			// myspace
			"MySpace" 	        => 	array ( // 'key' is your twitter application consumer key
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// twitter
			"Twitter"   	    => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// windows live
			"Live"  			=> array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "id" => "", "secret" => "" ) 
								),

			// linkedin
			"LinkedIn"          => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// tumblr
			"Tumblr"            => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// vimeo
			"Vimeo"             => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// foursquare
			"Foursquare"        => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								), 

			// gowalla
			"Gowalla"           => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// lastfm
			"LastFM"            => 	array ( 
									"enabled" 	=> true,
									"keys"	 	=> array ( "key" => "", "secret" => "" )
								),

			// PayPal
			"PayPal"  			=> array ( 
									"enabled" 	=> true, 
								),
		),

		"debug_mode"            => false, 

		// if you want to enable logging, set 'debug_mode' to true  then provide here a writable file by the web server 
		"debug_file"            => "", 
	);
