<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Api_Core extends Core_Api_Abstract {
    /*
     * Function to return the available profile types
     * 
     * @return array
     */

    public function getProfileTypes() {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        $profileFields = array();
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getElementParams('user');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }
        return $profileFields;
    }

    /*
     * Function to return the available member levels
     *
     * @return array
     */

    public function getMemberlLevels() {
        $levelMultiOptions = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        $publicLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel();
        unset($levelMultiOptions[$publicLevel->level_id]);
        return $levelMultiOptions;
    }

    /*
     * Function to return the default subscription plan
     *
     * @return array
     */

    public function getDefaultSubscriptionPlan() {

        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $packagesTableName = $packagesTable->info('name');

        $package = $packagesTable->fetchRow(array(
            "$packagesTableName.default = ? " => 1,
            "$packagesTableName.enabled = ?" => true,
            "$packagesTableName.price <= ?" => 0,
        ));

        return $package ? $package->getIdentity() : false;
    }

    /*
     * Function to fetch the image from the url returned from google & linkedin
     * 
     * @return image
     */

    public function fetchImage($photo_url, $subject) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $photo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        $tmpfile = APPLICATION_PATH_TMP . DS . md5($photo_url) . '.jpg';
        @file_put_contents($tmpfile, $data);
        $this->_resizeImages($tmpfile, $subject);
    }
    
    public function outlookfetchImage($subject) {
        $service_url = 'https://graph.microsoft.com/beta/me/Photo/$value';
        $curlHeaders = array (
                    'Host: graph.microsoft.com',
                    'Authorization: Bearer '.$_SESSION['outlook_access_token'],    
            );
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$curlHeaders);
        curl_setopt ($ch, CURLOPT_HEADER, false);
        ob_start();
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ob_get_contents();
        ob_end_clean();
        
        if($code == 200){
            $random=rand(0, 10000);
            $tmpfile = APPLICATION_PATH_TMP . DS . md5($service_url.$random) . '_outlook.jpg';
            while(file_exists($tmpfile)) {
                $random=rand(0, 10000);
                $tmpfile = APPLICATION_PATH_TMP . DS . md5($service_url.$random) . '_outlook.jpg';
            }
            str_replace(' ', '+', $data);
            file_put_contents($tmpfile,$data);
            $this->_resizeImages($tmpfile, $subject);
        }else{
            return;
        }
                
    }
    /*
     * Function to make the username
     * 
     * @return string
     */

    public function getUserName($first = '', $last = '') {
        // First attempt with contactinating first & last name
        $username = $first . $last;
        $user = Engine_Api::_()->user()->getUser($username);
        if (!empty($user->user_id)) {
            // Second attempt swaping first & last name
            $username = $last . $first;
            $user = Engine_Api::_()->user()->getUser($username);

            //Third & final attempt with a random string
            if (!empty($user->user_id)) {
                $counter = 0;
                do {
                    $username = $first . $last . rand(0, 10000);
                    $user = Engine_Api::_()->user()->getUser($username);
                    if (!$user) {
                        break;
                    }
                    $counter++;
                } while ($counter < 10);
            }
        }
        $username = str_replace(" ", "", $username);
        return $username;
    }

    public function isSubsciptionStepEnabled() {
        if (!Engine_Api::_()->hasModuleBootstrap('payment'))
            return false;

        $stepTable = Engine_Api::_()->getDbtable('signup', 'user');

        if (Engine_Api::_()->hasModuleBootstrap("sitesubscription")) {
            $stepSelect = $stepTable->select()->where('class = ?', 'Sitesubscription_Plugin_Signup_Subscription');
        } else {
            $stepSelect = $stepTable->select()->where('class = ?', 'Payment_Plugin_Signup_Subscription');
        }
        if (!empty($stepSelect->enable)) {
            $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
            $packagesSelect = $packagesTable
                    ->select()
                    ->from($packagesTable)
                    ->where('enabled = ?', true);

            $packagesObj = $packagesTable->fetchAll($packagesSelect);
            $row = $stepTable->fetchRow($stepSelect);
            return $row->enable || count($packagesObj) > 0;
        }

        return false;
    }

    /*
     * Function to set the image
     * 
     * @return string
     */

    protected function _resizeImages($file, $subject) {
        $name = basename($file);
        $path = dirname($file);
        $storage = Engine_Api::_()->storage();
        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->getIdentity(),
            'name' => $name,
        );

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $name)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $name)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $name)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->photo_id = $iMain->file_id;
        $subject->save();

        return $subject;
    }

    public function getGoogleRedirectUrl() {
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $domainUrl = (_ENGINE_SSL ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'];
        if (isset($baseParentUrl) && !empty($baseParentUrl)) {
            $domainUrl = $domainUrl . $baseParentUrl;
        }
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        return $domainUrl ."/".$params['module']."/".$params['controller']. '/google?google_connected=1';
    }

    public function getRedirectUrl($socialsite) {
        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $domainUrl = (_ENGINE_SSL ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'];
        if (isset($baseParentUrl) && !empty($baseParentUrl)) {
            $domainUrl = $domainUrl . $baseParentUrl;
        }

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        return $domainUrl . "/".$params['module']."/".$params['controller']."/".$socialsite;
    }

    public function facebookIntegrationEnabled() {

        $facebookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook;
        $coreSettings=(array) Engine_Api::_()->getApi('settings', 'core')->core_facebook;

        $client_id = isset($coreSettings['appid']) ? $coreSettings['appid'] : 0;
        $client_secret = isset($coreSettings['secret']) ? $coreSettings['secret'] : 0;
        $loginEnable = isset($facebookSettings['facebookOptions']) ? $facebookSettings['facebookOptions'] : 0;

        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;

        return true;
    }

    public function twitterIntegrationEnabled() {
        $twitterSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_twitter;
        $coreSettings = (array) Engine_Api::_()->getApi('settings', 'core')->core_twitter;
        
        $client_id = isset($coreSettings['key']) ? $coreSettings['key'] : 0;
        $client_secret = isset($coreSettings['secret']) ? $coreSettings['secret'] : 0;
        $loginEnable = isset($twitterSettings['twitterOptions']) ? $twitterSettings['twitterOptions'] : 0;

        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;

        return true;
    }
    
    public function facebookIntegration() {

        $coreSettings=(array) Engine_Api::_()->getApi('settings', 'core')->core_facebook;

        $client_id = isset($coreSettings['appid']) ? $coreSettings['appid'] : 0;
        $client_secret = isset($coreSettings['secret']) ? $coreSettings['secret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;

        return true;
    }

    public function twitterIntegration() {
        $coreSettings = (array) Engine_Api::_()->getApi('settings', 'core')->core_twitter;

        $client_id = isset($coreSettings['key']) ? $coreSettings['key'] : 0;
        $client_secret = isset($coreSettings['secret']) ? $coreSettings['secret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;

        return true;
    }
    
    function getfacebookcount() {
        $userTable=Engine_Api::_()->getDbTable('users', 'user');
        $userTableName=  $userTable->info('name');

        $facebookTable = Engine_Api::_()->getDbTable('facebook', 'user');
        $facebookTableName=  $facebookTable->info('name');
  
        $select = $facebookTable->select()->setIntegrityCheck(false);

        $select->from($facebookTableName , array('COUNT('.$facebookTableName.'.user_id) as count'))
        ->join($userTableName, $userTableName . '.user_id = ' . $facebookTableName . '.user_id AND '.$userTableName . '.password ="" ',array());
        $results = $facebookTable->fetchRow($select);
        return $results->count;
    }
    
    function gettwittercount() {
        $userTable=Engine_Api::_()->getDbTable('users', 'user');
        $userTableName=  $userTable->info('name');

        $twitterTable = Engine_Api::_()->getDbTable('twitter', 'user');
        $twitterTableName=  $twitterTable->info('name');
  
        $select = $twitterTable->select()->setIntegrityCheck(false);

        $select->from($twitterTableName , array('COUNT('.$twitterTableName.'.user_id) as count'))
        ->join($userTableName, $userTableName . '.user_id = ' . $twitterTableName . '.user_id AND '.$userTableName . '.password ="" ',array());
        $results = $twitterTable->fetchRow($select);
        return $results->count;
    }
    
    public function getStats() {
      $facebookresult=$this->getfacebookcount();
      $twitterresult=$this->gettwittercount();
      $flickrresult=Engine_Api::_()->getDbTable('flickr', 'sitelogin')->getCount();
      $googleresult=Engine_Api::_()->getDbTable('google', 'sitelogin')->getCount();
      $instagramresult=Engine_Api::_()->getDbTable('instagram', 'sitelogin')->getCount();
      $linkedinresult=Engine_Api::_()->getDbTable('linkedin', 'sitelogin')->getCount();
      $outlookresult=Engine_Api::_()->getDbTable('outlook', 'sitelogin')->getCount();
      $pinterestresult=Engine_Api::_()->getDbTable('pinterest', 'sitelogin')->getCount();
      $vkresult=Engine_Api::_()->getDbTable('vk', 'sitelogin')->getCount();
      $yahooresult=Engine_Api::_()->getDbTable('yahoo', 'sitelogin')->getCount();
      
      $data=array('facebook'=>$facebookresult,
          'twitter'=>$twitterresult,
          'linkedin'=>$linkedinresult,
          'google'=>$googleresult,
          'pinterest'=>$pinterestresult,
          'flickr'=>$flickrresult,
          'instagram'=>$instagramresult,
          'outlook'=>$outlookresult,
          'vk'=>$vkresult,
          'yahoo'=>$yahooresult);
      $total=0;
      foreach ($data as $key=>$value){
          $total=$total+$value;
      }
      $result=array('total'=>$total,'data'=>$data);
      return $result;
    }
}
