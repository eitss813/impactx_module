<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Share.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblog_Form_Admin_Share extends Engine_Form {

  public function init() {
			$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    	$fileLink = $view->baseUrl() . '/admin/sesbasic/settings/global';
			$headScript = new Zend_View_Helper_HeadScript();
			$headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');
			$script='
			sesJqueryObject(document).ready(function(){
				var text = sesJqueryObject("#advShareOptions-addThis").parent().find("label").html().replace(/\&lt;/g,"<");
				text = text.replace(/\&gt;/g,">");
				console.log(text);
					sesJqueryObject("#advShareOptions-addThis").parent().find("label").html(text);
			})';
		$view->headScript()->appendScript($script);
      $this->addElement(
					'MultiCheckbox',
					'advShareOptions',
					array(
						'label' => "Choose from below the options to be shown in this widget.",
						'multiOptions' => array(
							'privateMessage' => 'Send as Message [Private Message]',
							'siteShare' => 'Share via Activity Feed [with Message]',
							'quickShare' => 'Quick AJAX Share via Activity Feed',
							'addThis' => 'Add This Share Options [Enter your "Add This Publisher Id" from <a target="_blank" href="' . $fileLink . '">here</a> to enable this option.]',	
						),
					)
				);
  }

}
