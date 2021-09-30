<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manifest.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
	'package' => array(
		'type' => 'module',
		'name' => 'sitegateway',
		'version' => '5.4.1',
		'path' => 'application/modules/Sitegateway',
		'title' => 'Advanced Payment Gateways / Stripe Connect',
		'description' => 'Advanced Payment Gateways / Stripe Connect',
		'author' => '<a href="http://www.socialapps.tech" style="text-decoration:underline;" target="_blank">SocialApps.tech</a>',
		'callback' => array(
			'class' => 'Engine_Package_Installer_Module',
		),
		'actions' => array(
			0 => 'install',
			1 => 'upgrade',
			2 => 'refresh',
		),
		'callback' => array(
			'path' => 'application/modules/Sitegateway/settings/install.php',
			'class' => 'Sitegateway_Installer',
		),
		'directories' => array(
			0 => 'application/modules/Sitegateway',
			1 => 'application/libraries/Stripe',
			2 => 'application/libraries/MangoPay',
			3 => 'application/libraries/Mollie',
			4 => 'application/libraries/Engine/Service/Stripe',
			5 => 'application/libraries/Engine/Service/MangoPay',
			6 => 'application/libraries/Engine/Service/Payumoney',
			7 => 'application/libraries/Engine/Service/Paynow',
			8 => 'application/libraries/Engine/Service/Mollie',

		),
		'files' => array(
			0 => 'application/languages/en/sitegateway.csv',
			1 => 'application/libraries/Engine/Payment/Gateway/Stripe.php',
			2 => 'application/libraries/Engine/Payment/Gateway/MangoPay.php',
			3 => 'application/libraries/Engine/Payment/Gateway/Payumoney.php',
			4 => 'application/libraries/Engine/Payment/Gateway/Paynow.php',
			5 => 'application/libraries/Engine/Payment/Gateway/Mollie.php',
			6 => 'application/libraries/Engine/Service/Stripe.php',
			7 => 'application/libraries/Engine/Service/MangoPay.php',
			8 => 'application/libraries/Engine/Service/Payumoney.php',
			9 => 'application/libraries/Engine/Service/Paynow.php',
			10 => 'application/libraries/Engine/Service/Mollie.php',

		),
		'tests' => array(
			// PHP Extensions
			array(
				'type' => 'PhpExtension',
				'name' => 'MCrypt',
				'extension' => 'mcrypt',
				'defaultErrorType' => 1,
				'messages' => array(
					'noExtension' => 'We recommend installing the mcrypt extension. ' .
					'Your payment gateway login information will be stored ' .
					'encrypted if this extension is available.',
				),
			),
			array(
				'type' => 'PhpExtension',
				'name' => 'Curl',
				'extension' => 'curl',
				'messages' => array(
					'noExtension' => 'The Curl extension is required.',
				),
			),
		),
	),
	// Items ---------------------------------------------------------------------
	'items' => array(
		'sitegateway_transaction',
	),
);
