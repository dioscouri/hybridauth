<?php
/*
 * Friendster API client core classes
 * Copyright Friendster, Inc. 2002-2012
 */

class FriendsterAPIException extends Exception {
    public function __construct($code, $message = null) {
        if (is_null($message)) {
            $FAPIEC = new FriendsterAPIErrorCodes();
            $message = $FAPIEC->getDescription($code);
        }
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }
}

class FriendsterAPIResponseType {
    const SARRAY = 0;
    const RAW = 1;
    const XML = 2;
    const JSON = 3;
}

class FriendsterAPIErrorCodes {
    const SUCCESS = 0;

    /* General */
    const UNKNOWN = 1;

    const METHOD = 3;

    const XML = 6;

    const POST = 8;
    const URI = 9;

    const REQUEST = 10;

    const CURL = 60;
    const CURL_MISSING = 61;

    const NOCURL_GET = 62;
    const NOCURL_POST = 63;

    const AUTH_TOKEN = 200;
    const AUTH_TOKEN_EMPTY = 201;
    const AUTH_TOKEN_MISSING = 202;

    const SESSION = 210;
    const SESSION_EMPTY = 211;
    const SESSION_MISSING = 212;

    const ARGS = 220;

    const SIGNATURE = 230;

    public $api_error_description = array (
        self::METHOD                =>    'Method Invalid',
        self::REQUEST                =>    'Request type unknown',
        self::XML                    =>    'XML Invalid',
        self::CURL                    =>    'Curl Error',
        self::CURL_MISSING            =>    'Curl Not Installed',
        self::NOCURL_GET            =>    'GET failed, non-curl',
        self::NOCURL_POST            =>    'POST failed, non-curl',
        self::POST                    =>    'Post data invalid',
        self::AUTH_TOKEN            =>    'Auth Token Invalid',
        self::AUTH_TOKEN_EMPTY        =>    'Auth Token Empty',
        self::AUTH_TOKEN_MISSING    =>    'Auth Token Unavailable',
        self::SESSION                =>    'Session Invalid',
        self::SESSION_EMPTY            =>    'Session Empty',
        self::SESSION_MISSING        =>    'Session Unavailable',
        self::ARGS        =>    'Empty array of arguments',
        self::SIGNATURE        =>    'Invalid signature',
    );

    public function getDescription($code) {
        return array_key_exists($code, $this->api_error_description)
            ? $this->api_error_description[$code]
            : strval($code);
    }
}

class Request {

    const TIMEOUT = 30;
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const BLOCK_SIZE = 4096;
    const USER_AGENT = 'FriendsterAPI PHP5 Client 1.1';

    function execute($url, $vars = null, $method = 'GET') {

        if (( $method == self:: GET ) && (!is_null($vars))) {
            $url .= "?$vars";
        }

        if ($GLOBALS['friendster_api']['debug']) {
            echo print_r($url,true);  
        };

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT . ' (curl)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if ($method == self::POST) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
            }
            elseif ($method != self::GET) {

                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
            }
            $data = curl_exec($ch);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            if ($GLOBALS['friendster_api']['debug']) {
                echo print_r($data,true);  
            };
            
            if ($data) return $data;

            if ($curl_errno > 0) {
                throw new FriendsterAPIException(FriendsterAPIErrorCodes::CURL, $curl_error);
            }
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::CURL);
        } else { // non-curl version
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::CURL, FriendsterAPIErrorCodes::CURL_MISSING);
        }
    }
}


/*
 * FriendsterAPI PHP5 client
 * Copyright Friendster, Inc. 2002-2008
 *
 */

require_once(dirname(__FILE__) . '/FriendsterAPI.inc');

class InvalidMethodException extends Exception {
  
}

class FriendsterAPI {

    const FRIENDSTER_API_VERSION = 1;

    // param names
    const PARAM_API_KEY = 'api_key';
    const PARAM_SIG = 'sig';
    const PARAM_SESSION_KEY = 'session_key';
    const PARAM_CALL_ID = 'nonce';
    const PARAM_AUTH_TOKEN = 'auth_token';
    const PARAM_UID = 'uid';
    const PARAM_CONTENT = 'content';
    const PARAM_INSTANCE_ID = 'instance_id';

