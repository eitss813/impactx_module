<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Location.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Location extends Engine_Form {
  public function init() {    
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$this->addElement('Text', 'location', array(
				'label' => 'Enter the Location to be highlighted in this widget. [An attractive marker will be shown on the entered location in Google Map.]',
				 'id' => 'locationSesmultipleform',
		));
    $this->addElement('Text', 'lat', array(
				'label' => 'Latitude. [Will be automatically detected based on location.]',
				'id' => 'latSesmultipleform',
		));
    $this->addElement('Text', 'lng', array(
					'label' => 'Longitude. [Will be automatically detected based on location.]',
					'id' => 'lngSesmultipleform',
		));	
		
	
		
		$this->addElement('Select', 'mapzoom', array(
			'label' => $view->translate('Choose the default zoom level of Google Map.'),
			'multiOptions' => array(
					'1' => "1",
					"2" => "2",
					"4" => "4",
					"6" => "6",
					"8" => "8",
					"10" => "10",
					"12" => "12",
					"14" => "14",
					"16" => "16",
					"17" => "17"
			),
			'value' => '10',
		));
		$this->addElement('Text', 'height', array(
                        'label' => 'Enter the height of this widget (in pixels).',
                        'value' => 200,
                         'validators' => array(
                             array('Int', true),
                             array('GreaterThan', true, array(0)),
                         )
                    ));	
		$this->addElement('Select', 'quickContact', array(
			'label' => $view->translate('Do you want to enter your quick contact details in this widget.'),
			'multiOptions' => array(
					1 => 'Yes',
					0=> 'No'
			),
			'onchange'=>'showHideForm(this.value);return false;',
			'value' => '1',
		));
		$headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');
		$script='
		function showHideForm(value){
			if(value == 1){
					sesJqueryObject("#aboutdescr-wrapper").show();
					sesJqueryObject("#address-wrapper").show();
					sesJqueryObject("#email-wrapper").show();
					sesJqueryObject("#phone-wrapper").show();
					sesJqueryObject("#skype-wrapper").show();
					sesJqueryObject("#company-wrapper").show();
					sesJqueryObject("#facebook-wrapper").show();
					sesJqueryObject("#twitter-wrapper").show();
					sesJqueryObject("#youtube-wrapper").show();
					sesJqueryObject("#linkdin-wrapper").show();
					sesJqueryObject("#googleplus-wrapper").show();
					sesJqueryObject("#rssfeed-wrapper").show();
					sesJqueryObject("#pinterest-wrapper").show();
					sesJqueryObject("#dummy1-wrapper").show();
			}else{
					sesJqueryObject("#aboutdescr-wrapper").hide();
					sesJqueryObject("#address-wrapper").hide();
					sesJqueryObject("#email-wrapper").hide();
					sesJqueryObject("#phone-wrapper").hide();
					sesJqueryObject("#skype-wrapper").hide();
					sesJqueryObject("#company-wrapper").hide();
					sesJqueryObject("#facebook-wrapper").hide();
					sesJqueryObject("#twitter-wrapper").hide();
					sesJqueryObject("#youtube-wrapper").hide();
					sesJqueryObject("#linkdin-wrapper").hide();
					sesJqueryObject("#googleplus-wrapper").hide();
					sesJqueryObject("#rssfeed-wrapper").hide();
					sesJqueryObject("#pinterest-wrapper").hide();
					sesJqueryObject("#dummy1-wrapper").hide();
			}
		}
		sesJqueryObject(document).ready(function(){
				var params = parent.pullWidgetParams();
				sesJqueryObject("#locationSesmultipleform").val(params["location"]);
				sesJqueryObject("#latSesmultipleform").val(params["lat"]);
				sesJqueryObject("#lngSesmultipleform").val(params["lng"]);
				sesJqueryObject("#height").val(params["height"]);
				showHideForm(sesJqueryObject("#quickContact").val());
		})';
		$view->headScript()->appendScript($script);
		$this->addElement('Dummy', "dummy1", array(
			 'label' => "<span style='font-weight:bold;'>Quick Contact Details</span>",
    ));
		$this->getElement('dummy1')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		
		$this->addElement('Textarea', 'aboutdescr', array(
				 'label' => 'Enter a brief description about your website.',
         'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',                    
			));
		$this->addElement('Textarea', 'address', array(
				 'label' => 'Enter your address.',
         'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',                
			));
		$this->addElement('Text', 'email', array(
				 'label' => 'Enter your email id.',
         'value' => 'info@example.com',                
			));
		$this->addElement('Text', 'phone', array(
				 'label' => 'Enter your phone number.',
         'value' => '(+2)00000000000',                
			));
		$this->addElement('Text', 'skype', array(
				'label' => 'Enter your skype id.',
        'value' => 'skype.id',               
			));
		$this->addElement('Text', 'company', array(
				'label' => 'Enter your company registration info.',
        'value' => 'skype.id',               
			));
		$this->addElement('Text', 'facebook', array(
				'label' => 'Enter your Facebook Page URL.',
        'value' => 'http://www.facebook.com',               
			));
		$this->addElement('Text', 'twitter', array(
				'label' => 'Enter your Twitter Page URL.',
        'value' => 'http://www.twitter.com',               
			));
		$this->addElement('Text', 'youtube', array(
				'label' => 'Enter your YouTube Channel URL.',
				'value' => 'http://www.youtube.com',         
			));	
		$this->addElement('Text', 'linkdin', array(
				'label' => 'Enter your LinkedIn Page URL.',
				'value' => 'http://www.linkedin.com',               
			));
		$this->addElement('Text', 'googleplus', array(
				'label' => 'Enter your Google Plus Page URL.',
				'value' => 'plus.google.com',           
			));
		$this->addElement('Text', 'rssfeed', array(
				'label' => 'Enter your RSS feed URL.',
				'value' => 'http://feeds.feedburner.com',              
			));
		$this->addElement('Text', 'pinterest', array(
				'label' => 'Enter your Pinterest Board URL.',
				'value' => 'http://www.pinterest.com',               
			));	
		
   $this->addElement('dummy', 'location-data', array(
		'decorators' => array(array('ViewScript', array(
				'viewScript' => 'application/modules/Sesmultipleform/views/scripts/location.tpl',
	 )))
  ));
  }
}
