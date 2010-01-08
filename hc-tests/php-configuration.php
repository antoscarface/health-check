<?php
/**
 * Tests to check for php config issues.
 *
 * @package HealthCheck
 * @subpackage Tests
 */

/**
 * Check that we are running at least PHP 5
 * 
 * @todo Provide a link to a codex article
 * @link http://core.trac.wordpress.org/ticket/9751
 * @link http://www.php.net/archive/2007.php#2007-07-13-1
 * @author peterwestwood
 */
class HealthCheck_PHP_Version extends HealthCheckTest {
	function run_test() {
		$message = sprintf( __( 'Your Webserver is running PHP version %1$s. WordPress will no longer support it in future version because it is <a href="%2$s">no longer receiving security updates</a>. Please contact your host and have them fix this as soon as possible.', 'health-check' ), PHP_VERSION, 'http://www.php.net/archive/2007.php#2007-07-13-1' );
		$this->assertTrue(	version_compare('5.0.0', PHP_VERSION, '<'),
							$message,
							HEALTH_CHECK_ERROR );
	}
}
HealthCheck::register_test('HealthCheck_PHP_Version');


/**
 * Check that we don't have safe_mode
 * 
 * @link http://php.net/manual/en/features.safe-mode.php
 * @author Denis de Bernardy
 */
class HealthCheck_SafeMode extends HealthCheckTest {
	function run_test() {
		$message = sprintf( __( 'Your Webserver is running PHP with safe_mode turned on. In addition to being an <a href="%1$s">architecturally incorrect way to secure a web server</a>, it introduces scores of quirks in PHP. It has been deprecated in PHP 5.3 and dropped in PHP 6.0. Please contact your host to have them fix this.', 'health-check' ), 'http://php.net/manual/en/features.safe-mode.php' );
		$this->assertFalse(	(bool) ini_get('safe_mode'),
							$message,
							HEALTH_CHECK_RECOMMENDATION );
	}
}
HealthCheck::register_test('HealthCheck_SafeMode');


/**
 * Check that default_charset is not set to a bad value in php.ini
 * 
 * Validates against the following rules:
 * 
 * 	Max 40 chars
 * 	A-Z
 *  
 * @link http://www.w3.org/International/O-HTTP-charset
 * @link http://www.iana.org/assignments/character-sets
 * @link http://blog.ftwr.co.uk/archives/2009/09/29/missing-dashboard-css-and-the-perils-of-smart-quotes/
 * @author peterwestwood
 */
class HealthCheck_PHP_DefaultCharset extends HealthCheckTest {
	function run_test() {
		$message = sprintf( __( 'Default character set configured in php.ini %s contains illegal characters. Please contact your host to have them fix this.', 'health-check' ), $configured);
		$configured = ini_get('default_charset');
		$filtered = preg_replace('|[^a-z0-9_.\-:]|i', '', $configured);
		$this->assertEquals($configured, $filtered,
							$message,
							HEALTH_CHECK_ERROR );
	}
}
HealthCheck::register_test('HealthCheck_PHP_DefaultCharset');


/**
 * Check libxml2 versions for known issue with XML-RPC
 * 
 * Based on code in Joseph Scott's libxml2-fix plugin
 * which you should install if this test fails for you
 * as a stop gap solution whilest you get your server upgraded
 * 
 * @link http://josephscott.org/code/wordpress/plugin-libxml2-fix/
 * @link http://core.trac.wordpress.org/ticket/7771
 * 
 * @author peterwestwood
 */
class HealthCheck_PHP_libxml2_XMLRPC extends HealthCheckTest {
	function run_test() {
		$message = sprintf(	__('Your webserver is running PHP version %1$s with libxml2 version %2$s which will cause problems with the XML-RPC remote posting functionality. You can read more <a href="%3$s">here</a>. Please contact your host to have them fix this.', 'health-check'),
							PHP_VERSION,
							LIBXML_DOTTED_VERSION,
							'http://josephscott.org/code/wordpress/plugin-libxml2-fix/');
		$this->assertNotEquals( '2.6.27', LIBXML_DOTTED_VERSION, $message, HEALTH_CHECK_ERROR );
		$this->assertNotEquals( '2.7.0', LIBXML_DOTTED_VERSION, $message, HEALTH_CHECK_ERROR );
		$this->assertNotEquals( '2.7.1', LIBXML_DOTTED_VERSION, $message, HEALTH_CHECK_ERROR );
		$this->assertNotEquals( '2.7.2', LIBXML_DOTTED_VERSION, $message, HEALTH_CHECK_ERROR );
		$this->assertFalse( ( LIBXML_DOTTED_VERSION == '2.7.3' && version_compare( PHP_VERSION, '5.2.9', '<' ) ), $message, HEALTH_CHECK_ERROR );
	}
}
HealthCheck::register_test('HealthCheck_PHP_libxml2_XMLRPC');
?>