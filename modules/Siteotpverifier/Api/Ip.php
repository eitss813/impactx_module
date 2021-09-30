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
class Siteotpverifier_Api_Ip extends Core_Api_Abstract
{

  // STORE ENGINE IP OBJECT
  protected $_ip;

  // STORE CURRENT IP'S DATABASE PATH
  protected $_ipDb;

  // STORE ALL IP DATABASE PATHS
  protected $_ipDbArray = array(
    'ipv4' => APPLICATION_PATH_PUB . DS . 'maxmind-geoip' . DS . 'ipv4-country.dat',
    'ipv6' => APPLICATION_PATH_PUB . DS . 'maxmind-geoip' . DS . 'ipv6-country.dat',
  );

  // STORE ALL IP REMOTE DATABASE URL - LOCAL DATABASE NEEDS TO BE UPDATED FROM THIS URL
  // https://dev.maxmind.com/geoip/legacy/install/country/
  // $ wget -N http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
  // $ wget -N http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
  protected $_ipDbRemote = array(
    'ipv4' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz',
    'ipv6' => 'http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz',
  );

  // SET IP OBJECT, IP DATABSE & DOWNLOAD DATABSE TO SERVER IF NOT FOUND
  public function __construct() {
    $this->_ip = $ip = new Engine_IP();
    if ($ip->isIPv4()) {
      $this->_ipDb = $this->_ipDbArray['ipv4'];
    } else {
      $this->_ipDb = $this->_ipDbArray['ipv6'];
    }
    if (!$this->hasDb()) {
      $this->updateDb();
    }
  }

  // RETURN IP OBJECT
  protected function getIp() {
    return $this->_ip;
  }

  // RETURN CURRENT IP'S DATABASE FILE PATH
  protected function getDb() {
    return $this->_ipDb;
  }

  // CHECK IF WEBSITE HAS IP DATABSE OR NOT - INITIALLY THERE WILL NOT BE ANY DATABSE ON SERVER
  protected function hasDb() {
    return file_exists($this->_ipDb);
  }

  protected function isAllowed($country) {
    $allowedCountries = Engine_Api::_()->siteotpverifier()->countryCode();
    return isset($this->_dialCodes[$country]) && isset($allowedCountries[$this->_dialCodes[$country]]);
  }

  // RETURN COUNTRY CODE FOR THE CURRENT IP
  public function getCountryCode() {
    $country = false;
    try {
      $geoip = Net_GeoIP::getInstance($this->getDb());
      $country = $geoip->lookupCountryCode($this->getIp()->toString());
    } catch (Exception $e) {
    }
    if (!empty($country) && $this->isAllowed($country) ) {
      return $this->_dialCodes[$country];
    }
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.defaultCountry', '+1');
  }

  // CHECK IF IP IS VALID
  public function isValid() {
    return $this->getIp()->isValid();
  }

  // RETURN TRUE IF DATABASE NEEDS TO BE UPDATED - RETURN TRUE AFTER A PARTICULAR TIME INTERVAL
  public function checkUpdate() {
    $lastUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.ipdb.date.updated', date("2000-01-01"));
    $nextUpdate = date('Y-m-d', strtotime($lastUpdate .' + 3 days'));
    return strtotime($nextUpdate) < strtotime(date('Y-m-d'));
  }

