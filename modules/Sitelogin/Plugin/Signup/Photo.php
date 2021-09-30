<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Photo.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Plugin_Signup_Photo extends User_Plugin_Signup_Photo {
    protected $_adminFormClass = 'Sitelogin_Form_Admin_Signup_Photo';
    public function onView() {
        parent::onView();
        if (!empty($_SESSION['google_signup'])) {
            try {
                $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
                $authUrl = $googleTable->getGoogleInstance();
                if (!empty($authUrl)) {
                    // Fetch image from Google
                    $photo_url = $authUrl->picture;
                    $this->_fetchImage($photo_url);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['linkedin_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->linkedinIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token'])) {
                        $userDetails = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->fetch();
                    }
                    //GetLinkedin Image
                    if (isset($userDetails->pictureUrls) && !empty($userDetails->pictureUrls)) {
                        $originalImageUrls = get_object_vars($userDetails->pictureUrls);
                        if (!empty($originalImageUrls)) {
                            $image = isset($originalImageUrls['values'][0]) ? $originalImageUrls['values'][0] : 0;
                        }
                    }
                    // Fetch image from Linkedin
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['instagram_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->data;
                    }
                    //instagram Image
                    if (isset($userDetails->profile_picture) && !empty($userDetails->profile_picture)) {
                            $image = isset($userDetails->profile_picture) ? $userDetails->profile_picture : 0;
                    }
                    // Fetch image from instagram
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['pinterest_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->pinterestIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['pinterest_access_token']) && !empty($_SESSION['pinterest_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->data;
                    }
                    //pinterest Image
                    if (isset($userDetails->image) && !empty($userDetails->image)) {
                        $originalImageUrls = get_object_vars($userDetails->image);
                        if (!empty($originalImageUrls)) {
                            $image = isset($originalImageUrls['60x60']->url) ? $originalImageUrls['60x60']->url : 0;
                        }
                    }
                    // Fetch image from pinterest
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['yahoo_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->yahooIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['yahoo_access_token']) && !empty($_SESSION['yahoo_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->profile;
                    }
                    //yahoo Image
                    if (isset($userDetails->image) && !empty($userDetails->image)) {
                        $originalImageUrls = get_object_vars($userDetails->image);
                        if (!empty($originalImageUrls)) {
                            $image = isset($originalImageUrls['imageUrl']) ? $originalImageUrls['imageUrl'] : 0;
                        }
                    }
                    // Fetch image from yahoo
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['vk_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('vk', 'sitelogin')->vkIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->response[0];
                    }
                    //vk Image
                    if (isset($userDetails->photo_50) && !empty($userDetails->photo_50)) {
                            $image = isset($userDetails->photo_50) ? $userDetails->photo_50 : 0;
                    }
                    // Fetch image from vk
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['outlook_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->outlookIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token'])) {
                       $this->_outlookfetchImage();
                    }
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        if (!empty($_SESSION['flickr_signup'])) {
            try {
                $loginEnable = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->flickrIntegrationEnabled();
                if (!empty($loginEnable)) {
                    if (isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token'])) {
                        $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();
                    }
                    //flickr Image
                    if (isset($userDetails['photoUrl']) && !empty($userDetails['photoUrl'])) {
                            $image = isset($userDetails['photoUrl']) ? $userDetails['photoUrl'] : 0;
                    }
                    // Fetch image from flickr
                    if (isset($image) && !empty($image))
                        $this->_fetchImage($image);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
    }
    public function onAdminProcess($form) {
        $step_table = Engine_Api::_()->getDbtable('signup', 'user');
        $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Sitelogin_Plugin_Signup_Photo'));
        $step_row->enable = $form->getValue('enable');
        $step_row->save();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $values = $form->getValues();
        $settings->user_signup_photo = $values['require_photo'];
        $form->addNotice('Your changes have been saved.');
    }
    protected function _outlookfetchImage()
    {
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
            $this->_resizeImages($tmpfile);
        }else{
            return;
        }
    }
}