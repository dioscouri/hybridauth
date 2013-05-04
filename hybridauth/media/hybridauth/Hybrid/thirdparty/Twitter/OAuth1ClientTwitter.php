<?php 

require_once Hybrid_Auth::$config["path_libraries"] . "OAuth/OAuth1Client.php";

class OAuth1ClientTwitter extends OAuth1Client 
{
	/** 
	* Make signed request  
	*/ 
	function signedRequest( $url, $method, $parameters )
	{
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
		switch ($method) {
			case 'GET': return $this->request( $request->to_url(), 'GET' );
			case 'POST': return $this->request( $request->get_normalized_http_url(), $method, $request->to_postdata() ) ;
			default   : return $this->request( $request->get_normalized_http_url(), $method, $request->to_postdata(), $request->to_header() ) ;
		}
	}
}