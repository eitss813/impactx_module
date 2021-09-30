<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Api_Core extends Core_Api_Abstract
{

  private $_otpLog;

  public function downloadFiles($service, $extractDir = '')
  {
    $services = array(
      'amazon' => 'aHR0cDovL3NvY2lhbGVuZ2luZWFkZG9ucy5jb20vU29jaWFsRW5naW5lL1NvY2lhbGVuZ2luZU1vZHVsZXMvc2l0ZW90cHZlcmlmaWVyX2xpYnJhcmllcy9zdG9yYWdlL2xpYnJhcnktc2Vhby1hd3MtNC45LjQudGFy',
      'twilio' => 'aHR0cDovL3NvY2lhbGVuZ2luZWFkZG9ucy5jb20vU29jaWFsRW5naW5lL1NvY2lhbGVuZ2luZU1vZHVsZXMvc2l0ZW90cHZlcmlmaWVyX2xpYnJhcmllcy9zdG9yYWdlL2xpYnJhcnktc2Vhby10d2lsaW8tNC45LjQudGFy'
    );
    if( empty($services[$service]) ) {
      return false;
    }
    $fileUrl = base64_decode($services[$service]);
    set_time_limit(0);
    $urlParts = explode('/', trim(parse_url($fileUrl, PHP_URL_PATH), '/'));
    $newfilename = end($urlParts);
    $local_path = str_replace('/', DS, APPLICATION_PATH . '/temporary/package/archives/');
    $extractDir = str_replace('/', DS, APPLICATION_PATH . '/temporary/package/packages');
    $path = $local_path . $newfilename;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fileUrl);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    ob_start();
    $result = curl_exec($ch);
    if( empty($result) ) {
      $result = file_get_contents($fileUrl);
    }
    curl_close($ch);
    ob_end_clean();
    $status = false;
    if( !empty($result) && !strstr(substr($result, 0, 50), 'error') ) {

      $file = fopen($path, 'wb');
      chmod($path, 0777);
      $result = fwrite($file, $result);
      fclose($file);
      $status = true;
    }
    if( $status ) {
      // Try to deflate archive?
      $extractFiles = array($path);
      $packagesInfo = array();
      $toRemove = array();
      try {
        while( count($extractFiles) > 0 ) {
          $current = array_shift($extractFiles);
          // Try to extract
          $outputPath = Engine_Package_Archive::inflate($current, $extractDir);
          // Add to remove after extraction
          $toRemove[] = $outputPath;
          @unlink($current);
        }
      } catch( Exception $e ) {
        $error = $e->getMessage();
        return false;
      }
      $data = file_get_contents($outputPath . DS . 'package.json');
      $data = json_decode($data, true);
      $vfsAdapter = 'system';
      $vfsConfig = array(
        'path' => APPLICATION_PATH,
      );
      $vfs = Engine_Vfs::factory($vfsAdapter, $vfsConfig);
      foreach( $data['structure'] as $structure ) {
        $filesInfo = $structure['structure'];
        $desPath = $structure['path'];
        $paths = explode('/', $desPath);
        $dP = '';
        foreach( $paths as $dirName ) {
          $directory = $vfs->path($dP . $dirName);
          // Already a directory
          if( !$vfs->isDirectory($directory) ) {
            $vfs->makeDirectory($directory, true);
            $vfs->mode($directory, 0777);
          }
          $dP .= $dirName . '/';
        }

        foreach( $filesInfo as $info ) {
          if( $info['path'] == 'manifest.php' ) {
            continue;
          }
          $fP = $desPath . DS . $info['path'];
          if( !empty($info['dir']) ) {
            $vfs->makeDirectory($fP, true);
            $vfs->mode($fP, $info['perms']);
          } else {
            $vfs->makeDirectory(dirname($fP), true);
            $vfs->put($fP, $outputPath . DS . $fP);
            $vfs->mode($fP, $info['perms']);
          }
        }
      }
      // Remove to remove
      foreach( $toRemove as $removeFile ) {
        if( is_dir($removeFile) ) {
          try {
            Engine_Package_Utilities::fsRmdirRecursive($removeFile, true);
          } catch( Exception $e ) {
            
          }
        } elseif( is_file($removeFile) ) {
          @unlink($removeFile);
        }
      }
    }

    return $status;
  }

  public function generateCode()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $codelength = $settings->getSetting('siteotpverifier.length', 4);
    $codetype = $settings->getSetting('siteotpverifier.type', 0);

    do {
      if( empty($codetype) ) {
        $maxNbrStr = str_repeat('9', $codelength);
        $maxNbr = intval($maxNbrStr);
        $n = mt_rand(0, $maxNbr);
        $code = str_pad($n, $codelength, "0", STR_PAD_LEFT);
      } else {
        $code = substr(md5(microtime()), 1, $codelength);
      }
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
      $forgotSelect = $forgotTable->select()
        ->where('code = ?', $code);
      $forgotRow = $forgotTable->fetchRow($forgotSelect);
    } while( !empty($forgotRow) );

    return $code;
  }

  public function genrateMessage($type, $code, $user = null)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $nativelangauge = $settings->getSetting('siteotpverifier.nativelangauge', 0);
    if( !empty($nativelangauge) ) {
      $language = Zend_Registry::get('Locale')->getLanguage();
    } else {
      $language = $settings->getSetting('core.locale.locale', 'en');
    }
    $websiteName = $settings->getSetting('core.general.site.title', '');
    $userName = $user ? $user->getTitle() : '';
    $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');
    $select = $messageTable->select()->where('language=?', $language);
    $param = $messageTable->fetchRow($select);
    if( empty($param) ) {
      $select = $messageTable->select()->where('language=?', 'en');
      $param = $messageTable->fetchRow($select);
    }
    if( !empty($param[$type]) && strpos($param[$type], '[code]') !== false ) {
      $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
      $timestring = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);
      $message = str_replace(array("[code]", "[expirytime]", "[website_name]", "[username]"), array($code, $timestring, $websiteName, $userName), $param[$type]);
    } else {
      $message = sprintf('Your code for OTP verification is %s.', $code);
    }
    return $message;
  }

  public function sendOtpCode($user, $code, $type)
  {
    if ($user instanceof User_Model_User) {
      $user = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }
    if( !$user || !$user->getIdentity() || empty($user->phoneno) ) {
      return 0;
    }
    $message = $this->genrateMessage($type, $code, $user);
    $ccode = empty($user->country_code) ? '+1' : $user->country_code;
    $phone = $ccode . $user->phoneno;
    return $this->sendOTPMessage($phone, $message, $type);
  }

  public function sendOtpCodeWitoutUser($phone, $code, $type)
  {
    if( empty($phone) || empty($code) ) {
      return 0;
    }
    $message = $this->genrateMessage($type, $code);
    return $this->sendOTPMessage($phone, $message, $type);
  }

  public function verifyMobileNo($phone, $code)
  {

    if( empty($phone) || empty($code) ) {
      return 0;
    }
    $type = 'signup';
    $message = $this->genrateMessage($type, $code);
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    if( !empty($otpverifySession->otp_code) ) {
      $otpverifySession->otp_code = null;
    }
    $status = $this->sendOTPMessage($phone, $message, $type);
    if( $status ) {
      $otpverifySession->otp_code = $code;
      $otpverifySession->time = time();
    }
    return $status;
  }

  public function sendOTPMessage($phone, $message, $type, $user = null)
  {
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    if( $service == "amazon" ) {
      $status = $this->sendViaAmazon($phone, $message);
    } elseif( $service == "twilio" ) {
      $status = $this->sendViaTwilo($phone, $message);
    } elseif( $service == "testmode" ) {
      $status = $this->saveMessageInLog($phone, $message, $type);
    }
    if( $status ) {
      $statistictable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
      $statistictable->insert(array(
        'type' => $type, //'admin_sent',
        'creation_date' => date('Y-m-d H:i:s'),
        'service' => $service,
        'user_id' => $user ? $user->getIdentity() : 0,
      ));
    }
    return $status;
  }

  public function saveMessageInLog($phone, $message, $type = null)
  {
    $otpMessageSession = new Zend_Session_Namespace('Siteotpverifier_OTP_MESSAGE');
    $smsList[] = array(
      'time' => date('Y-m-d H:i:s'),
      'phone' => $phone,
      'message' => $message
    );
    if( !empty($otpMessageSession->smsOTPInfo) && count($otpMessageSession->smsOTPInfo) > 0 ) {
      foreach( $otpMessageSession->smsOTPInfo as $key => $sms ) {
        if( $key > 9 ) {
          break;
        }
        $smsList[] = $sms;
      }
    }
    $otpMessageSession->smsOTPInfo = $smsList;
    $this->getLog()->log(sprintf('Message(%s) [%s]: %s', $phone, $type, $message), Zend_Log::INFO);
    return true;
  }

  public function getLog()
  {
    if( null === $this->_otpLog ) {
      $log = new Zend_Log();
      $log->setEventItem('domain', 'siteotp');
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/siteotp.log'));
      } catch( Exception $e ) {
        $log->addWriter(new Zend_Log_Writer_Null());
      }
      $this->_otpLog = $log;
    }
    return $this->_otpLog;
  }

  public function sendViaAmazon($phone, $message)
  {
    if( !$this->amazonIntegrationEnabled() ) {
      return 0;
    }
    $amazonSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon;
    $client_id = $amazonSettings['clientId'];
    $client_secret = $amazonSettings['clientSecret'];
    require_once APPLICATION_PATH . '/application/libraries/SEAO/aws/aws-autoloader.php';
    try {
      $client = new \Aws\Sns\SnsClient([
        'version' => 'latest',
        'region' => 'us-west-2',
        'credentials' => [
          'key' => $client_id,
          'secret' => $client_secret,
        ],
      ]);
      $array = array('attributes' => array('DefaultSenderID' => 'test', 'DefaultSMSType' => 'Transactional'));
      $client->setSMSAttributes($array);
      $client->publish([
        'Message' => $message, // REQUIRED
        'PhoneNumber' => $phone,
        'Subject' => 'Test',
      ]);
      return true;
    } catch( Exceptions $e ) {
      return false;
    }
  }

  public function sendViaTwilo($phone, $message)
  {
    if( !$this->twilioIntegrationEnabled() ) {
      return 0;
    }
    require_once APPLICATION_PATH . '/application/libraries/SEAO/Twilio/autoload.php';
    $twilioSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio;
    $sid = $twilioSettings['accountsid'];
    $token = $twilioSettings['apikey'];
    $clientphone_no = $twilioSettings['phoneno'];
    try {
      $client = new \Twilio\Rest\Client($sid, $token);

      // Use the client to do fun stuff like send text messages!
      $client->messages->create(
        // the number you'd like to send the message to
        $phone, array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' => $clientphone_no,
        // the body of the text message you'd like to send
        'body' => $message
        )
      );
      return true;
    } catch( Exceptions $e ) {
      return false;
    }
  }

  public function amazonIntegrationEnabled()
  {

    $amazonSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon;
    $client_id = isset($amazonSettings['clientId']) ? $amazonSettings['clientId'] : 0;
    $client_secret = isset($amazonSettings['clientSecret']) ? $amazonSettings['clientSecret'] : 0;

    return !empty($client_id) && !empty($client_secret);
  }

  public function twilioIntegrationEnabled()
  {

    $twilioSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio;
    $client_id = isset($twilioSettings['accountsid']) ? $twilioSettings['accountsid'] : 0;
    $client_secret = isset($twilioSettings['apikey']) ? $twilioSettings['apikey'] : 0;
    $phone_no = isset($twilioSettings['phoneno']) ? $twilioSettings['phoneno'] : 0;

    return !empty($client_id) && !empty($client_secret) && !empty($phone_no);
  }

  public function countryCode()
  {
    $allowedCountrycode = $GLOBALS['countryCodes'];
    $countryCode = Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_allowCountry;

    if( !empty($countryCode) ) {
      $allowedCountrycodes = array();
      foreach( $countryCode as $value ) {
        $allowedCountrycodes[$value] = $GLOBALS['countryCodes'][$value];
      }
      if( !empty($allowedCountrycodes) )
        $allowedCountrycode = $allowedCountrycodes;
    }
    return $allowedCountrycode;
  }

  public function sendmessage($user, $message)
  {
    if ($user instanceof User_Model_User) {
      $user = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }
    if( !$user || !$user->getIdentity() || empty($user->phoneno) ) {
      return 0;
    }

    $ccode = empty($user->country_code) ? '+1' : $user->country_code;
    $phone = $ccode . $user->phoneno;
    return $this->sendOTPMessage($phone, $message, 'admin_sent', $user);
  }

  public function enabledOTPClient()
  {
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    $status = false;
    if( $service == "amazon" ) {
      $status = $this->amazonIntegrationEnabled();
    } elseif( $service == "twilio" ) {
      $status = $this->twilioIntegrationEnabled();
    } elseif( $service == "testmode" ) {
      $status = true;
    }
    return $status;
  }

  public function convertTime($param)
  {
    if( empty($param) )
      return;
    $hours = floor($param / 3600);
    $minutes = floor(($param / 60) % 60);
    $seconds = $param % 60;
    $result = '';
    if( $hours != 0 ) {
      $result = $hours . (($hours > 1) ? " hours" : " hour");
    }
    if( $minutes != 0 ) {
      $result .= $minutes . (($minutes > 1) ? " minutes" : " minute");
    }
    if( $seconds != 0 ) {
      $result .= $seconds . (($seconds > 1) ? " seconds" : " second");
    }

    return $result;
  }

  public function isSiteMobileModeEnabled() {
    return $this->checkSitemobileMode('tablet-mode') || $this->checkSitemobileMode('mobile-mode');
  }

  public function checkSitemobileMode($mode = 'fullsite-mode') {
    if (Engine_Api::_()->hasModuleBootstrap('sitemobile')) {
      return (bool) (Engine_API::_()->sitemobile()->getViewMode() === $mode);
    } else {
      return (bool) ('fullsite-mode' === $mode);
    }
  }

  // RETURN DEFAULT COUNTRY AS PER THE DEFAULT COUNTRY SETTING & AUTO COUNTRY SELECTION
  public function getDefaultCountry() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $autoSelection = $settings->getSetting('siteotpverifier.autoCountrySelection', true);
    $ipApi = Engine_Api::_()->getApi('ip', 'siteotpverifier');
    if ($autoSelection && $ipApi->isValid()) {
      return $ipApi->getCountryCode();
    }
    return $settings->getSetting('siteotpverifier.defaultCountry', '+1');
  }
}
