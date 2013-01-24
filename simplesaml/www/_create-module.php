<?php
require_once '../lib/_autoload.php';
$auth = new SimpleSAML_Auth_Simple('example-mysql');

$task = $_GET['task'];

//via cette page
//http://localhost/simplesaml/www/module.php/core/loginuserpass.php?AuthState=_49103a894b2d94d25482c39f5b8623113c363bc437%3Ahttp%3A%2F%2Flocalhost%2Fsimplesaml%2Fwww%2Fmodule.php%2Fcore%2Fas_login.php%3FAuthId%3Dexample-userpass%26ReturnTo%3Dhttp%253A%252F%252Flocalhost%252Fextranet-epfc%252F

//via google calendar
//http://localhost/simplesaml/www/module.php/core/loginuserpass.php?AuthState=_77dd7f25c79b3d200fc4056a0ddeb1d8f90f964194%3Ahttp%3A%2F%2Flocalhost%2Fsimplesaml%2Fwww%2Fsaml2%2Fidp%2FSSOService.php%3Fspentityid%3Dgoogle.com%26RelayState%3Dhttps%253A%252F%252Fwww.google.com%252Fa%252Fepfc.eu%252FServiceLogin%253Fservice%253Dcl%2526passive%253Dtrue%2526nui%253D1%2526continue%253Dhttp%25253A%25252F%25252Fwww.google.com%25252Fcalendar%25252Fhosted%25252Fepfc.eu%25252Frender

/*$auth->login(array(
    'saml:IsPassive' => TRUE,
    'ErrorURL' => 'https://.../error_handler.php',
));*/

switch ($task) {
    case 'isAuthenticated':
        isAuthenticated();
        break;
	
	case 'forceAuthn':
        forceAuthn();
        break;
	
	case 'requireAuth':
        requireAuth();
        break;
		
	case 'getLogoutURL':
        getLogoutURL();
        break;
	
	case 'getAttributes':
        getAttributes();
        break;
    
    case 'login':
        login();
        break;
	case 'logout':
		logout();
		break;

    default:
        requireAuth ();
    }
	/*
	 * Check whether the user is authenticated with this authentication source.
	 * TRUE is returned if the user is authenticated, FALSE if not.
	 */
    function isAuthenticated() {
        global $auth;
        /*$session = SimpleSAML_Session::getInstance();
        echo 'session name '.session_name().'<br />';
        echo 'session id '.session_id().'<br />';
        //echo('$session <pre>'.print_r($session, true).'</pre>');
        
        echo 'session <pre>'.print_r($_SESSION,true).'</pre>';
    	die();*/
        
        if (!$auth->isAuthenticated()) {			
			print('<a href="#">Login</a>');
        } else {
        	//echo '<pre>'.print_r($auth,true).'</pre>';
            $attrs = $auth->getAttributes();
			//echo '<pre>'.print_r($attrs,true).'</pre>';
            echo 'session utilisateur activée';
        }
    }
    function forceAuthn() {
        global $auth;
        $auth->login(array('saml:ForceAuthn'=>TRUE, ));
    }
    
    /*
	 * Return the user to the frontpage after authentication, don't post
	 * the current POST data.
	 */

	function requireAuth(){
		global $auth;
	    $auth->requireAuth(array(
		//AuthState
	    //'ReturnTo' => 'http://localhost/extranet-epfc/index.php?option=com_simplesamlphp&task=returngoogleapps',
	    'ReturnTo' => 'http://extranet.epfc.eu/_dev-extranet/index.php?option=com_simplesamlphp&task=returngoogleapps',
	    'KeepPost' => TRUE,
	    ));
	    //echo '<pre>'.print_r($return,true).'</pre>';
		 echo 'already logged';
	}
    /**
     *
     */
    function login() {
        global $auth;
        // not existing
        //$auth->bind('dev','dev'     	);
    }
	function getLogoutURL (){
		global $auth;
		$url = $auth->getLogoutURL();
		echo $url;
	}
	function getAttributes(){
		global $auth;
		$attrs = $auth->getAttributes();
		print_r($attrs);
		if (!isset($attrs['uid'][0])) {
		    throw new Exception('displayName attribute missing.');
		}
		$name = $attrs['uid'][0];
		
		print('Hello, ' . htmlspecialchars($name));
	}
	function logout(){
		global $auth;
		$url = $auth->getLogoutURL();
		print('<a href="' . htmlspecialchars($url) . '">Logout</a>');

	}
	function destroy(){
		session_start();

		// Détruit toutes les variables de session
		$_SESSION = array();
		
		// Si vous voulez détruire complètement la session, effacez également
		// le cookie de session.
		// Note : cela détruira la session et pas seulement les données de session !
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		
		// Finalement, on détruit la session.
		session_destroy();
	}
    
?>
