<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html 
*/

// ------------------------------------------------------------------------
//	Beginning of com_hybridauth modifications
// ------------------------------------------------------------------------
if (!empty($_SESSION["jsession_id"]))
{
    session_id( $_SESSION["jsession_id"] );
}
else
    if ( !empty($_COOKIE["jsession_id"]) ) {
    session_id( $_COOKIE["jsession_id"] );
}

// start a new session
session_start();
// ------------------------------------------------------------------------
//	End of com_hybridauth modifications
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------
//	HybridAuth End Point
// ------------------------------------------------------------------------

require_once( "Hybrid/Auth.php" );
require_once( "Hybrid/Endpoint.php" ); 

Hybrid_Endpoint::process();
