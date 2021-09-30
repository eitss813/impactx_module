<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MangoPayBankDetail.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_MangoPayBankDetail extends Engine_Form {

    public function init() {
        parent::init();

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('MangoPay Bank Account Configuration')));
        $this->setName('sitecrowdfunding_mangopay_bank_detail');
        $description = '<div id="show_mangopay_bank_detail_form_massges" class="tool_tip"></div>';
        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->setDescription($description);
        $countryCodes = array(
            "AD" => "AD", "AE" => "AE", "AF" => "AF", "AG" => "AG", "AI" => "AI",
            "AL" => "AL", "AM" => "AM", "AO" => "AO", "AQ" => "AQ", "AR" => "AR",
            "AS" => "AS", "AT" => "AT", "AU" => "AU", "AW" => "AW", "AX" => "AX",
            "AZ" => "AZ", "BA" => "BA", "BB" => "BB", "BD" => "BD", "BE" => "BE",
            "BF" => "BF", "BG" => "BG", "BH" => "BH", "BI" => "BI", "BJ" => "BJ",
            "BL" => "BL", "BM" => "BM", "BN" => "BN", "BO" => "BO", "BQ" => "BQ",
            "BR" => "BR", "BS" => "BS", "BT" => "BT", "BV" => "BV", "BW" => "BW",
            "BY" => "BY", "BZ" => "BZ", "CA" => "CA", "CC" => "CC", "CD" => "CD",
            "CF" => "CF", "CG" => "CG", "CH" => "CH", "CI" => "CI", "CK" => "CK",
            "CL" => "CL", "CM" => "CM", "CN" => "CN", "CO" => "CO", "CR" => "CR",
            "CU" => "CU", "CV" => "CV", "CW" => "CW", "CX" => "CX", "CY" => "CY",
            "CZ" => "CZ", "DE" => "DE", "DJ" => "DJ", "DK" => "DK", "DM" => "DM",
            "DO" => "DO", "DZ" => "DZ", "EC" => "EC", "EE" => "EE", "EG" => "EG",
            "EH" => "EH", "ER" => "ER", "ES" => "ES", "ET" => "ET", "FI" => "FI",
            "FJ" => "FJ", "FK" => "FK", "FM" => "FM", "FO" => "FO", "FR" => "FR",
            "GA" => "GA", "GB" => "GB", "GD" => "GD", "GE" => "GE", "GF" => "GF",
            "GG" => "GG", "GH" => "GH", "GI" => "GI", "GL" => "GL", "GM" => "GM",
            "GN" => "GN", "GP" => "GP", "GQ" => "GQ", "GR" => "GR", "GS" => "GS",
            "GT" => "GT", "GU" => "GU", "GW" => "GW", "GY" => "GY", "HK" => "HK",
            "HM" => "HM", "HN" => "HN", "HR" => "HR", "HT" => "HT", "HU" => "HU",
            "ID" => "ID", "IE" => "IE", "IL" => "IL", "IM" => "IM", "IN" => "IN",
            "IO" => "IO", "IQ" => "IQ", "IR" => "IR", "IS" => "IS", "IT" => "IT",
            "JE" => "JE", "JM" => "JM", "JO" => "JO", "JP" => "JP", "KE" => "KE",
            "KG" => "KG", "KH" => "KH", "KI" => "KI", "KM" => "KM", "KN" => "KN",
            "KP" => "KP", "KR" => "KR", "KW" => "KW", "KY" => "KY", "KZ" => "KZ",
            "LA" => "LA", "LB" => "LB", "LC" => "LC", "LI" => "LI", "LK" => "LK",
            "LR" => "LR", "LS" => "LS", "LT" => "LT", "LU" => "LU", "LV" => "LV",
            "LY" => "LY", "MA" => "MA", "MC" => "MC", "MD" => "MD", "ME" => "ME",
            "MF" => "MF", "MG" => "MG", "MH" => "MH", "MK" => "MK", "ML" => "ML",
            "MM" => "MM", "MN" => "MN", "MO" => "MO", "MP" => "MP", "MQ" => "MQ",
            "MR" => "MR", "MS" => "MS", "MT" => "MT", "MU" => "MU", "MV" => "MV",
            "MW" => "MW", "MX" => "MX", "MY" => "MY", "MZ" => "MZ", "NA" => "NA",
            "NC" => "NC", "NE" => "NE", "NF" => "NF", "NG" => "NG", "NI" => "NI",
            "NL" => "NL", "NO" => "NO", "NP" => "NP", "NR" => "NR", "NU" => "NU",
            "NZ" => "NZ", "OM" => "OM", "PA" => "PA", "PE" => "PE", "PF" => "PF",
            "PG" => "PG", "PH" => "PH", "PK" => "PK", "PL" => "PL", "PM" => "PM",
            "PN" => "PN", "PR" => "PR", "PS" => "PS", "PT" => "PT", "PW" => "PW",
            "PY" => "PY", "QA" => "QA", "RE" => "RE", "RO" => "RO", "RS" => "RS",
            "RU" => "RU", "RW" => "RW", "SA" => "SA", "SB" => "SB", "SC" => "SC",
            "SD" => "SD", "SE" => "SE", "SG" => "SG", "SH" => "SH", "SI" => "SI",
            "SJ" => "SJ", "SK" => "SK", "SL" => "SL", "SM" => "SM", "SN" => "SN",
            "SO" => "SO", "SR" => "SR", "SS" => "SS", "ST" => "ST", "SV" => "SV",
            "SX" => "SX", "SY" => "SY", "SZ" => "SZ", "TC" => "TC", "TD" => "TD",
            "TF" => "TF", "TG" => "TG", "TH" => "TH", "TJ" => "TJ", "TK" => "TK",
            "TL" => "TL", "TM" => "TM", "TN" => "TN", "TO" => "TO", "TR" => "TR",
            "TT" => "TT", "TV" => "TV", "TW" => "TW", "TZ" => "TZ", "UA" => "UA",
            "UG" => "UG", "UM" => "UM", "US" => "US", "UY" => "UY", "UZ" => "UZ",
            "VA" => "VA", "VC" => "VC", "VE" => "VE", "VG" => "VG", "VI" => "VI",
            "VN" => "VN", "VU" => "VU", "WF" => "WF", "WS" => "WS", "YE" => "YE",
            "YT" => "YT", "ZA" => "ZA", "ZM" => "ZM", "ZW" => "ZW"
        );
        $accountTypes = array('IBAN' => 'IBAN', 'GB' => 'GB', 'US' => 'US', 'CA' => 'CA', 'OTHER' => 'OTHER');
        $this->addElement('Select', 'account_type', array(
            'label' => 'Bank account type',
            'multiOptions' => $accountTypes,
            'onchange' => 'changeMangoPayAccountType(this.value)',
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Text', 'owner_name', array(
            'label' => 'Owner name',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Textarea', 'owner_address', array(
            'label' => 'Owner address Line1',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
            ),
        ));
        $this->addElement('Textarea', 'owner_address2', array(
            'label' => 'Owner address Line2',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
            ),
        ));
        $this->addElement('Text', 'city', array(
            'label' => 'City',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->addElement('Text', 'region', array(
            'label' => 'Region',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->addElement('Text', 'postal_code', array(
            'label' => 'Postal Code',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->addElement('Select', 'country', array(
            'label' => 'Country',
            'multiOptions' => $countryCodes,
            'allowEmpty' => false,
            'required' => true,
            
        ));
        
        
        
        //Fields for IBAN ACCOUNT TYPE
        $this->addElement('Text', 'iban', array(
            'label' => 'IBAN',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('iban')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'bic', array(
            'label' => 'BIC',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('bic')->setAttribs(array('class'=>'optionalBankDetails'));
        //Fields for GB ACCOUNT TYPE
        $this->addElement('Text', 'sort_code', array(
            'label' => 'Sort code',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('sort_code')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'account_number', array(
            'label' => 'Account number',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('account_number')->setAttribs(array('class'=>'optionalBankDetails'));
        
        //Fields for US ACCOUNT TYPE
        $depositAccountTypeArr = array('CHECKING' => 'CHECKING', 'SAVINGS' => 'SAVINGS');
        $this->addElement('Select', 'deposit_account_type', array(
            'label' => "Deposit account type",
            'multiOptions' => $depositAccountTypeArr,
            'allowEmpty' => false,
            'required' => true,
        ));
        $this->getElement('deposit_account_type')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'aba', array(
            'label' => 'ABA',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('aba')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'us_account_number', array(
            'label' => 'Account number',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('us_account_number')->setAttribs(array('class'=>'optionalBankDetails'));
        //Fields for CA ACCOUNT TYPE
        $this->addElement('Text', 'branch_code', array(
            'label' => 'Branch code',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('branch_code')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'bank_name', array(
            'label' => 'Bank name',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('bank_name')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'institution_number', array(
            'label' => 'Institution number',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('institution_number')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'ca_account_number', array(
            'label' => 'Account number',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('ca_account_number')->setAttribs(array('class'=>'optionalBankDetails'));
        //Fields for CA ACCOUNT TYPE
        $this->addElement('Text', 'other_bic', array(
            'label' => 'BIC',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('other_bic')->setAttribs(array('class'=>'optionalBankDetails'));
        $this->addElement('Text', 'other_account_number', array(
            'label' => 'Account number',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->getElement('other_account_number')->setAttribs(array('class'=>'optionalBankDetails'));
    }

}
