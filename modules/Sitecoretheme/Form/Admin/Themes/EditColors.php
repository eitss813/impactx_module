<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditColors.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
# Load the libraries. Do it manually if you don't like this way.
include APPLICATION_PATH . '/application/libraries/Scaffold/libraries/Bootstrap.php';
error_reporting(E_ALL);
ini_set('display_errors', true);

class Sitecoretheme_Form_Admin_Themes_EditColors extends Engine_Form
{

	protected $_theme;
	protected $_updateMethod;

	public function setTheme($theme)
	{
		$this->_theme = $theme;
	}
	public function setUpdateMethod($method)
	{
		$this->_updateMethod = $method;
	}

	public function init()
	{
		$this
			->setTitle('Update Color Scheme')
			->setDescription('Here you can manage color scheme for your website.');
		// Show layout to only logged in users
		$this->addElement('Select', 'sitecoretheme_update_method', array(
			'label' => 'Color Updation Method',
			'description' => "Select the method which you want to use to update color scheme.",
			'multiOptions' => array(
				'single' => 'Update the colors for specific elements solely',
				'group' => 'Update the colors wherever they are being used',
			),
			'value' => ($this->_updateMethod) ? $this->_updateMethod : 'single',
			'onchange' => 'spwThemeUpdateMethod(this.value)'
		));
	 
		$this->addElement('Dummy', 'sitecoretheme_header_constants_label', array(
			'label' => 'Heading Color',
		));
		$this->addElement('Dummy', 'sitecoretheme_footer_constants_label', array(
			'label' => 'Footer Colors',
		));
		$this->addElement('Dummy', 'sitecoretheme_body_constants_label', array(
			'label' => 'Body Colors',
		));
		//ADD HERE THE LEBEL REPLACEMENT FOR THE CONSTANTS
		$constantLebels = array('sitecoretheme_body_header_color' => 'Heading Font Color',
			'sitecoretheme_body_font_color_light' => 'Body Font Color Light',
			'sitecoretheme_body_input_background_color' => 'Input Box Background Color',
			'sitecoretheme_body_input_font_color' => 'Input Box Font Color',
			'sitecoretheme_body_input_border_color' => 'Input Box Border Color',
			'sitecoretheme_body_comments_background_color' => 'Comments Box Background Color',
			'sitecoretheme_body_list_background_color' => 'Listview Background Color',
			'sitecoretheme_body_list_background_color_alt' => 'Listview Background Color Alt',
			'sitecoretheme_body_list_background_color_on_hover' => 'Listview Background Color On Hover',
			'sitecoretheme_header_minimenu_items_background_color' => 'Mini Menu Items Background Color',
			'sitecoretheme_header_minimenu_items_background_color_on_hover' => 'Mini Menu Items Background Color On Hover',
		);
		$colors = array();
		$headerConstants = array();
		$footerConstants = array();
		$bodyConstants = array();
		foreach( $this->getColorConstants() as $name => $value ) {
			$colors[$value] = $value;
			$constantType = explode('_', $name)[1];
			$labelString = $name;
			if($constantType == 'header') {
				$headerConstants[] = $name;
				$labelString = str_replace('sitecoretheme_header_', '', $name);
			} else if($constantType == 'footer') {
				$footerConstants[] = $name;
				$labelString = str_replace('sitecoretheme_footer_', '', $name);
			} else if($constantType == 'body') {
				$bodyConstants[] = $name;
				$labelString = str_replace('sitecoretheme_body_', '', $name);
			}
			
			$labelString = ucwords(preg_replace('/[^a-z-0-9]/', ' ', $labelString));
			if (array_key_exists($name, $constantLebels)) {
				$labelString = $constantLebels[$name];
			}

			$this->addElement('Text', $name, array(
				'label' => ucwords(preg_replace('/[^a-z-0-9]/', ' ', $name)),
				'decorators' => array(array('ViewScript', array(
							'viewScript' => '_formColor.tpl',
							'class' => 'sitecoretheme_constant_color_single_element form element',
							'name' => $name,
							'value' => $value,
							'label' => $labelString,
              'style' => 'width: 24%; display: inline-block; box-sizing: border-box;'
						)))
			));
		}
		$this->addDisplayGroup(array_merge(array('sitecoretheme_header_constants_label'), $headerConstants), 'sitecoretheme_header_constants');
		$this->addDisplayGroup(array_merge(array('sitecoretheme_footer_constants_label'), $footerConstants), 'sitecoretheme_footer_constants');
		$this->addDisplayGroup(array_merge(array('sitecoretheme_body_constants_label'), $bodyConstants), 'sitecoretheme_body_constants');
		foreach( $colors as $name => $value ) {
			$name = 'spwgroupcolor-' . md5($name);
			$this->addElement('Text', $name, array(
				'label' => "Color: " . $value,
				'decorators' => array(array('ViewScript', array(
							'viewScript' => '_formColor.tpl',
							'class' => 'sitecoretheme_constant_color_group_element form element',
							'name' => $name,
							'value' => $value,
							'label' => "Color: " . $value,
              'style' => 'width: 24%; display: inline-block; box-sizing: border-box;'
						)))
			));
		}
		// Add submit button
		$this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
		));
	}

	public function getColorConstants()
	{
		$path = APPLICATION_PATH . '/application/themes/sitecoretheme/' . $this->_theme . '/colorConstants.css';
		$elements = array();
		if( file_exists($path) ) {
			$css = new Scaffold_CSS($path);
			$css->string = '@constants {' . $css->string . '}';
			$found = $css->find_at_group('constants');
			$elements = $found['values'];
		}
		return $elements;
	}

}