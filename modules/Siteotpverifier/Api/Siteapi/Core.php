<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
use Aws\Sns\SnsClient;
use Twilio\Rest\Client;

class Siteotpverifier_Api_Siteapi_Core extends Siteotpverifier_Api_Core {

    private $_profileFieldsArray = array();

    public function __construct() {

//        require APPLICATION_PATH.'/application/libraries/aws/aws-autoloader.php';
//        require APPLICATION_PATH.'/application/libraries/Twilio/autoload.php';
    }

    //private $_validateSearchProfileFields = false;

    public function getSignupAccountForm($accountForm = array()) {
            // Set the translations for zend library.
            if (!Zend_Registry::isRegistered('Zend_Translate'))
                Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

            $settings = Engine_Api::_()->getApi('settings', 'core');
            $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
            $countrycodes = Engine_Api::_()->getApi('core', 'siteotpverifier')->countryCode();
            $countrycodes = array_keys($countrycodes);
            $countrycodes = array_combine($countrycodes, $countrycodes);
            $reqphoneno = !empty($showBothPhoneAndEmail) && $settings->getSetting('siteotpverifier.singupRequirePhone', 1);
            //$countrycodes = $this->getCountryCode();

            if (!empty($showBothPhoneAndEmail)) {
                // Element: email
                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'email',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address'),
                    'hasValidator' => true
                );

                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'country_code',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country Code'),
                    'multiOptions' => $countrycodes,
                    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry', '+1'),
                );

                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'phoneno',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Phone Number'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter phone no with country code.'),
                    'hasValidator' => !empty($reqphoneno) ? true : false
                );
            } else {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'country_code',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country Code'),
                    'multiOptions' => $countrycodes,
                    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry', '+1'),
                );


                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'emailaddress',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email or Mobile Number'),
                    'hasValidator' => true
                );
            }

            // Element: code
            if ($settings->getSetting('user.signup.inviteonly') > 0) {
                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'code',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite Code'),
                    'hasValidator' => true
                );
            }

            if ($settings->getSetting('user.signup.random', 0) == 0 && empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id'])) {
                // Element: password
                $accountForm[] = array(
                    'type' => 'Password',
                    'name' => 'password',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password'),
                    'hasValidator' => true
                );

                // Element: passconf
                $accountForm[] = array(
                    'type' => 'Password',
                    'name' => 'passconf',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password Again'),
                    'hasValidator' => true
                );
            }

            // Element: username
            if ($settings->getSetting('user.signup.username', 1) > 0) {
                $description = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Username must be all lowercase with one number no spaces allowed');

                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'username',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Address'),
                    'description' => $description,
                    'hasValidator' => true
                );
            }
            // Element: profile_type
            $profileFields = $this->getProfileTypes();
            if (!empty($profileFields)) {
                $this->_profileFieldsArray = $profileFields;

                if (COUNT($profileFields) > 1) {
                    $accountForm[] = array(
                        'type' => 'Select',
                        'name' => 'profile_type',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Type'),
                        'multiOptions' => $profileFields,
                        'hasValidator' => true
                    );
                }
            }

            // Element: timezone
            $timezone = $this->_getTimeZone;
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'timezone',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
                'multiOptions' => $timezone,
                'hasValidator' => true
            );

            // Element: language
            $translate = Zend_Registry::get('Zend_Translate');
            $languageList = $translate->getList();
            if (COUNT($languageList) > 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'language',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Language'),
                    'multiOptions' => $this->getLanguages(),
                    'hasValidator' => true
                );
            }


            // Element: terms
            if ($settings->getSetting('user.signup.terms', 1) == 1) {
                $accountForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'terms',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('I have read and agree to the terms of service.'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('I have read and agree to the terms of service.'),
                    'hasValidator' => true
                );


            if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'ios' && _IOS_VERSION > '1.5.5') || (_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.7.1'))) {
                $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseParentUrl = @trim($baseParentUrl, "/");
                $table = (_CLIENT_TYPE === 'ios') ? Engine_Api::_()->getDbtable('menus', 'siteiosapp') : Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
                $select = $table->select()
                        ->where('status = ?', 1)
                        ->where('name = ?', 'terms_of_service')
                        ->limit(1);

                $menu = $table->fetchRow($select);

                if (($menu->name == 'terms_of_service')) {
                    if (empty($menu->url))
                        $url = (!empty($baseParentUrl)) ? $getHost . DIRECTORY_SEPARATOR . $baseParentUrl . DIRECTORY_SEPARATOR . 'help/terms' : $getHost . DIRECTORY_SEPARATOR . 'help/terms';
                    else
                        $url = $menu->url;
                }

                if (isset($url) && !empty($url)) {
                    $accountForm[] = array(
                        'type' => 'Dummy',
                        'name' => 'terms_url',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Click here to read the terms of service.'),
                        'url' => $url
                    );
                }
            }
        }
        return $accountForm;
    }

    public function getProfileTypes($profileFields = array()) {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

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

    public function getLanguages() {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();

        // Get the default local.
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = '';
            }
        }

        // Find out the local.
        $locale = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        try {
            $locale = Zend_Locale::findLocale($locale);
        } catch (Exception $e) {
            $locale = 'en_US';
        }
        $localeObject = new Zend_Locale($locale);

        $languageDataList = $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territoryDataList = $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        // Make the language array.
        $languageNameList = array();
        foreach ($languageList as $key) {
            $languageNameList[$key] = Zend_Locale::getTranslation($key, 'language', $key);

            if (empty($languageNameList[$key])) {
                list($locale, $territory) = explode('_', $key);
                $languageNameList[$key] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
            }
        }

        // Set default language at first place.
        $languageNameList = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $languageNameList);

        return $languageNameList;
    }

    public $_getTimeZone = array(
        'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central' => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
        'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
        'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
        'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
        'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
        'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow,Israel',
        'Iran' => '(UTC+3:30) Tehran',
        'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        'Asia/Kabul' => '(UTC+4:30) Kabul',
        'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
        'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        'Asia/Katmandu' => '(UTC+5:45) Nepal',
        'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
        'Indian/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
        'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
        'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        'Asia/Magadan' => '(UTC+11) Magadan, Solomon Is., New Caledonia',
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
    );

    public function getCountryCode() {
        $countryCode = array("+972" => "Israel (+972)", "+93" => "Afghanistan (+93)", "+355" => "Albania (+355)", "+213" => "Algeria (+213)", "+1 684" => "AmericanSamoa (+1 684)", "+376" => "Andorra (+376)", "+244" => "Angola (+244)", "+1 264" => "Anguilla (+1 264)", "+1268" => "Antigua and Barbuda (+1268)", "+54" => "Argentina (+54)", "+374" => "Armenia (+374)", "+297" => "Aruba (+297)", "+61" => "Australia (+61)", "+43" => "Austria (+43)", "+994" => "Azerbaijan (+994)", "+1 242" => "Bahamas (+1 242)", "+973" => "Bahrain (+973)", "+880" => "Bangladesh (+880)", "+1 246" => "Barbados (+1 246)", "+375" => "Belarus (+375)", "+32" => "Belgium (+32)", "+501" => "Belize (+501)", "+229" => "Benin (+229)", "+1 441" => "Bermuda (+1 441)", "+975" => "Bhutan (+975)", "+387" => "Bosnia and Herzegovina (+387)", "+267" => "Botswana (+267)", "+55" => "Brazil (+55)", "+246" => "British Indian Ocean Territory (+246)", "+359" => "Bulgaria (+359)", "+226" => "Burkina Faso (+226)", "+257" => "Burundi (+257)", "+855" => "Cambodia (+855)", "+237" => "Cameroon (+237)", "+1" => "Canada (+1)", "+238" => "Cape Verde (+238)", "+345" => "Cayman Islands (+345)", "+236" => "Central African Republic (+236)", "+235" => "Chad (+235)", "+56" => "Chile (+56)", "+86" => "China (+86)", "+61" => "Christmas Island (+61)", "+57" => "Colombia (+57)", "+269" => "Comoros (+269)", "+242" => "Congo (+242)", "+682" => "Cook Islands (+682)", "+506" => "Costa Rica (+506)", "+385" => "Croatia (+385)", "+53" => "Cuba (+53)", "+537" => "Cyprus (+537)", "+420" => "Czech Republic (+420)", "+45" => "Denmark (+45)", "+253" => "Djibouti (+253)", "+1767" => "Dominica (+1 767)", "+1849" => "Dominican Republic (+1 849)", "+593" => "Ecuador (+593)", "+20" => "Egypt (+20)", "+503" => "El Salvador (+503)", "+240" => "Equatorial Guinea (+240)", "+291" => "Eritrea (+291)", "+372" => "Estonia (+372)", "+251" => "Ethiopia (+251)", "+298" => "Faroe Islands (+298)", "+679" => "Fiji (+679)", "+358" => "Finland (+358)", "+33" => "France (+33)", "+594" => "French Guiana (+594)", "+689" => "French Polynesia (+689)", "+241" => "Gabon (+241)", "+220" => "Gambia (+220)", "+995" => "Georgia (+995)", "+49" => "Germany (+49)", "+233" => "Ghana (+233)", "+350" => "Gibraltar (+350)", "+30" => "Greece (+30)", "+299" => "Greenland (+299)", "+1473" => "Grenada (+1 473)", "+590" => "Guadeloupe (+590)", "+1671" => "Guam (+1 671)", "+502" => "Guatemala (+502)", "+224" => "Guinea (+224)", "+245" => "Guinea-Bissau (+245)", "+595" => "Guyana (+595)", "+509" => "Haiti (+509)", "+504" => "Honduras (+504)", "+36" => "Hungary (+36)", "+354" => "Iceland (+354)", "+91" => "India (+91)", "+62" => "Indonesia (+62)", "+964" => "Iraq (+964)", "+353" => "Ireland (+353)", "+972" => "Israel (+972)", "+39" => "Italy (+39)", "+1876" => "Jamaica (+1 876)", "+81" => "Japan (+81)", "+962" => "Jordan (+962)", "+77" => "Kazakhstan (+7 7)", "+254" => "Kenya (+254)", "+686" => "Kiribati (+686)", "+965" => "Kuwait (+965)", "+996" => "Kyrgyzstan (+996)", "+371" => "Latvia (+371)", "+961" => "Lebanon (+961)", "+266" => "Lesotho (+266)", "+231" => "Liberia (+231)", "+423" => "Liechtenstein (+423)", "+370" => "Lithuania (+370)", "+352" => "Luxembourg (+352)", "+261" => "Madagascar (+261)", "+265" => "Malawi (+265)", "+60" => "Malaysia (+60)", "+960" => "Maldives (+960)", "+223" => "Mali (+223)", "+356" => "Malta (+356)", "+692" => "Marshall Islands (+692)", "+596" => "Martinique (+596)", "+222" => "Mauritania (+222)", "+230" => "Mauritius (+230)", "+262" => "Mayotte (+262)", "+52" => "Mexico (+52)", "+377" => "Monaco (+377)", "+976" => "Mongolia (+976)", "+382" => "Montenegro (+382)", "+1664" => "Montserrat (+1664)", "+212" => "Morocco (+212)", "+95" => "Myanmar (+95)", "+264" => "Namibia (+264)", "+674" => "Nauru (+674)", "+977" => "Nepal (+977)", "+31" => "Netherlands (+31)", "+599" => "Netherlands Antilles (+599)", "+687" => "New Caledonia (+687)", "+64" => "New Zealand (+64)", "+505" => "Nicaragua (+505)", "+227" => "Niger (+227)", "+234" => "Nigeria (+234)", "+683" => "Niue (+683)", "+672" => "Norfolk Island (+672)", "+1670" => "Northern Mariana Islands (+1 670)", "+47" => "Norway (+47)", "+968" => "Oman (+968)", "+92" => "Pakistan (+92)", "+680" => "Palau (+680)", "+507" => "Panama (+507)", "+675" => "Papua New Guinea (+675)", "+595" => "Paraguay (+595)", "+51" => "Peru (+51)", "+63" => "Philippines (+63)", "+48" => "Poland (+48)", "+351" => "Portugal (+351)", "+1939" => "Puerto Rico (+1 939)", "+974" => "Qatar (+974)", "+40" => "Romania (+40)", "+250" => "Rwanda (+250)", "+685" => "Samoa (+685)", "+378" => "San Marino (+378)", "+966" => "Saudi Arabia (+966)", "+221" => "Senegal (+221)", "+381" => "Serbia (+381)", "+248" => "Seychelles (+248)", "+232" => "Sierra Leone (+232)", "+65" => "Singapore (+65)", "+421" => "Slovakia (+421)", "+386" => "Slovenia (+386)", "+677" => "Solomon Islands (+677)", "+27" => "South Africa (+27)", "+500" => "South Georgia and the South Sandwich Islands (+500)", "+34" => "Spain (+34)", "+94" => "Sri Lanka (+94)", "+249" => "Sudan (+249)", "+597" => "Suriname (+597)", "+268" => "Swaziland (+268)", "+46" => "Sweden (+46)", "+41" => "Switzerland (+41)", "+992" => "Tajikistan (+992)", "+66" => "Thailand (+66)", "+228" => "Togo (+228)", "+690" => "Tokelau (+690)", "+676" => "Tonga (+676)", "+1868" => "Trinidad and Tobago (+1 868)", "+216" => "Tunisia (+216)", "+90" => "Turkey (+90)", "+993" => "Turkmenistan (+993)", "+1649" => "Turks and Caicos Islands (+1 649)", "+688" => "Tuvalu (+688)", "+256" => "Uganda (+256)", "+380" => "Ukraine (+380)", "+971" => "United Arab Emirates (+971)", "+44" => "United Kingdom (+44)", "+1" => "United States (+1)", "+598" => "Uruguay (+598)", "+998" => "Uzbekistan (+998)", "+678" => "Vanuatu (+678)", "+681" => "Wallis and Futuna (+681)", "+967" => "Yemen (+967)", "+260" => "Zambia (+260)", "+263" => "Zimbabwe (+263)", "+591" => "Bolivia, Plurinational State of (+591)", "+673" => "Brunei Darussalam (+673)", "+61" => "Cocos (Keeling) Islands (+61)", "+243" => "Congo, The Democratic Republic of the (+243)", "+225" => "Cote d'Ivoire (+225)", "+500" => "Falkland Islands (Malvinas) (+500)", "+44" => "Guernsey (+44)", "+379" => "Holy See (Vatican City State) (+379)", "+852" => "Hong Kong (+852)", "+98" => "Iran, Islamic Republic of (+98)", "+44" => "Isle of Man (+44)", "+44" => "Jersey (+44)", "+850" => "Korea, Democratic People's Republic of (+850)", "+82" => "Korea, Republic of (+82)", "+856" => "Lao People's Democratic Republic (+856)", "+218" => "Libyan Arab Jamahiriya (+218)", "+853" => "Macao (+853)", "+389" => "Macedonia, The Former Yugoslav Republic of (+389)", "+691" => "Micronesia, Federated States of (+691)", "+373" => "Moldova, Republic of (+373)", "+258" => "Mozambique (+258)", "+970" => "Palestinian Territory, Occupied (+970)", "+872" => "Pitcairn (+872)", "+262" => "Réunion (+262)", "+7" => "Russia (+7)", "+590" => "Saint Barthélemy (+590)", "+290" => "Saint Helena, Ascension and Tristan Da Cunha (+290)", "+1869" => "Saint Kitts and Nevis (+1 869)", "+1758" => "Saint Lucia (+1 758)", "+590" => "Saint Martin (+590)", "+508" => "Saint Pierre and Miquelon (+508)", "+1784" => "Saint Vincent and the Grenadines (+1 784)", "+239" => "Sao Tome and Principe (+239)", "+252" => "Somalia (+252)", "+47" => "Svalbard and Jan Mayen (+47)", "+963" => "Syrian Arab Republic (+963)", "+886" => "Taiwan, Province of China (+886)", "+255" => "Tanzania, United Republic of (+255)", "+670" => "Timor-Leste (+670)", "+58" => "Venezuela, Bolivarian Republic of (+58)", "+84" => "Viet Nam (+84)", "+1284" => "Virgin Islands, British (+1 284)", "+1340" => "Virgin Islands, U.S. (+1 340)");

        $allowcountryCode = Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_allowCountry;
        $allowedCountrycode = array();
        if (!empty($allowcountryCode)) {
            $allowedCountrycodes = array();
            foreach ($allowcountryCode as $key => $value) {
                $allowedCountrycode[$value] = $countryCode[$value];
            }
            if (!empty($allowedCountrycode))
                $allowedCountrycode = $allowedCountrycode;
        }
        else {
            return $countryCode;
        }
        return $allowedCountrycode;
    }

