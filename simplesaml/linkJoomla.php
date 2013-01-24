<?php
class linkJoomla {
    /*function ____construct() {
        require_once (JPATH_BASE.DS.'includes'.DS.'defines.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'loader.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'filter'.DS.'filterinput.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'utility.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'registry'.DS.'registry.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'user.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'factory.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'helper.php');
    }*/
//autoload ne passe pas
	static function getToken(){
		require_once (JPATH_BASE.DS.'includes'.DS.'defines.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'loader.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
        		//update for Joomla 1.6
                require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'environment'.DS.'request.php');
                require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'session'.DS.'session.php');
                require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'application.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'filter'.DS.'filterinput.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'utility.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'registry'.DS.'registry.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'user.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'factory.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'helper.php');
		$token = JUtility::getToken();
		return $token;
	}
	static function testcrypt($password, $salt){
		require_once (JPATH_BASE.DS.'includes'.DS.'defines.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'loader.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'filter'.DS.'filterinput.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'utility.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'registry'.DS.'registry.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'user.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'factory.php');
        require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'helper.php');
		$testcrypt = JUserHelper::getCryptedPassword($password, $salt);
		return $testcrypt;
	}
}
?>
