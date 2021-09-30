<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    install.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Installer extends Engine_Package_Installer_Module {

	public function onPreInstall() {
		$db = $this->getDb();

		$getErrorMsg = $this->_getVersion();
		if (!empty($getErrorMsg)) {
			return $this->_error($getErrorMsg);
		}

		$PRODUCT_TYPE = 'sitegateway';
		$PLUGIN_TITLE = 'Sitegateway';
		$PLUGIN_VERSION = '5.4.1';
		$PLUGIN_CATEGORY = 'plugin';
		$PRODUCT_DESCRIPTION = 'Advanced Payment Gateways / Stripe Connect';
		$PRODUCT_TITLE = 'Advanced Payment Gateways / Stripe Connect';
		$_PRODUCT_FINAL_FILE = 0;
		$SocialEngineAddOns_version = '4.8.9p14';
		$file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
		$is_file = file_exists($file_path);
		if (empty($is_file)) {
			include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
		} else {
			$db = $this->getDb();
			$select = new Zend_Db_Select($db);
			$select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
			$is_Mod = $select->query()->fetchObject();
			if (empty($is_Mod)) {
				include_once $file_path;
			}
		}

		parent::onPreinstall();
	}

	public function onInstall() {
		$db = $this->getDb();

		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitegateway';");

		$result = $db->query("SHOW TABLE STATUS WHERE `Name` = 'engine4_payment_gateways'")->fetch();
		$next_increment = $result['Auto_increment'];

		$stripeGatewayId = $db->select()
			->from('engine4_payment_gateways', 'gateway_id')
			->where('plugin = ?', 'Sitegateway_Plugin_Gateway_Stripe')
			->limit(1)
			->query()
			->fetchColumn();

		if (empty($stripeGatewayId) && $next_increment > 5) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES ('Stripe', NULL, 0, 'Sitegateway_Plugin_Gateway_Stripe', 0);");
		} elseif (empty($stripeGatewayId)) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES (6, 'Stripe', NULL, 0, 'Sitegateway_Plugin_Gateway_Stripe', 0);");
		}

		$siteeventticketEnabled = $db->select()
			->from('engine4_core_modules')
			->where('name = ?', 'siteeventticket')
			->where('enabled = ?', 1)
			->limit(1)
			->query()
			->fetchColumn();

		if (!empty($siteeventticketEnabled)) {

			$orderTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_orders\'')->fetch();
			if (!empty($orderTable)) {
				$payment_split_column = $db->query("SHOW COLUMNS FROM engine4_siteeventticket_orders LIKE 'payment_split'")->fetch();
				if (empty($payment_split_column)) {
					$db->query("ALTER TABLE `engine4_siteeventticket_orders` ADD `payment_split` TINYINT(1) NOT NULL DEFAULT '0';");
				}
			}

			$eventGatewayTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_gateways\'')->fetch();
			if (!empty($eventGatewayTable)) {
				$eventIdIndex = $db->query("SHOW INDEX FROM `engine4_siteeventticket_gateways` WHERE Key_name = 'event_id'")->fetch();
				if (!empty($eventIdIndex)) {
					$db->query("ALTER TABLE `engine4_siteeventticket_gateways` DROP INDEX event_id");
					$db->query("ALTER TABLE `engine4_siteeventticket_gateways` ADD INDEX (`event_id`)");
				}
			}

			$eventBillTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_eventbills\'')->fetch();
			if (!empty($eventBillTable)) {
				$eventIdIndex = $db->query("SHOW INDEX FROM `engine4_siteeventticket_eventbills` WHERE Key_name = 'event_id'")->fetch();
				if (!empty($eventIdIndex)) {
					$db->query("ALTER TABLE `engine4_siteeventticket_eventbills` DROP INDEX event_id");
					$db->query("ALTER TABLE `engine4_siteeventticket_eventbills` ADD INDEX (`event_id`)");
				}

				$gateway_id_column = $db->query("SHOW COLUMNS FROM engine4_siteeventticket_eventbills LIKE 'gateway_id'")->fetch();
				if (empty($gateway_id_column)) {
					$db->query("ALTER TABLE `engine4_siteeventticket_eventbills` ADD `gateway_id` INT(11) NOT NULL AFTER `status`;");
				}
			}
		}

		$sitestoreproductEnabled = $db->select()
			->from('engine4_core_modules')
			->where('name = ?', 'sitestore')
			->where('enabled = ?', 1)
			->limit(1)
			->query()
			->fetchColumn();

		if (!empty($sitestoreproductEnabled)) {

			$orderTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_orders\'')->fetch();
			if (!empty($orderTable)) {
				$payment_split_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'payment_split'")->fetch();
				if (empty($payment_split_column)) {
					$db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `payment_split` TINYINT(1) NOT NULL DEFAULT '0';");
				}
			}

			$storeGatewayTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_gateways\'')->fetch();
			if (!empty($storeGatewayTable)) {
				$storeIdIndex = $db->query("SHOW INDEX FROM `engine4_sitestoreproduct_gateways` WHERE Key_name = 'store_id'")->fetch();
				if (!empty($storeIdIndex)) {
					$db->query("ALTER TABLE `engine4_sitestoreproduct_gateways` DROP INDEX store_id");
					$db->query("ALTER TABLE `engine4_sitestoreproduct_gateways` ADD INDEX (`store_id`)");
				}
			}

			$storeBillTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_storebills\'')->fetch();
			if (!empty($storeBillTable)) {
				$storeIdIndex = $db->query("SHOW INDEX FROM `engine4_sitestoreproduct_storebills` WHERE Key_name = 'store_id'")->fetch();
				if (!empty($storeIdIndex)) {
					$db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` DROP INDEX store_id");
					$db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` ADD INDEX (`store_id`)");
				}

				$gateway_id_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_storebills LIKE 'gateway_id'")->fetch();
				if (empty($gateway_id_column)) {
					$db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` ADD `gateway_id` INT(11) NOT NULL AFTER `status`;");
				}
			}
		}

		//START SITEGATEWAY RELATED WORK
		$sitecouponEnabled = $db->select()
			->from('engine4_core_modules')
			->where('name = ?', 'sitecoupon')
			->where('enabled = ?', 1)
			->limit(1)
			->query()
			->fetchColumn();

		$sitecouponTable = $db->query("SHOW TABLES LIKE 'engine4_sitecoupon_coupons'")->fetch();
		if (!empty($sitecouponEnabled) && !empty($sitecouponTable)) {

			$duration_column = $db->query("SHOW COLUMNS FROM engine4_sitecoupon_coupons LIKE 'duration'")->fetch();
			if (empty($duration_column)) {
				$db->query("ALTER TABLE `engine4_sitecoupon_coupons` ADD `duration` VARCHAR(32) NOT NULL DEFAULT 'once' , ADD `duration_in_months` INT(4) NOT NULL DEFAULT '0' ;");
			}
		}
		//END SITEGATEWAY RELATED WORK
		if (!($this->isGatewayExist('Sitegateway_Plugin_Gateway_Paynow'))) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES ('Paynow', NULL, 0, 'Sitegateway_Plugin_Gateway_Paynow', 0);");
		}
		if (!($this->isGatewayExist('Sitegateway_Plugin_Gateway_MangoPay'))) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES ('MangoPay', NULL, 0, 'Sitegateway_Plugin_Gateway_MangoPay', 0);");
		}
		if (!($this->isGatewayExist('Sitegateway_Plugin_Gateway_Payumoney'))) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES ('Payumoney', NULL, 0, 'Sitegateway_Plugin_Gateway_Payumoney', 0);");
		}
		if (!($this->isGatewayExist('Sitegateway_Plugin_Gateway_Mollie'))) {
			$db->query("INSERT IGNORE INTO `engine4_payment_gateways` (`title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES ('Mollie', NULL, 0, 'Sitegateway_Plugin_Gateway_Mollie', 0);");
		}
		if ( $this->isGatewayExist('Sitegateway_Plugin_Gateway_PayPalAdaptive') ) {
		    $db->query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`plugin` = 'Sitegateway_Plugin_Gateway_PayPalAdaptive';");
		}
		$db->query("UPDATE `engine4_core_modules` SET `title` = 'Advanced Payment Gateways / Stripe Connect', `description` ='Advanced Payment Gateways / Stripe Connect' where name='sitegateway';");
		$db->query("UPDATE `engine4_core_menuitems` SET `label` = 'SEAO - Advanced Payment Gateways / Stripe Connect' where name='core_admin_main_plugins_sitegateway' and module='sitegateway';");

		$isJobExist = $db->select()
			->from('engine4_core_jobtypes')
			->where('type = ?', 'sitegateway_payment_status')
			->limit(1)
			->query()
			->fetchColumn();
		if (empty($isJobExist)) {
			$db->query("INSERT IGNORE INTO `engine4_core_jobtypes`(`title`,`type`,`module`,`plugin`,`enabled`,`priority`,`multi`) values('Sitegateway Payment Status','sitegateway_payment_status','sitegateway','Sitegateway_Plugin_Job_PaymentStatus',1,75,1);");
		}
		$TransactionTable = $db->query('SHOW TABLES LIKE \'engine4_sitegateway_transactions\'')->fetch();
		if (!empty($TransactionTable)) {

			$resourseId = $db->query("SHOW COLUMNS FROM engine4_sitegateway_transactions LIKE 'resource_id'")->fetch();
			if (empty($resourseId)) {
				$db->query("ALTER TABLE `engine4_sitegateway_transactions` ADD `resource_id` INT(11) NOT NULL;");
			}

			$gateway_payment_key = $db->query("SHOW COLUMNS FROM engine4_sitegateway_transactions LIKE 'gateway_payment_key'")->fetch();
			if (empty($resourseId)) {
				$db->query("ALTER TABLE `engine4_sitegateway_transactions` ADD `gateway_payment_key` VARCHAR(255) ;");
			}

			$payout_status = $db->query("SHOW COLUMNS FROM engine4_sitegateway_transactions LIKE 'payout_status'")->fetch();
			if (empty($payout_status)) {
				$db->query("ALTER TABLE `engine4_sitegateway_transactions` ADD `payout_status` VARCHAR(50) NOT NULL AFTER `gateway_payment_key`;");
			}

			$refund_status = $db->query("SHOW COLUMNS FROM engine4_sitegateway_transactions LIKE 'refund_status'")->fetch();
			if (empty($refund_status)) {
				$db->query("ALTER TABLE `engine4_sitegateway_transactions` ADD `refund_status` VARCHAR(50) NOT NULL AFTER `payout_status`;");
			}
		}

		$mobileModuleTable = $db->query('SHOW TABLES LIKE \'engine4_sitemobile_modules\'')->fetch();
		if (!empty($mobileModuleTable)) {
			$siteMobleModuleExist = $db->select()
				->from('engine4_core_modules')
				->where('name = ?', 'sitemobile')
				->where('enabled = ?', 1)
				->limit(1)
				->query()
				->fetchColumn();
			if ($siteMobleModuleExist) {
				$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('sitegateway',1,1,1,1);");
			}
		}
		parent::onInstall();
	}

	function isGatewayExist($plugin) {
		$db = $this->getDb();
		$isGatewayExist = $db->select()
			->from('engine4_payment_gateways')
			->where('plugin = ?', $plugin)
			->limit(1)
			->query()
			->fetchColumn();
		return $isGatewayExist;
	}

	private function _getVersion() {

		$db = $this->getDb();

		$errorMsg = '';
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

		$modArray = array(
			'siteevent' => '4.8.9p6',
			'siteeventticket' => '4.8.9p7',
			'sitereview' => '4.8.9p3',
			'sitereviewlistingtype' => '4.8.9p3',
			'sitereviewpaidlisting' => '4.8.9p3',
			'communityads' => '4.8.9p4',
			'sitepage' => '4.8.9p3',
			'sitegroup' => '4.8.9p3',
			'sitebusiness' => '4.8.9p3',
			'sitestore' => '4.8.9p6',
			'sitecoupon' => '4.8.9p1',
		);

		$finalModules = array();
		foreach ($modArray as $key => $value) {
			$select = new Zend_Db_Select($db);
			$select->from('engine4_core_modules')
				->where('name = ?', "$key")
				->where('enabled = ?', 1);
			$isModEnabled = $select->query()->fetchObject();
			if (!empty($isModEnabled)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_modules', array('title', 'version'))
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
				$getModVersion = $select->query()->fetchObject();

				$isModSupport = $this->checkVersion($getModVersion->version, $value);
				if (empty($isModSupport)) {
					$finalModules[$key] = $getModVersion->title;
				}
			}
		}

		foreach ($finalModules as $modArray) {
			$errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
		}

		return $errorMsg;
	}

	private function checkVersion($databaseVersion, $checkDependancyVersion) {
		$f = $databaseVersion;
		$s = $checkDependancyVersion;
		if (strcasecmp($f, $s) == 0) {
			return -1;
		}

		$fArr = explode(".", $f);
		$sArr = explode('.', $s);
		if (count($fArr) <= count($sArr)) {
			$count = count($fArr);
		} else {
			$count = count($sArr);
		}

		for ($i = 0; $i < $count; $i++) {
			$fValue = $fArr[$i];
			$sValue = $sArr[$i];
			if (is_numeric($fValue) && is_numeric($sValue)) {
				if ($fValue > $sValue) {
					return 1;
				} elseif ($fValue < $sValue) {
					return 0;
				} else {
					if (($i + 1) == $count) {
						return -1;
					} else {
						continue;
					}

				}
			} elseif (is_string($fValue) && is_numeric($sValue)) {
				$fsArr = explode("p", $fValue);

				if ($fsArr[0] > $sValue) {
					return 1;
				} elseif ($fsArr[0] < $sValue) {
					return 0;
				} else {
					return 1;
				}
			} elseif (is_numeric($fValue) && is_string($sValue)) {
				$ssArr = explode("p", $sValue);

				if ($fValue > $ssArr[0]) {
					return 1;
				} elseif ($fValue < $ssArr[0]) {
					return 0;
				} else {
					return 0;
				}
			} elseif (is_string($fValue) && is_string($sValue)) {
				$fsArr = explode("p", $fValue);
				$ssArr = explode("p", $sValue);
				if ($fsArr[0] > $ssArr[0]) {
					return 1;
				} elseif ($fsArr[0] < $ssArr[0]) {
					return 0;
				} else {
					if ($fsArr[1] > $ssArr[1]) {
						return 1;
					} elseif ($fsArr[1] < $ssArr[1]) {
						return 0;
					} else {
						return -1;
					}
				}
			}
		}
	}

}
