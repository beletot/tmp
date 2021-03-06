<?php
/**
 * SAML 2.0 remote SP metadata for simpleSAMLphp.
 *
 * See: https://rnd.feide.no/content/sp-remote-metadata-reference
 */

/*
 * Example simpleSAMLphp SAML 2.0 SP
 */
/*$metadata['https://saml2sp.example.org'] = array(
	'AssertionConsumerService' => 'http://localhost/simplesaml/saml2/sp/AssertionConsumerService.php',
	'SingleLogoutService' => 'http://localhost/simplesaml/saml2/sp/SingleLogoutService.php',
);*/

/*
   * This example shows an example config that works with Google Apps for education.
   * What is important is that you have an attribute in your IdP that maps to the local part of the email address
   * at Google Apps. E.g. if your google account is foo.com, and you have a user with email john@foo.com, then you
   * must set the simplesaml.nameidattribute to be the name of an attribute that for this user has the value of 'john'.
   */
  $metadata['google.com'] = array(
    'AssertionConsumerService'   => 'https://www.google.com/a/epfc.eu/acs', 
    'spNameQualifier'            => 'epfc.eu',
    'NameIDFormat'               => 'urn:oasis:names:tc:SAML:2.0:nameid-format:email',
    'simplesaml.nameidattribute' => 'uid',
    'simplesaml.attributes'      => false
  );

