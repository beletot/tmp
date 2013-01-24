<?php
/**
 * SAML 2.0 IdP configuration for simpleSAMLphp.
 *
 * See: https://rnd.feide.no/content/idp-hosted-metadata-reference
 */

//$metadata['__DYNAMIC:1__'] = array(
	/*
	 * The hostname of the server (VHOST) that will use this SAML entity.
	 *
	 * Can be '__DEFAULT__', to use this entry by default.
	 */
	//'host' => '__DEFAULT__',

	/* X.509 key and certificate. Relative to the cert directory. */
	//'privatekey' => 'server.pem',
	//'certificate' => 'server.crt',

	/*
	 * Authentication source to use. Must be one that is configured in
	 * 'config/authsources.php'.
	 */
	//'auth' => 'example-userpass',
//);

 // The SAML entity ID is the index of this config.
  $metadata['__DYNAMIC:1__'] = array(

    // The hostname of the server (VHOST) that this SAML entity will use.
   //'host'        => 'sp.example.org',
   'host'        => '__DEFAULT__',
   

    // X.509 key and certificate. Relative to the cert directory.
    'privatekey'   => 'googleappsidp.pem',
    'certificate'  => 'googleappsidp.crt',

    // Authentication plugin to use. login.php is the default one that uses LDAP.
      //'auth' => 'example-userpass',
	'auth' => 'example-mysql',	
  );