  // DOWNLOAD DATABASES FROM MAXMIND SERVER AND STORE INTO WEBSITE PUBLIC FOLDER
  public function updateDb() {
    foreach ($this->_ipDbRemote as $type => $remote) {
      $file = $this->downloadDb($remote);
      $this->extractAndMove($file, $this->_ipDbArray[$type]);
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('siteotpverifier.ipdb.date.updated', date('Y-m-d') );
  }

  protected function downloadDb ($remote) {
    $urlParts = explode('/', trim(parse_url($remote, PHP_URL_PATH), '/'));
    $newfilename = end($urlParts);
    $local_path = str_replace('/', DS, APPLICATION_PATH . '/temporary/');
    $path = $local_path . $newfilename;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    ob_start();
    $result = curl_exec($ch);
    if( empty($result) ) {
      $result = file_get_contents($remote);
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
    return $path;
  }

  // EXTRACT THE GZIP FILE AND MOVE THE IP DATABASE TO PUBLIC FOLDER
  protected function extractAndMove ($gz, $dat) {

    // CREATE PUBLIC DIRECTORY IF NOT CREATED
    if (!file_exists(dirname($dat))) {
      @mkdir(dirname($dat));
      @chmod(dirname($dat), 0777);
    }

    // EXTRACT DAT.GZ FILE TO .GZ FILE IN PUBLIC
    $gz_file = gzopen($gz, 'rb');
    $dat_file = fopen($dat, 'wb');
    while($gz_file && !gzeof($gz_file)) {
      fwrite($dat_file, gzread($gz_file, 4096));
    }
    fclose($dat_file);
    gzclose($gz_file);

    // SET PERMISSION FOR FILE AND DELETE TEMPORARY FILE
    @chmod($dat, 0777);
    @unlink($gz);
  }

  // COUNTRY CODES WITH THEIR DIAL CODES
  protected $_dialCodes = array(
    "IL" => "+972",
    "AF" => "+93",
    "AL" => "+355",
    "DZ" => "+213",
    "AS" => "+1684",
    "AD" => "+376",
    "AO" => "+244",
    "AI" => "+1264",
    "AG" => "+1268",
    "AR" => "+54",
    "AM" => "+374",
    "AW" => "+297",
    "AU" => "+61",
    "AT" => "+43",
    "AZ" => "+994",
    "BS" => "+1242",
    "BH" => "+973",
    "BD" => "+880",
    "BB" => "+1246",
    "BY" => "+375",
    "BE" => "+32",
    "BZ" => "+501",
    "BJ" => "+229",
    "BM" => "+1441",
    "BT" => "+975",
    "BA" => "+387",
    "BW" => "+267",
    "BR" => "+55",
    "IO" => "+246",
    "BG" => "+359",
    "BF" => "+226",
    "BI" => "+257",
    "KH" => "+855",
    "CM" => "+237",
    "CA" => "+1",
    "CV" => "+238",
    "KY" => "+345",
    "CF" => "+236",
    "TD" => "+235",
    "CL" => "+56",
    "CN" => "+86",
    "CX" => "+61",
    "CO" => "+57",
    "KM" => "+269",
    "CG" => "+242",
    "CK" => "+682",
    "CR" => "+506",
    "HR" => "+385",
    "CU" => "+53",
    "CY" => "+537",
    "CZ" => "+420",
    "DK" => "+45",
    "DJ" => "+253",
    "DM" => "+1767",
    "DO" => "+1849",
    "EC" => "+593",
    "EG" => "+20",
    "SV" => "+503",
    "GQ" => "+240",
    "ER" => "+291",
    "EE" => "+372",
    "ET" => "+251",
    "FO" => "+298",
    "FJ" => "+679",
    "FI" => "+358",
    "FR" => "+33",
    "GF" => "+594",
    "PF" => "+689",
    "GA" => "+241",
    "GM" => "+220",
    "GE" => "+995",
    "DE" => "+49",
    "GH" => "+233",
    "GI" => "+350",
    "GR" => "+30",
    "GL" => "+299",
    "GD" => "+1473",
    "GP" => "+590",
    "GU" => "+1671",
    "GT" => "+502",
    "GN" => "+224",
    "GW" => "+245",
    "GY" => "+595",
    "HT" => "+509",
    "HN" => "+504",
    "HU" => "+36",
    "IS" => "+354",
    "IN" => "+91",
    "ID" => "+62",
    "IQ" => "+964",
    "IE" => "+353",
    "IT" => "+39",
    "JM" => "+1876",
    "JP" => "+81",
    "JO" => "+962",
    "KZ" => "+77",
    "KE" => "+254",
    "KI" => "+686",
    "KW" => "+965",
    "KG" => "+996",
    "LV" => "+371",
    "LB" => "+961",
    "LS" => "+266",
    "LR" => "+231",
    "LI" => "+423",
    "LT" => "+370",
    "LU" => "+352",
    "MG" => "+261",
    "MW" => "+265",
    "MY" => "+60",
    "MV" => "+960",
    "ML" => "+223",
    "MT" => "+356",
    "MH" => "+692",
    "MQ" => "+596",
    "MR" => "+222",
    "MU" => "+230",
    "YT" => "+262",
    "MX" => "+52",
    "MC" => "+377",
    "MN" => "+976",
    "ME" => "+382",
    "MS" => "+1664",
    "MA" => "+212",
    "MM" => "+95",
    "NA" => "+264",
    "NR" => "+674",
    "NP" => "+977",
    "NL" => "+31",
    "AN" => "+599",
    "NC" => "+687",
    "NZ" => "+64",
    "NI" => "+505",
    "NE" => "+227",
    "NG" => "+234",
    "NU" => "+683",
    "NF" => "+672",
    "MP" => "+1670",
    "NO" => "+47",
    "OM" => "+968",
    "PK" => "+92",
    "PW" => "+680",
    "PA" => "+507",
    "PG" => "+675",
    "PY" => "+595",
    "PE" => "+51",
    "PH" => "+63",
    "PL" => "+48",
    "PT" => "+351",
    "PR" => "+1939",
    "QA" => "+974",
    "RO" => "+40",
    "RW" => "+250",
    "WS" => "+685",
    "SM" => "+378",
    "SA" => "+966",
    "SN" => "+221",
    "RS" => "+381",
    "SC" => "+248",
    "SL" => "+232",
    "SG" => "+65",
    "SK" => "+421",
    "SI" => "+386",
    "SB" => "+677",
    "ZA" => "+27",
    "GS" => "+500",
    "ES" => "+34",
    "LK" => "+94",
    "SD" => "+249",
    "SR" => "+597",
    "SZ" => "+268",
    "SE" => "+46",
    "CH" => "+41",
    "TJ" => "+992",
    "TH" => "+66",
    "TG" => "+228",
    "TK" => "+690",
    "TO" => "+676",
    "TT" => "+1868",
    "TN" => "+216",
    "TR" => "+90",
    "TM" => "+993",
    "TC" => "+1649",
    "TV" => "+688",
    "UG" => "+256",
    "UA" => "+380",
    "AE" => "+971",
    "GB" => "+44",
    "US" => "+1",
    "UY" => "+598",
    "UZ" => "+998",
    "VU" => "+678",
    "WF" => "+681",
    "ZW" => "+263",
    "AX" => "",
    "AQ" => null,
    "BO" => "+591",
    "BN" => "+673",
    "CC" => "+61",
    "CD" => "+243",
    "CI" => "+225",
    "FK" => "+500",
    "GG" => "+44",
    "VA" => "+379",
    "HK" => "+852",
    "IR" => "+98",
    "IM" => "+44",
    "JE" => "+44",
    "KP" => "+850",
    "KR" => "+82",
    "LA" => "+856",
    "LY" => "+218",
    "MO" => "+853",
    "MK" => "+389",
    "FM" => "+691",
    "MD" => "+373",
    "MZ" => "+258",
    "PS" => "+970",
    "PN" => "+872",
    "RE" => "+262",
    "RU" => "+7",
    "BL" => "+590",
    "SH" => "+290",
    "KN" => "+1869",
    "LC" => "+1758",
    "MF" => "+590",
    "PM" => "+508",
    "VC" => "+1784",
    "ST" => "+239",
    "SO" => "+252",
    "SJ" => "+47",
    "SY" => "+963",
    "TW" => "+886",
    "TZ" => "+255",
    "TL" => "+670",
    "VE" => "+58",
    "VN" => "+84",
    "VG" => "+1284",
    "VI" => "+1340"
  );
}
