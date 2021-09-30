<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MapSocial.php 2018-02-21 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Form_Admin_Mapfields extends Engine_Form {

	public $_socialSite, $_maps;

	public function setSocialSite($_socialSite)
	{
		if(!empty($_socialSite)) {
			$this->_socialSite = $_socialSite;
		}
	}

	public function getSocialSite()
	{
		return $this->_socialSite;
	}

	public function setMaps($_maps)
	{
		if(!empty($_maps)) {
			$this->_maps = $_maps;
		}
	}

	public function getMaps()
	{
		return $this->_maps;
	}

	public function init() {
		$this->setTitle('Profile Fields Mapping');
		$this->loadDefaultDecorators();
		$this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can map the Profile Fields for users of your site with the parameters returning from other social sites.<br/>Click on the Profile type you want to do mapping for from below available Profile types on your site and then choose the appropriate parameter from the drop-downs for respective Profile fields."
)))
			->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
			->setAttrib('name', 'Siteloginconnect_map_profilefields')
			->setAttrib('class', 'global_form')
			->getDecorator('Description')->setOption('escape', false);

        $options = Engine_Api::_()->siteloginconnect()->getSocialSiteFields();

        $this->addElement("Select", "social_site", array(
        	"description" => "Please select the social site that you want to use for profile fields mapping.",
        	"label" => "Social Site",
        	"multiOptions" => array("" =>"Select", "linkedin"=>"LinkedIn", "facebook"=>"Facebook", "instagram"=>"Instagram", "twitter"=>"Twitter"),
        	"value" => $this->getSocialSite(),
        	"onchange" => "selectSocialSite(this)",
        ));

        $maps = $this->getMaps();
		foreach ($maps["profile_types"] as $profileType) {

			$this->addElement('Dummy', "dummy_profile_type_{$profileType['option_id']}", array(
                'label' => $profileType["label"],
            ));
            $element = $this->getElement("dummy_profile_type_{$profileType['option_id']}");
            $element->removeDecorator('HtmlTag')
                    ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading accordion');
			$element->getDecorator('Label')->setOption('style', 'font-weight:bold;color: #000;width:100%');
        	$arrayfieldnames=array();
            foreach ( $maps["fieldsMap"][$profileType["option_id"]] as $fields ) {
            	if(array_key_exists($fields['field_id'], $maps["childData"])) {
            		$arrayfieldnames[]="dummy_profile_type_{$profileType['option_id']}_{$fields['field_id']}";
            		$this->addElement('Dummy', "dummy_profile_type_{$profileType['option_id']}_{$fields['field_id']}", array(
                		'label' => $fields["label"],
            		));
            		$element = $this->getElement("dummy_profile_type_{$profileType['option_id']}_{$fields['field_id']}");
            		$element->removeDecorator('HtmlTag')
                    ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
					$element->getDecorator('Label')->setOption('style', 'font-weight:bold;color: #666;display:block;padding:15px 10px;margin:10px 0 15px;');

            		foreach ($maps["childData"][$fields['field_id']] as $key => $value) {
	                	$arrayfieldnames[]="dummy_option_type_{$key}";
	                   	$this->addElement('Dummy', "dummy_option_type_{$key}", array(
	                		'label' => $value["option_label"],
	            		));
	            		$element = $this->getElement("dummy_option_type_{$key}");
            			$element->removeDecorator('HtmlTag')
                    		->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
						$element->getDecorator('Label')->setOption('style', 'color: #000;background-color: #eee; padding: 20px 10px; display: block; width: 100%;');

	                   	foreach ($maps["childData"][$fields['field_id']][$key] as $field_id => $fieldvalue) {
	                   		if($field_id=='option_label'){
	                   			continue;
	                   		}

	                   		$arrayfieldnames[]="field_{$profileType['option_id']}_{$field_id}";
			                $this->addElement("Select", "field_{$profileType['option_id']}_{$field_id}", array(
			                    "label" => $fieldvalue["label"],
			                    'multiOptions' => !empty($options[$this->getSocialSite()]) ? $options[$this->getSocialSite()] : array(""=>"Select"),
			                    'onchange' => "changefieldvalues(this)",
			                ));

			                $el = $this->getElement("field_{$profileType['option_id']}_{$field_id}");
			                $el->removeDecorator('HtmlTag')
			                   ->getDecorator("HtmlTag2")->setOption("class", "form-wrapper profile_type_{$profileType['option_id']}");
			            
	                   	}
                	}
            	} else {
            		$arrayfieldnames[]="field_{$profileType['option_id']}_{$fields['field_id']}";
	                $this->addElement("Select", "field_{$profileType['option_id']}_{$fields['field_id']}", array(
	                    "label" => $fields["label"],
	                    'multiOptions' => !empty($options[$this->getSocialSite()]) ? $options[$this->getSocialSite()] : array(""=>"Select"),
	                    'onchange' => "changefieldvalues(this)",
	                ));

	                $el = $this->getElement("field_{$profileType['option_id']}_{$fields['field_id']}");
	                $el->removeDecorator('HtmlTag')
	                   ->getDecorator("HtmlTag2")->setOption("class", "form-wrapper profile_type_{$profileType['option_id']}");
	            
            	}
            }
            if(!empty($arrayfieldnames)){
            	$this->addDisplayGroup($arrayfieldnames, 'Select_'.$profileType['option_id']);
            } else {
            	$this->removeElement("dummy_profile_type_{$profileType['option_id']}");
            }
            
		}

		$this->addElement("Hidden", "profile_type");

		$this->addElement('button', 'submit-save',
			array(
				'label' => "Save Changes",
				'onclick' => "saveMapping()",
			)
		);
	}

}