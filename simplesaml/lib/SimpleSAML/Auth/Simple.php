<?php

/**
 * Helper class for simple authentication applications.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Auth_Simple {
	/**
	 * The id of the authentication source we are accessing.
	 *
	 * @var string
	 */
	private $authSource;


	/**
	 * Create an instance with the specified authsource.
	 *
	 * @param string $authSource  The id of the authentication source.
	 */
	public function __construct($authSource) {
		assert('is_string($authSource)');

		$this->authSource = $authSource;
	}


	/**
	 * Check if the user is authenticated.
	 *
	 * This function checks if the user is authenticated with the default
	 * authentication source selected by the 'default-authsource' option in
	 * 'config.php'.
	 *
	 * @return bool  TRUE if the user is authenticated, FALSE if not.
	 */
	public function isAuthenticated() {
		$session = SimpleSAML_Session::getInstance();
		//attention session joomla ne doit pas être sauvee en database
		//echo 'session <pre>'.print_r($session,true).'</pre>';
		//echo 'session is valid -> '.$session->isValid($this->authSource).'<br />';
		return $session->isValid($this->authSource);
	}


	/**
	 * Require the user to be authenticated.
	 *
	 * If the user is authenticated, this function returns immediately.
	 *
	 * If the user isn't authenticated, this function will authenticate the
	 * user with the authentication source, and then return the user to the
	 * current page.
	 *
	 * If $allowPost is set to TRUE, any POST data to the current page is
	 * preserved. If $allowPost is FALSE, the user will be returned to the
	 * current page with a GET request.
	 *
	 * @param array $options  Various options to the authentication request.
	 */
	public function requireAuth(array $options = array()) {

		$session = SimpleSAML_Session::getInstance();

		if ($session->isValid($this->authSource)) {
			/* Already authenticated. */
			return;
		}

		if (array_key_exists('KeepPost', $options)) {
			$keepPost = (bool)$options['KeepPost'];
		} else {
			$keepPost = TRUE;
		}

		if (array_key_exists('ReturnTo', $options)) {
			$returnTo = (string)$options['ReturnTo'];
		} else {
			$returnTo = SimpleSAML_Utilities::selfURL();
		}

		if ($keepPost && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$returnTo = SimpleSAML_Utilities::createPostRedirectLink($returnTo, $_POST);
		}

		/*
		 * An URL to restart the authentication, in case the user bookmarks
		 * something, e.g. the discovery service page.
		 */
		$restartURL = $this->getLoginURL($returnTo);

		$hints = array(
			SimpleSAML_Auth_State::RESTART => $restartURL,
		);

		SimpleSAML_Auth_Default::initLogin($this->authSource, $returnTo, NULL, $hints);
	}


	/**
	 * Log the user out.
	 *
	 * This function logs the user out. It will never return. By default,
	 * it will cause a redirect to the current page after logging the user
	 * out, but a different URL can be given with the $url parameter.
	 *
	 * @param string|NULL $url  The url the user should be redirected to after logging out.
	 *                          Defaults to the current page.
	 */
	public function logout($url = NULL) {
		assert('is_string($url) || is_null($url)');

		if ($url === NULL) {
			$url = SimpleSAML_Utilities::selfURL();
		}

		$session = SimpleSAML_Session::getInstance();
		if (!$session->isValid($this->authSource)) {
			/* Not authenticated to this authentication source. */
			SimpleSAML_Utilities::redirect($url);
			assert('FALSE');
		}

		SimpleSAML_Auth_Default::initLogout($url);
	}


	/**
	 * Retrieve attributes of the current user.
	 *
	 * This function will retrieve the attributes of the current user if
	 * the user is authenticated. If the user isn't authenticated, it will
	 * return an empty array.
	 *
	 * @return array  The users attributes.
	 */
	public function getAttributes() {
		//echo __LINE__.' function getAttributes <br />';
		if (!$this->isAuthenticated()) {
			/* Not authenticated. */
			return array();
		}

		/* Authenticated. */
		$session = SimpleSAML_Session::getInstance();
		return $session->getAttributes();
	}


	/**
	 * Retrieve an URL that can be used to log the user in.
	 *
	 * @param string|NULL $returnTo
	 *   The page the user should be returned to afterwards. If this parameter
	 *   is NULL, the user will be returned to the current page.
	 * @return string
	 *   An URL which is suitable for use in link-elements.
	 */
	public function getLoginURL($returnTo = NULL) {
		assert('is_null($returnTo) || is_string($returnTo)');

		if ($returnTo === NULL) {
			$returnTo = SimpleSAML_Utilities::selfURL();
		}

		$login = SimpleSAML_Module::getModuleURL('core/as_login.php');
		$login = SimpleSAML_Utilities::addURLparameter($login, array(
			'AuthId' => $this->authSource,
			'ReturnTo' => $returnTo,
		));

		return $login;
	}


	/**
	 * Retrieve an URL that can be used to log the user out.
	 *
	 * @param string|NULL $returnTo
	 *   The page the user should be returned to afterwards. If this parameter
	 *   is NULL, the user will be returned to the current page.
	 * @return string
	 *   An URL which is suitable for use in link-elements.
	 */
	public function getLogoutURL($returnTo = NULL) {
		assert('is_null($returnTo) || is_string($returnTo)');

		if ($returnTo === NULL) {
			$returnTo = SimpleSAML_Utilities::selfURL();
		}

		$logout = SimpleSAML_Module::getModuleURL('core/as_logout.php');
		$logout = SimpleSAML_Utilities::addURLparameter($logout, array(
			'AuthId' => $this->authSource,
			'ReturnTo' => $returnTo,
		));

		return $logout;
	}

}

?>