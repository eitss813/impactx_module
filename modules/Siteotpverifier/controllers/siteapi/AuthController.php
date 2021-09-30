<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AuthController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_AuthController extends Siteapi_Controller_Action_Standard {

    public function verifyMobilenoAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Check post
        $phoneNo = $this->_getParam('phoneno', null);
        $email = $this->_getParam('emailaddress', null);
        $country_code = $this->_getParam('country_code');

        if (empty($phoneNo) && empty($email)) {
            $bodyParams['response']['isOtpSend'] = false;
        }

        if (preg_match("/^([1-9][0-9]{4,15})$/", $phoneNo) || preg_match("/^([1-9][0-9]{4,15})$/", $email)) {
            $phoneNo = preg_match("/^([1-9][0-9]{4,15})$/", $phoneNo) ? $phoneNo : $email;
        } else {
            $bodyParams['response']['isOtpSend'] = false;
            $this->respondWithSuccess($bodyParams);
        }

        if (empty($phoneNo) || empty($country_code)) {
            $this->respondWithError('no_record');
        }
        
        if (!strstr($country_code, '+')) {
            $country_code = '+' . preg_replace('/\s+/', '', $country_code);
            $phoneno = $country_code . $phoneNo;
        } else {
            $phoneno = $country_code . $phoneNo;
        }

        $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
        $sqlquery = $userTable->select()
                ->from($userTable->info('name'), array('user_id'))
                ->where('phoneno = ?', $phoneNo);
        $userAdded = $userTable->fetchRow($sqlquery);

        if (!empty($userAdded)) {

            $this->respondWithError('unauthorized', "Someone is already registered with this Phone Number. Please try with another number.");
        }
        
        try {
            $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
            $status = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->verifyMobileNo($phoneno, $code);

            if (!empty($status)) {
                $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
                $min_creation_date = time() + ($expirytime);
                $otpverify['code'] = $code;
                $otpverify['sent_time'] = date("Y-m-d H:i:s");
                $otpverify['expairy_time'] = date("Y-m-d H:i:s", $min_creation_date);
                $otpverify['expairyTime'] = $min_creation_date;
                $otpverify['country_code'] = $country_code;
                $otpverify['phoneno'] = $phoneNo;
                $otpverify['type'] = $settings->getSetting('siteotpverifier.type');
                $otpverify['length'] = $settings->getSetting('siteotpverifier.length');
                $otpverify['timezone'] = date_default_timezone_get();
                $bodyParams['response'] = $otpverify;
                $bodyParams['response']['isOtpSend'] = true;
                $duration = round($expirytime / 60);
                $bodyParams['response']['duration'] = $expirytime;
                if ($duration >= 1) {
                    $time_text = "minute(s)";
                } else {
                    $duration = $expirytime;
                    $time_text = "seconds";
                }
                $bodyParams['otpMessage'] = "Note: OTP is valid for " . $duration . " " . $time_text;

                $this->respondWithSuccess($bodyParams);
            } else {
                $this->respondWithError('unauthorized', 'Unable to process this request');
            }
        } catch (Exception $ex) {
           $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    public function sendAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->respondWithError('unauthorized', 'You are already signed in.');
        }
        $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
        $phone_no = $this->_getparam('email');
        $password = $this->_getparam('password');
        $loginWithOtp = $this->_getparam('loginWithOtp', null);
        if (empty($phone_no))
            $this->respondWithError('unauthorized', 'Enter a valid Email Address or Phone Number');
        if (preg_match("/^([1-9][0-9]{4,15})$/", $phone_no)) {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
                    ->fetchRow(array('phoneno = ?' => $phone_no));
            $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id) : null;
            $phoneno = $phone_no;
            $email = $user ? $user->email : null;
        } else {
            $user = Engine_Api::_()->getDbtable('users', 'user')
                    ->fetchRow(array('email = ?' => $phone_no));
            $email = $user ? $user->email : $phone_no;
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        if (!$user || !$user->getIdentity()) {
            $this->respondWithError('no_record');

            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'no-member',
            ));
            return;
        }
        $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');

        if ($loginoption != 'both') {
            $isValidPassword = Engine_Api::_()->user()->checkCredential($user->getIdentity(), $password);
            if (empty($isValidPassword)) {
                $this->respondWithError('unauthorized', 'Email/Phone Number or Password is not valid.');
            }
        }
        if (!$user->enabled) {
            if (!$user->verified) {
                $this->respondWithError('email_not_verified');
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            } else if (!$user->approved) {
                $this->respondWithError('not_approved');

                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                return;
            }
        }
        try {
            $loginAllowed = Engine_Api::_()->authorization()->getPermission($user->level_id, 'Siteotpverifier_level', 'login');
            $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
            $bodyParams['enable_verification'] = 0;
            if (!empty($otpUser))
                $bodyParams['enable_verification'] = $otpUser->enable_verification;
            $bodyParams['loginoption'] = $loginoption;
            $response['response'] = $bodyParams;
            if (($loginoption == 'otp' && (empty($otpUser) || empty($otpUser->enable_verification) || empty($otpUser->phoneno))) || $loginoption == 'default') {

                $bodyParams['otpsent'] = 0;
                $this->_forward('login', 'auth', 'user', array(
                    'email' => $user->email,
                    'password' => $password,
                ));
                return;
            } else {
                $bodyParams['otpsent'] = 1;
            }

            if ($loginoption == 'both' && (empty($otpUser) || empty($otpUser->phoneno))) {
                $this->respondWithError('unauthorized', 'You have not registered phone no');
            }
            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'user_id' => $user->getIdentity(),
                'email' => $email,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'otpNotVerified',
            ));


            $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
            $type = 'login';
            // genrate OTP code codes
            $response = $forgotTable->createCode($type, $user);
            if (!empty($response['error'])) {
                $error = $response['error'];
                $this->respondWithError('unauthorized', $error);
            }
            $code = $response['code'];
            $response['type'] = $settings->getSetting('siteotpverifier.type');
            $response['length'] = $settings->getSetting('siteotpverifier.length');
            $response['phoneno'] = $otpUser->phoneno;
            $response['country_code'] = $otpUser->country_code;
            $response['duration'] = $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
            $duration = round($expirytime / 60);
            if ($duration >= 1) {
                $time_text = "minute(s)";
            } else {
                $duration = $expirytime;
                $time_text = "seconds";
            }
            $response['otpMessage'] = "Note: OTP is valid for " . $duration . " " . $time_text;
            //code for sending code to phone no.
            $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($user, $code, $type);

            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    public function enableVerificationAction() {
        //enable disbale modules 
        $user = Engine_Api::_()->user()->getViewer();
        if (!$user || !$user->getIdentity()) {
            $this->respondWithError('no_record');
        }
        $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        $enable_verification = $this->_getParam('enable_verification');
        $otpUser->enable_verification = $enable_verification;
        $otpUser->save();
        $this->successResponseNoContent('no_content');
    }

    public function addMobilenoAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Can specifiy custom id
        $id = $this->_getParam('user_id', null);
        $subject = null;
        if (null === $id) {
            $subject = Engine_Api::_()->user()->getViewer();
            Engine_Api::_()->core()->setSubject($subject);
        } else {
            $subject = Engine_Api::_()->getItem('user', $id);
            Engine_Api::_()->core()->setSubject($subject);
        }
        // Set up require's
        $this->_helper->requireUser();
        $this->_helper->requireSubject();
        if ($this->_helper->requireAuth()->setAuthParams(
                        $subject, null, 'edit'
                )) {
            
        }
        $user = $subject;
        if (empty($user)) {
            $this->respondWithError('no_record');
        }

        try {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
            if ($this->getRequest()->isGet()) {
                $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');

                if (empty($otpUser) || empty($otpUser->phoneno)) {
                    $countrycodes = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->getCountryCode();
                    $accountForm[] = array(
                        'type' => 'Select',
                        'name' => 'country_code',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country/Region'),
                        'multiOptions' => $countrycodes,
                        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry', '+1'),
                    );
                    $accountForm[] = array(
                        'type' => 'Text',
                        'name' => 'mobileno',
                        'label' => $this->translate('Add Mobile No'),
                        'hasValidator' => true
                    );
                    $accountForm[] = array(
                        'type' => 'Submit',
                        'name' => 'submit',
                        'label' => $this->translate('Save'),
                    );

                    $bodyParams['form'] = $accountForm;
                    //$bodyParams['response']['loginoption'] = $loginoption;

                    $this->respondWithSuccess($bodyParams);
                } else {
                    $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                    $tempUser['country_code'] = $otpUser->country_code;
                    $tempUser['phoneno'] = $otpUser->phoneno;
                    $tempUser['enable_verification'] = $otpUser->enable_verification;
                    $bodyParams['response'] = $tempUser;
                    $bodyParams['menu'] = $this->_gutterMenus($user);
                    $this->respondWithSuccess($bodyParams);
                }
            } else {
                if (empty($otpUser) || empty($otpUser->phoneno)) {

                    $values = $_REQUEST;
                    $countrycode = $values['country_code'];
                    if (!strstr($countrycode, '+')) {
                        $countrycode = '+' . preg_replace('/\s+/', '', $countrycode);
                    }
                    $countrycodes = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->getCountryCode();
                    if (!isset($countrycodes[$countrycode]) || empty($countrycodes[$countrycode])) {
                        $this->respondWithError('unauthorized', 'Country code is not valid or not allowed by admin.');
                    }
                    //........................................

                    $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
                    $sqlquery = $userTable->select()
                            ->from($userTable->info('name'), array('user_id'))
                            ->where('phoneno = ?', $values['mobileno']);
                    $userAdded = $userTable->fetchRow($sqlquery);

                    if (!empty($userAdded)) {

                        $this->respondWithError('unauthorized', "Someone is already registered with this Phone Number. Please try with another number.");
                    }
                    $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
                    $mobileTable->delete(array(
                        'user_id = ?' => $user->getIdentity(),
                    ));
                    $mobileTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'phoneno' => $values['mobileno'],
                        'country_code' => $countrycode,
                        'creation_date' => date('Y-m-d H:i:s'),
                    ));

                    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
                    $type = 'add';
                    $response = $forgotTable->createCode($type, $user);
                    if (!empty($response['error'])) {
                        $error = $response['error'];
                        $this->respondWithError('unauthorized', $error);
                    }
                    $code = $response['code'];
                    //code for sending code to phone no.
                    $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCodeWitoutUser($countrycode . $values['mobileno'], $code, $type);
                    //$this->successResponseNoContent('no_content');
                    $bodyParams['response']['code'] = $code;
                    $bodyParams['response']['type'] = $settings->getSetting('siteotpverifier.type');
                    $bodyParams['response']['length'] = $settings->getSetting('siteotpverifier.length');
                    $bodyParams['response']['phoneno'] = $values['mobileno'];
                    $bodyParams['response']['country_code'] = $countrycode;
                    $bodyParams['response']['duration'] = $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
                    $duration = round($expirytime / 60);
                    if ($duration >= 1) {
                        $time_text = "minute(s)";
                    } else {
                        $duration = $expirytime;
                        $time_text = "seconds";
                    }
                    $bodyParams['otpMessage'] = "Note: OTP is valid for " . $duration . " " . $time_text;

                    $this->respondWithSuccess($bodyParams);
                }
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    public function editMobilenoAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $user = Engine_Api::_()->user()->getViewer();
        if (!$user || !$user->getIdentity()) {
            //
        }

        $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        if ($this->getRequest()->isGet()) {
            $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');

            $countrycodes = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->getCountryCode();
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'country_code',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country/Region'),
                'multiOptions' => $countrycodes,
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry', '+1'),
            );
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'mobileno',
                'label' => $this->translate('Edit Mobile No'),
                'hasValidator' => true
            );
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => $this->translate('Save'),
            );
            try {
                $bodyParams['form'] = $accountForm;
                //$bodyParams['formValues'] = $user->toArray();
                $bodyParams['formValues']['mobileno'] = $otpUser->phoneno;
                $bodyParams['formValues']['country_code'] = $otpUser->country_code;
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            //$bodyParams['response']['loginoption'] = $loginoption;

            $this->respondWithSuccess($bodyParams);
        } else {
            $values = $_REQUEST;
            $countrycode = $values['country_code'];
            if (!strstr($countrycode, '+')) {
                $countrycode = '+' . preg_replace('/\s+/', '', $countrycode);
            }

            //country code validation.................
            $countrycodes = Engine_Api::_()->getApi('core', 'siteotpverifier')->countryCode();
            $countrycodes = array_keys($countrycodes);
            $countrycodes = array_combine($countrycodes, $countrycodes);

            if (!isset($countrycodes[$countrycode]) || empty($countrycodes[$countrycode])) {
                $this->respondWithError('unauthorized', 'Country code is not valid or not allowed by admin.');
            }
            //........................................

            $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
            $sqlquery = $userTable->select()
                    ->from($userTable->info('name'), array('user_id'))
                    ->where('phoneno = ?', $values['mobileno']);
            $userExist = $userTable->fetchRow($sqlquery);
            if (!empty($userExist)) {
                $this->respondWithError('unauthorized', "Someone is already registered with this Phone Number. Please try with another number.");
            }
            $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
            $mobileTable->delete(array(
                'user_id = ?' => $user->getIdentity(),
            ));
            $mobileTable->insert(array(
                'user_id' => $user->getIdentity(),
                'phoneno' => $values['mobileno'],
                'country_code' => $countrycode,
                'creation_date' => date('Y-m-d H:i:s'),
            ));
            $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
            $type = 'edit';
            // genrate OTP code codes
            $response = $forgotTable->createCode($type, $user);
            if (!empty($response['error'])) {
                $error = $response['error'];
                $this->respondWithError('unauthorized', $error);
            }
            $code = $response['code'];
            $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCodeWitoutUser($countrycode . $values['mobileno'], $code, $type);

            //$this->successResponseNoContent('no_content');
            $bodyParams['response']['code'] = $code;
            $bodyParams['response']['type'] = $settings->getSetting('siteotpverifier.type');
            $bodyParams['response']['length'] = $settings->getSetting('siteotpverifier.length');
            $bodyParams['response']['phoneno'] = $values['mobileno'];
            $bodyParams['response']['country_code'] = $countrycode;
            $bodyParams['response']['duration'] = $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
            $duration = round($expirytime / 60);
            if ($duration >= 1) {
                $time_text = "minute(s)";
            } else {
                $duration = $expirytime;
                $time_text = "seconds";
            }
            $bodyParams['otpMessage'] = "Note: OTP is valid for " . $duration . " " . $time_text;

            $this->respondWithSuccess($bodyParams);
        }
    }

    public function codeVerificationAction() {
        $type = $this->_getParam('type', 'edit');

        // Check for empty params
        $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');

        $user = Engine_Api::_()->user()->getViewer();
        if (!$user || !$user->getIdentity()) {
            $this->respondWithError('no_record');
        }
        $user_id = $user->getIdentity();


        $values = $_REQUEST;
        //if(!isset($values['verify_submit'])){return;}
        // Check code
        $forgotSelect = $forgotTable->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('code = ?', $values['code'])
                ->where('type = ?', $type);
        //echo $forgotSelect;
        $forgotRow = $forgotTable->fetchRow($forgotSelect);

        if (!$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity()) {
            $this->respondWithError('unauthorized', "Invalid OTP. Please try again.");
            return;
        }
        $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
        // Code expired
        // Note: Let's set the current timeout for 10 minutes for now
        $min_creation_date = time() - ($expiaryTime);
        if (strtotime($forgotRow->creation_date) < $min_creation_date) { // @todo The strtotime might not work exactly right
            $this->respondWithError('unauthorized', "OTP has been expired. Please resend the OTP.");
            return;
        }
        $forgotTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
            'type = ?' => $type
        ));
        $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
        $mobileSelect = $mobileTable->select()
                ->where('user_id = ?', $user->getIdentity());
        $mobileRow = $mobileTable->fetchRow($mobileSelect);
        $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        $otpUser->country_code = $mobileRow->country_code;
        $otpUser->phoneno = $mobileRow->phoneno;
        $otpUser->save();
        $mobileTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
        ));

        $this->successResponseNoContent('no_content');
    }

    private function _gutterMenus($user) {

        if ($user->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Edit Mobile No'),
                'name' => 'edit_mobile',
                'url' => 'otpverifier/edit-mobileno',
                'urlParams' => array(
                    "user_id" => $user->getIdentity(),
                )
            );

            $menus[] = array(
                'label' => $this->translate('Delete Mobile No'),
                'name' => 'delete',
                'url' => 'otpverifier/delete-mobileno',
                'urlParams' => array(
                    "user_id" => $user->getIdentity(),
                )
            );
        }
        return $menus;
    }

    public function deleteMobilenoAction() {
        $this->validateRequestMethod('DELETE');
        $user = Engine_Api::_()->user()->getViewer();
        if (!$user || !$user->getIdentity()) {
            $this->respondWithError('no_record');
        }
        // Check post
        $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
        $otpUser->phoneno = 0;
        $otpUser->save();
        $this->successResponseNoContent('no_content');
    }

    public function forgotPasswordAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        $values = $_REQUEST;
        if (!isset($values['email']) || empty($values['email'])) {
            $this->respondWithError('unauthorized', "Please enter phone number or email address.");
        }
        $user = Engine_Api::_()->getDbtable('users', 'user')
                ->fetchRow(array('email = ?' => $values['email']));
        if (!$user || !$user->getIdentity()) {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
                    ->fetchRow(array('phoneno = ?' => $values['email']));
            $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id) : null;
            $searchType = 'phoneno';
            if (!$user || !$user->getIdentity()) {
                $this->respondWithError('unauthorized', "A user account with this email or phone number was not found.");
            }
        } else {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        }

        if (empty($otpUser->phoneno)) {

            $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
            $db = $forgotTable->getAdapter();
            $db->beginTransaction();
            // Delete any existing reset password codes
            $forgotTable->delete(array(
                'user_id = ?' => $user->getIdentity(),
            ));

            // Create a new reset password code
            $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
            $forgotTable->insert(array(
                'user_id' => $user->getIdentity(),
                'code' => $code,
                'creation_date' => date('Y-m-d H:i:s'),
                'type' => 'forgot',
                'verfied' => $user->verified
            ));
            // Send user an email
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
                'host' => $_SERVER['HTTP_HOST'],
                'email' => $user->email,
                'date' => time(),
                'recipient_title' => $user->getTitle(),
                'recipient_link' => $user->getHref(),
                'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
                'object_link' => $this->_helper->url->url(array('controller' => 'auth', 'action' => 'reset', 'code' => $code, 'uid' => $user->getIdentity()), 'siteotpverifier_extended', 'true'),
                'queue' => false,
            ));
            // Show success

            $db->commit();
            $bodyParams['response']['isEmail'] = 1;
            $this->respondWithSuccess($bodyParams);
        }


        //$values['option'] 1 email 0 phoneno
        // Ok now we can do the fun stuff


        try {

            if ($values['option']) {
                $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
                $db = $forgotTable->getAdapter();
                $db->beginTransaction();
                // Delete any existing reset password codes
                $forgotTable->delete(array(
                    'user_id = ?' => $user->getIdentity(),
                ));

                // Create a new reset password code
                $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
                $forgotTable->insert(array(
                    'user_id' => $user->getIdentity(),
                    'code' => $code,
                    'creation_date' => date('Y-m-d H:i:s'),
                    'type' => 'forgot',
                    'verfied' => $user->verified
                ));
                // Send user an email
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'email' => $user->email,
                    'date' => time(),
                    'recipient_title' => $user->getTitle(),
                    'recipient_link' => $user->getHref(),
                    'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
                    'object_link' => $this->_helper->url->url(array('controller' => 'auth', 'action' => 'reset', 'code' => $code, 'uid' => $user->getIdentity()), 'siteotpverifier_extended', 'true'),
                    'queue' => false,
                ));
                // Show success


                $db->commit();
                $bodyParams['response']['isEmail'] = 1;
                $this->respondWithSuccess($bodyParams);
            } else {
                $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
                $type = 'forgot';
                // genrate OTP code codes
                $response = $forgotTable->createCode($type, $user);
                if (!empty($response['error'])) {
                    $error = $response['error'];
                    $this->respondWithError('unauthorized', $error);
                }
                $code = $response['code'];
                //code for sending code to phone no.
                $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($otpUser, $code, $type);
            }
            $bodyParams['response']['duration'] = $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);

            $duration = round($expirytime / 60);
            if ($duration >= 1) {
                $time_text = "minute(s)";
            } else {
                $duration = $expirytime;
                $time_text = "seconds";
            }
            $bodyParams['otpMessage'] = "Note: OTP is valid for " . $duration . " " . $time_text;

            $bodyParams['response']['duration'] = $expirytime;
            
            $bodyParams['response']['type'] = $settings->getSetting('siteotpverifier.type');
            $bodyParams['response']['length'] = $settings->getSetting('siteotpverifier.length');
            $bodyParams['response']['code'] = $code;
            $bodyParams['response']['isEmail'] = 0;
            $bodyParams['response']['phoneno'] = $otpUser->phoneno;
            $bodyParams['response']['country_code'] = $otpUser->country_code;
            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    public function verifyAction() {
        $email = $this->_getParam('email', null);
        $code = $this->_getParam('code', null);
        $user = Engine_Api::_()->getDbtable('users', 'user')
                ->fetchRow(array('email = ?' => $email));
        if (!$user || !$user->getIdentity()) {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
                    ->fetchRow(array('phoneno = ?' => $email));
            $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id) : null;
            if (!$user || !$user->getIdentity()) {
                $this->respondWithError('unauthorized', "A user account with this email or phone number was not found.");
            }
        } else {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        }

        // Check user
        if (!$otpUser->phoneno) {
            $this->respondWithError('unauthorized', "You don't have register phone no with us.");
        }
        // Check for empty params
        $user_id = $user->getIdentity();
        $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
        // Check code
        $forgotSelect = $forgotTable->select()
                        ->where('user_id = ?', $user->getIdentity())
                        ->where('code = ?', $code)->where('type = ?', 'forgot');
        $forgotRow = $forgotTable->fetchRow($forgotSelect);
        if (!$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity()) {
            $this->respondWithError('unauthorized', 'OTP entered is not valid.');
        }
        $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
        // Code expired
        // Note: Let's set the current timeout for 10 minutes for now
        $min_creation_date = time() - ($expiaryTime);
        if (strtotime($forgotRow->modified_date) < $min_creation_date) { // @todo The strtotime might not work exactly right
            $this->respondWithError('unauthorized', "OTP has been expired. Please resend the OTP.");
        }
        $forgotTable->update(array('verfied' => 1), array('code =?' => $code, 'type =?' => 'forgot', 'user_id =?' => $user->getIdentity()));
        $bodyParams = array();
        $bodyParams['response']['email'] = $email;
        $this->respondWithSuccess($bodyParams);
    }

    public function resetAction() {
        // no logged in users
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->respondWithError('unauthorized', 'You are already loged in user.');
        }
        $email = $this->_getParam('email', null);
        $code = $this->_getParam('code', null);
        $user = Engine_Api::_()->getDbtable('users', 'user')
                ->fetchRow(array('email = ?' => $email));

        if (!$user || !$user->getIdentity()) {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
                    ->fetchRow(array('phoneno = ?' => $email));
            $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id) : null;
            if (!$user || !$user->getIdentity()) {
                $this->respondWithError('unauthorized', "A user account with this email or phone number was not found.");
            }
        } else {
            $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
        }
        // Check code
        $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
        $forgotSelect = $forgotTable->select()
                        ->where('user_id = ?', $user->getIdentity())
                        ->where('code = ?', $code)->where('type = ?', 'forgot');

        $forgotRow = $forgotTable->fetchRow($forgotSelect);
        if (!$forgotRow || empty($forgotRow->verfied) || (int) $forgotRow->user_id !== (int) $user->getIdentity()) {
            $this->respondWithError('unauthorized', 'OTP entered is not valid.');
        }
        if ($this->getRequest()->isGet()) {
            // Make form
            $form['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->getRestForm();
            $this->respondWithSuccess($form);
        }
        // Process
        $values = $_REQUEST;
        // Check same password
        $validationMessage = array();
        if (empty($values['password']))
            $validationMessage['password'] = $this->translate('Please complete this field - it is required.');

        if (empty($values['password_confirm']))
            $validationMessage['password_confirm'] = $this->translate('Please complete this field - it is required.');

        if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
        }

        if ($values['password'] !== $values['password_confirm']) {
            $this->respondWithError('unauthorized', 'The passwords you entered did not match.');
        }

        // Db
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Delete the lost password code now
            $forgotTable->delete(array(
                'user_id = ?' => $user->getIdentity(),
                'type = ?' => 'forgot',
            ));

            // This gets handled by the post-update hook
            $user->password = $values['password'];
            $user->save();

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}

?>