public function verifyMobileNo($phone, $code)
  {

    if( empty($phone) || empty($code) ) {
      return 0;
    }
    $type = 'signup';
    $message = Engine_Api::_()->getApi('core', 'siteotpverifier')->genrateMessage($type, $code);
    $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOTPMessage($phone, $message, $type);
    
    return $status;
  }

    public function sendOtpCode(User_Model_User $user, $code, $type) {
        if (!$user || !$user->getIdentity()) {
            return 0;
        }
        if (empty($user->phoneno)) {
            return 0;
        }

        $nativelangauge = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.nativelangauge', 0);
        if (!empty($nativelangauge)) {
            Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
            $language = Zend_Registry::get('Locale')->getLanguage();
            $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');

            $select = $messageTable->select()->where('language=?', $language);
            $param = $messageTable->fetchRow($select);
        } else {
            $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
            $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');

            $select = $messageTable->select()->where('language=?', $defaultLanguage);
            $param = $messageTable->fetchRow($select);
        }
        $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
        $timestring = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);
        if (!empty($param[$type]) && strpos($param[$type], '[code]') !== false) {

            $message = str_replace("[code]", $code, $param[$type]);
            $message = str_replace("[expirytime]", $timestring, $message);
        } else {
            $message = 'Your code for OTP verification is ' . $code;
        }
        $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
        if ($service == "amazon") {
            $amazonSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon;
            $client_id = isset($amazonSettings['clientId']) ? $amazonSettings['clientId'] : 0;
            $client_secret = isset($amazonSettings['clientSecret']) ? $amazonSettings['clientSecret'] : 0;
            if (empty($client_id) || empty($client_secret)) {
                return 0;
            }
            $client = new SnsClient([
                'version' => 'latest',
                'region' => 'us-west-2',
                'credentials' => [
                    'key' => $client_id,
                    'secret' => $client_secret,
                ],
            ]);
            $array = array('attributes' => array('DefaultSenderID' => 'test', 'DefaultSMSType' => 'Transactional'));
            $client->setSMSAttributes($array);
            $ccode = empty($user->country_code) ? '+1' : $user->country_code;

            $result = $client->publish([
                'Message' => $message, // REQUIRED
                'PhoneNumber' => $ccode . $user->phoneno,
                'Subject' => 'Test',
            ]);
            $statistictable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
            $statistictable->insert(array(
                'user_id' => $user->getIdentity(),
                'type' => $type,
                'creation_date' => date('Y-m-d H:i:s'),
                'service' => 'amazon',
            ));
        } elseif ($service == "twilio") {
            $twilioSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio;
            $sid = isset($twilioSettings['accountsid']) ? $twilioSettings['accountsid'] : 0;
            $token = isset($twilioSettings['apikey']) ? $twilioSettings['apikey'] : 0;
            $clientphone_no = isset($twilioSettings['phoneno']) ? $twilioSettings['phoneno'] : 0;
            if (empty($sid) || empty($token) || empty($clientphone_no)) {
                return 0;
            }
            $ccode = empty($user->country_code) ? '+1' : $user->country_code;
            $client = new Client($sid, $token);

            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
                    // the number you'd like to send the message to
                    $ccode . $user->phoneno, array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => $clientphone_no,
                // the body of the text message you'd like to send
                'body' => $message
                    )
            );
            $statistictable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
            $statistictable->insert(array(
                'user_id' => $user->getIdentity(),
                'type' => $type,
                'creation_date' => date('Y-m-d H:i:s'),
                'service' => 'twilio',
            ));
        }


        //        if(isset($result->data->MessageId)){
        //            $message_id=$result->data->MessageId;
        //            return $message_id;
        //        }
        //return 1;
    }

    public function sendOtpCodeWitoutUser($phone, $code, $type) {
        if (empty($phone) || empty($code)) {
            return 0;
        }
        $user = Engine_Api::_()->user()->getViewer();
        $nativelangauge = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.nativelangauge', 0);
        if (!empty($nativelangauge)) {
            Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
            $language = Zend_Registry::get('Locale')->getLanguage();
            $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');

            $select = $messageTable->select()->where('language=?', $language);
            $param = $messageTable->fetchRow($select);
        } else {
            $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
            $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');

            $select = $messageTable->select()->where('language=?', $defaultLanguage);
            $param = $messageTable->fetchRow($select);
        }
        $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
        $timestring = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);

        if (!empty($param[$type]) && strpos($param[$type], '[code]') !== false) {
            $message = str_replace("[code]", $code, $param[$type]);
            $message = str_replace("[expirytime]", $timestring, $message);
        } else {
            $message = 'Your code for OTP verification is ' . $code;
        }

        $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
        if ($service == "amazon") {
            $amazonSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon;
            $client_id = isset($amazonSettings['clientId']) ? $amazonSettings['clientId'] : 0;
            $client_secret = isset($amazonSettings['clientSecret']) ? $amazonSettings['clientSecret'] : 0;
            if (empty($client_id) || empty($client_secret)) {
                return 0;
            }
            $client = new SnsClient([
                'version' => 'latest',
                'region' => 'us-west-2',
                'credentials' => [
                    'key' => $client_id,
                    'secret' => $client_secret,
                ],
            ]);
            $array = array('attributes' => array('DefaultSenderID' => 'test', 'DefaultSMSType' => 'Transactional'));
            $client->setSMSAttributes($array);
            $result = $client->publish([
                'Message' => $message, // REQUIRED
                'PhoneNumber' => $phone,
                'Subject' => 'Test',
            ]);
            $statistictable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
            $statistictable->insert(array(
                'user_id' => $user->getIdentity(),
                'type' => $type,
                'creation_date' => date('Y-m-d H:i:s'),
                'service' => 'amazon',
            ));
        } elseif ($service == "twilio") {
            $twilioSettings = (array) Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio;
            $sid = isset($twilioSettings['accountsid']) ? $twilioSettings['accountsid'] : 0;
            $token = isset($twilioSettings['apikey']) ? $twilioSettings['apikey'] : 0;
            $clientphone_no = isset($twilioSettings['phoneno']) ? $twilioSettings['phoneno'] : 0;
            if (empty($sid) || empty($token) || empty($clientphone_no)) {
                return 0;
            }
            $client = new Client($sid, $token);

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
            $statistictable = Engine_Api::_()->getDbtable('statistics', 'siteotpverifier');
            $statistictable->insert(array(
                'user_id' => $user->getIdentity(),
                'type' => $type,
                'creation_date' => date('Y-m-d H:i:s'),
                'service' => 'twilio',
            ));
        }
    }
    
    public function getRestForm(){
	$resetForm=array();
	$resetForm[] = array(
                        'type' => 'Password',
                        'name' => 'password',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New Password'),
                        'hasValidator' => true
                    );
	
	$resetForm[] = array(
                        'type' => 'Password',
                        'name' => 'password_confirm',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Confirm Password'),
                        'hasValidator' => true
                    );
        $resetForm[] = array(
                        'type' => 'Submit',
                        'name' => 'submit',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Reset'),
                    );
	return $resetForm;
    }

}

?>