    // api names
    const API_SESSION = 'session';
    const API_TOKEN = 'token';
    const API_USERS = 'user';
    const API_FANS = 'fans';
    const API_FRIENDS = 'friends';
    const API_AREFRIENDS = 'arefriends';
    const API_DEPTH = 'depth';
    const API_SHOUTOUT = 'shoutout';
    const API_NOTIFICATION = 'notification';
    const API_APPFRIENDS = 'application/friends';
     const API_WALLET = 'wallet';
    const API_WALLETSANDBOX = 'wallet-sandbox';

    private $isSandbox;


    // invalid call_id
    private $call_id = 0;

    private $path_info_prefix;
    private $server_base;
    private $api_key;
    private $secret;
    public  $session_key;
    private $last_call_id;
    private $base_www_url;

    public function __construct($api_key, $secret, $session_key = null,$base_api_url = 'https://neutron.friendster.com', $base_www_url = 'https://neutron.friendster.com',
             $isSandbox = FALSE) {
        // Friendster API server host
        $this->path_info_prefix =  '/v' . self::FRIENDSTER_API_VERSION . '/';
        $this->server_base = trim($base_api_url, '/') . $this->path_info_prefix;
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->session_key = $session_key;
        $this->base_www_url = $base_www_url;
        $this->isSandbox = $isSandbox;
    }

    public function setResponseType($response_type) {
        $this->$response_type = $response_type;
    }

    public function token() {
        $response = $this->doRequest(self::API_TOKEN, null, null, Request::POST);

        if (!array_key_exists(self::PARAM_AUTH_TOKEN, $response)) {
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::AUTH_TOKEN_MISSING);
        }

        if (strlen($response[self::PARAM_AUTH_TOKEN]) == 0) {
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::AUTH_TOKEN_EMPTY);
        }
        return $response[self::PARAM_AUTH_TOKEN];
    }

    public function users($uids, $params = null, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {

        if ($params!=null && $callback!=null) {
            $params = array_merge(params,array('callback'=>$callback));
        } else {
            $params = array('callback'=>$callback);
        }
        return $this->doRequest(self::API_USERS, $uids, $params, Request::GET, $response_type);
    }

    public function arefriends($uids, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();
        if ($callback!=null) {
            $params =array('callback'=>$callback);
        }
        return $this->doRequest(self::API_AREFRIENDS, $uids, $params, Request::GET, $response_type);
    }

    public function depth($uids, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();
        if ($callback!=null) {
            $params =array('callback'=>$callback);
        }
        return $this->doRequest(self::API_DEPTH, $uids, $params, Request::GET, $response_type);
    }

    public function post_shoutout($content,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();
        if ($callback!=null) {
            $params =array('callback'=>$callback);
        }
        return $this->doRequest(self::API_SHOUTOUT, null, array('content'=>$content), Request::POST, $response_type);
    }

    public function get_shoutout($uids,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();
        if ($callback!=null) {
            $params =array('callback'=>$callback);
        }
        return $this->doRequest(self::API_SHOUTOUT, $uids, $params, Request::GET, $response_type);
    }

 
    public function friends($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();
        if ($callback!=null) {
            $params =array('callback'=>$callback);
        }
        return $this->doRequest(self::API_FRIENDS, $uid, $params, Request::GET, $response_type);
    }

    /**
     * Post a notification
     */
    public function post_notification($uids,$subject,$label,$content,$url_fragment="",$notification_type=2, $response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
        $params = array( 'subject'=>$subject,'label'=>$label,'content'=>$content,'url_fragment'=>$url_fragment,'type'=>$notification_type);
        if ($callback!=null) {
            $params = array_merge($params,array('callback'=>$callback));
        }
        return $this->doRequest(self::API_NOTIFICATION, $uids, $params, Request::POST, $response_type);
    }


    /* Get the list of friends who have the application currently installed */
    public function get_application_friends($response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {

        $params = array();
        if ($callback!=null) {
            $params = array('callback'=>$callback);
        }
        return $this->doRequest(self::API_APPFRIENDS, null, $params, Request::GET, $response_type);
    }

    private function getResource(){
        return ($this->isSandbox?self::API_WALLETSANDBOX:self::API_WALLET);
    }


  public function get_wallet_balance($uid = null, $response_type = FriendsterAPIResponseType::SARRAY){
      $params = null;
      if ($uid != null) {
         $params = array('uid'=>$uid);
      }
      return $this->doRequest($this->getResource() . '/balance', null, $params, Request::GET, $response_type);
   }

   public function create_wallet($uid, $coins = 0, $wallet_key = 'wallet', $response_type = FriendsterAPIResponseType::SARRAY){
      if (!$this->isSandbox){
         throw new InvalidMethodException();
      }
      $params = array('coins' => $coins, 'wallet_key' => $wallet_key);
      return $this->doRequest($this->getResource() . '/create', $uid, $params, Request::POST, $response_type);
   }

   public function payment_wallet($name, $description, $amt, $params = '', $ref_no = '', $response_type = FriendsterAPIResponseType::SARRAY, $callback = null){
      $params = array('name' => $name, 'description' => $description, 'amt' => $amt, 'params' => $params, 'ref_no' => $ref_no);
      if ($callback!=null) {
         $params =array_merge($params,array('callback'=>$callback));
      }
      return $this->doRequest($this->getResource() . '/payment', null, $params, Request::POST, $response_type);
   }

   public function payment_commit_wallet($request_token, $response_type = FriendsterAPIResponseType::SARRAY){
      $params = array('request_token' => $request_token);
      return $this->doRequest($this->getResource() . '/commit', null, $params, Request::POST, $response_type);
   }

   protected function queryString($params) {
        $qstring = '';
        foreach ($params as $key => $value)
            $qstring .= $key . '=' . urlencode($value) . '&';
        return rtrim($qstring, '&');
   }

   public function get_signed_url($api_key, $secret, $host_path, $params = array()) {
       $url_info = parse_url($host_path);
       $path = $url_info['path'];
       $params['api_key'] = $api_key;
       $params['sig'] = $this->generateSignature($path, $params, $secret);
       return  $host_path."?".$this->queryString($params);
   }

    /**
     * Create a FriendsterAPI instance from Friendster iframe params
     */
    public static function from_friendster($secret, $api_key = null, $session_key = null, $check_signature = true) {
        
        if ($check_signature) {
           self::validateSignature($secret);
        }
        
        $server_base = "https://" . (empty($_GET['api_domain']) ? 'neutron.friendster.com' : $_GET['api_domain']);
        $server_www_url = "https://".(empty($_GET['api_domain']) ? 'neutron.friendster.com' : $_GET['api_domain']);
        $api_key = $api_key ? $api_key : $_GET['api_key'];
        $session_key = $_GET['session_key'] ? $_GET['session_key'] : $session_key;
        return new FriendsterAPI($api_key, $secret, $session_key, $server_base, $server_www_url);
    }


    /* encodeParams
     */
    private function encodeParams(&$params) {
        $encoded_params = '';
        foreach($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(",", $value);
                $params[$key] = $value;
            }
            $encoded_params[] = $key . '=' . urlencode($value);
        }
        return $encoded_params;
    }

    /* doRequest(
     * @method: 'user', - method name
     * @args: array(57519, 1234), - list of args OR single arg
     * @params: array('status' => 'maried'), - get/post params
     * @type: 'POST' || 'GET'
     */
    public function doRequest($method, $args, $params, $type = Request::GET,$response_type = FriendsterAPIResponseType::SARRAY) {

        // http://api.friendster.com/getUser
        $url = $this->server_base . $method;
        $argstr = "";
        if (!is_null($args)) {
            // http://api.friendster.com/getUser/
            $argstr = "/";
            if (is_array($args)) {
                if (count($args) == 0) {
                    throw new FriendsterAPIException(FriendsterAPIErrorCodes::ARGS);
                }
                // http://api.friendster.com/getUser/57519,1234
               $argstr .= implode(",", $args);
            } else {
                // http://api.friendster.com/getUser/57519
                $argstr .= $args;
            }
            $url .= $argstr;
        }

        //Set the requested format
        if ($response_type == FriendsterAPIResponseType::XML) {
            $params['format'] = 'xml';
        } else
            if ($response_type == FriendsterAPIResponseType::JSON) {
                $params['format'] = 'json';
            }

        $params[self::PARAM_API_KEY] = $this->api_key;
        if (!is_null($this->session_key)) {
            $params[self::PARAM_SESSION_KEY] = $this->session_key;
            $params[self::PARAM_CALL_ID] = $this->getCallId(true);
        }

        $filename = null;
        foreach($params as $key=>$value) {
            if ($key=='filepath') {
                $filename = '@'.$value;
                unset($params['filepath']);
            }
        }

        $encoded_params = $this->encodeParams($params);
        $pathinfo = $this->path_info_prefix . $method . $argstr;
        $encoded_params[] = self::PARAM_SIG . '=' . $this->generateSignature($pathinfo, $params, $this->secret);

        //$url .= '?' . implode("&", $encoded_params);
        if (strlen($url) == 0) {
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::URI);
        }

        $param_string = implode('&', $encoded_params);
        //Uncomment to enable server side debugging - you may have to disable signature
        //$param_string .= '&DBGSESSID=1@localhost:10001';
        if ($GLOBALS['friendster_api']['debug']) { echo "URL:\t$url\nMETHOD:\t$type\nPARAMS:\t$param_string\n\n"; error_log("LOG:".$response); }

        $req = new Request();

        //Handle file upload scenarios
        if ($filename!=null) {
            $params['filepath'] = $filename;
            $md5sig = $this->generateSignature($pathinfo, $params, $this->secret);
            $params['sig'] = $md5sig;
            $encoded_params = $this->encodeParams($params);
            $param_string = implode('&', $encoded_params);
            $response =  $req->execute($url, $param_string, $type);
        } else {
            $response =  $req->execute($url, $param_string, $type);
        }
        $req = null;
        if ($GLOBALS['friendster_api']['debug']) { echo "RESPONSE:\n["; print_r($response); echo "]\n\n"; error_log(print_r($response,true)); }

        if ( $response_type != FriendsterAPIResponseType::SARRAY) {

            $result = $response;
        } else {
            $xml_obj = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml_obj instanceof SimpleXMLElement) {
                $result = self::sxml_to_array($xml_obj);
            } else {
                throw new FriendsterAPIException(FriendsterAPIErrorCodes::XML);
            }
        }

        return $result;
    }

    protected function generateSignature($pathinfo, $params, $secret) {
        $str = $pathinfo;
        ksort($params);
        foreach($params as $key => $value) {
            if ($key != self::PARAM_SIG) {
               $str .= "$key=$value";
            }
        }
        return md5($str . $secret);
    } // generateSignature

    /**
     * Validate callback URL signature
     */
    public static function validateSignature($secret) {
        $uri = $_SERVER['REQUEST_URI'];

        $uria = split('\?', $uri, 2);
        $pathinfo = $uria[0];
        if ( isset($uria[1]) && !empty($uria[1]) ) {
            $querystring = $uria[1];

            $params = array();
            $pairs = split('&', $querystring);
            for ( $i=0; $i < count($pairs); ++$i ) {
               $keyval = split('=', $pairs[$i], 2);
               $params[$keyval[0]] = isset($keyval[1]) ? $keyval[1]: '';
            }
        } else {
            $params = $_POST;
        }
        if ( count($params) == 0 )
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::SIGNATURE);

        ksort($params);
        $str = '';
        $sig = '';
        
        $signed_keys = null;
        
        if ($params['signed_keys']) {
          $signed_keys = array_fill_keys(explode(',',$params['signed_keys']),0);
        }
        
        foreach($params as $key => $value) {
            
            if ($key!='sig' && $signed_keys!=null) {
              if (!isset($signed_keys[$key])) continue;
            }
            
            if (!empty($key)) {
                if ($key != self::PARAM_SIG) {
                    $str .= "$key=".$value;
                }
                else {
                    $sig = $value;
                }
            }
        }
   
        if ( md5($pathinfo . $str . $secret) != $sig ) {
            $pathinfo = rtrim($pathinfo, '/');
            if ( md5($pathinfo . $str . $secret) != $sig )
                throw new FriendsterAPIException(FriendsterAPIErrorCodes::SIGNATURE);
        }
    }// validateSignature

    public static function sxml_to_array($sxml) {
        $result = array();
        if ($sxml) {
            foreach($sxml as $key => $value) {
                if ($sxml['list']) {
                    $result[] = self::sxml_to_array($value);
                } else {
                    $result[$key] = self::sxml_to_array($value);
                }
            }
        }

        return sizeof($result) > 0 ? $result : (string)$sxml;
    } // sxml_to_array

    protected function getCallId($generate = true) {
        if ($generate) {
            $timestamp = microtime(true);
            if ($timestamp <= $this->last_call_id ) {
                $timestamp = $this->last_call_id + 0.001;
            }
            $this->last_call_id = $timestamp;
        }
        return $this->last_call_id;
    } // getCallId
}
