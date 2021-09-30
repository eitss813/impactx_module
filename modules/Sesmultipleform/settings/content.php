<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
  $data[] = 'Choose a Form';
  if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.pluginactivated')) {
	  $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm(array('fetchAll'=>true,'active'=>true));
		foreach ($paginator as $item) {
			$data[$item['form_id']] = $item['title'];
		}
	}
	$headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
  $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
  return array(
	   array(
        'title' => 'SES - Multiple Form - Form Display on Widgetized Page',
        'description' => 'This widget displays the form as chosen from the edit settings of this widget. The form will be shown directly on the widgetized page.',
        'category' => 'SES - All in One Multiple Forms Plugin',
        'type' => 'widget',
        'name' => 'sesmultipleform.forms',
        'autoEdit' => true,
        'adminForm' => array(
	      'elements' => array(
					array(
						'Select',
						'formtype',
						array(
							'label' => 'Choose the form to be displayed in this widget.',
							'multiOptions' => $data,
						  'value' => 1,
						)
					),
					array(
						'Text',
						'redirect',
						array(
							'label'=>'Enter the URL of the page to which users will be redirected after the form is submitted.',
						  'value' => '',
						)
					),
					array(
						'Select',
						'hideform',
						array(
							'label' => 'Do you want to hide the form after it is successfully submitted?',
							'multiOptions' => array(1=>'Yes, hide the form after successful submission',0=>'No, do not hide the form after successful submission.'),
						  'value' => 1,
						)
					),
	      )
		   ),
	    ),
		 array(
        'title' => 'SES - Multiple Form - Form Display on Button Click in Popup or Page',
        'description' => 'This widget displays a button as configured from the edit settings of this widget to show chosen form. The form can be shown in popup or on specific page. Various settings for button like text, color can be configured.',
        'category' => 'SES - All in One Multiple Forms Plugin',
        'type' => 'widget',
        'name' => 'sesmultipleform.popup',
        'autoEdit' => true,
        'adminForm' => array(
	      'elements' => array(
					array(
						'Text',
						'buttontext',
						array(
							'label'=>'Enter Button Text.',
						  'value' => '',
							'required' => true
						)
					),
					array(
						'Select',
						'formtype',
						array(
							'label' => 'Choose the form to be displayed in this widget.',
							'multiOptions' => $data,
						  'value' => 1,
							'required' => true
						)
					),
					array(
						'Select',
						'position',
						array(
							'label'=>'Choose the placement of button. [This setting will only work, if you place this widget in the header or footer of your website. On other pages, button will be shown at placed position.]',
							'multiOptions' => array('1'=>'In Right Side','2'=>'In Left Side','3'=>'At placed Position'),
						  'value' => '3',
						)
					),
					array(
						'Text',
						'buttoncolor',
						array(
							'class' => 'SEScolor',
							'label'=>'Choose color of the button.',
						  'value' => '#78c744',
						)
					),
					array(
						'Text',
						'texthovercolor',
						array(
							'class' => 'SEScolor',
							'label'=>'Choose color of the button when mouse is hovered on it.',
						  'value' => '#f2134f',
						)
					),
					array(
						'Text',
						'textcolor',
						array(
							'class' => 'SEScolor',
							'label'=>'Choose text color on the button.',
						  'value' => '#ffffff',
						)
					),
					array(
						'Text',
						'margin',
						array(
							'label' => 'Enter value for the top margin. [This setting will work for Left / Right placement of button.]',
						  'value' => '30',
						)
					),
					array(
						'Select',
						'margintype',
						array(
							'label' => 'Choose the unit of margin.',
							'multiOptions' => array('pix'=>'Pixels (px)','per'=>'Percentage (%)'),
						  'value' => 1,
							'required' => true
						)
					),
					array(
						'Select',
						'hideform',
						array(
							'label' => 'Do you want to hide the form after it is successfully submitted?',
							'multiOptions' => array(1=>'Yes, hide the form after successful submission',0=>'No, do not hide the form after successful submission.'),
						  'value' => 1,
						)
					),
					array(
						'Select',
						'popuptype',
						array(
							'label' => 'Do you want to show Form in Popup or Redirect users to Specific Page, when the button of this widget is clicked?',
							'multiOptions' => array(1=>'Show Form in Popup',0=>'Redirect users to Specific Page.'),
						  'value' => 1,
						)
					),
						array(
						'Text',
						'redirectOpen',
						array(
							'label'=>'Enter URL to which users will be redirected.(this setting works only, if you select "Redirect users to Specific Page.")',
						  'value' => '',
						)
					),
					
					array(
						'Select',
						'closepopup',
						array(
							'label' => 'Do you want to close the popup after form is successfully submitted?',
							'multiOptions' => array(1=>'Yes, close the popup.',0=>'No, do not close popup.'),
						  'value' => 1,
						)
					),
					array(
						'Text',
						'redirect',
						array(
							'label'=>'Enter the URL of the page to which users will be redirected after the form is submitted.',
						  'value' => '',
						)
					),
	      )
		   ),
	    ),
	   array(
        'title' => 'SES - Multiple Form - Google Map for Location',
        'description' => 'This widget displays the location in Google Map. You can also enter your quick details which will show attractively on the map.',
        'category' => 'SES - All in One Multiple Forms Plugin',
        'type' => 'widget',
        'name' => 'sesmultipleform.locations',
        'autoEdit' => true,
				'adminForm' => 'Sesmultipleform_Form_Admin_Location',
	    ), 
	   array(
        'title' => 'SES - Multiple Form - Banner Image',
        'description' => 'This widget displays a banner as chosen from the edit settings of this widget. Image to be chosen in this widget should be first uploaded from the "Layout" >> "File & Media Manager" section. This widget can be placed anywhere multiple times on a single or separate pages on your website.',
        'category' => 'SES - All in One Multiple Forms Plugin',
        'type' => 'widget',
				'autoEdit' => true,
        'name' => 'sesmultipleform.banner',
        'adminForm' => 'Sesmultipleform_Form_Admin_Banner',
    ),
        array(
        'title' => 'SES - Multiple Form - Key Contacts',
        'description' => 'This widget displays the Key Contact persons of your website as chosen from the "Key Contacts" section of this plugin. You can configure various settings of this widget.',
        'category' => 'SES - All in One Multiple Forms Plugin',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesmultipleform.list-key-contacts',
        'defaultParams' => array(
            'title' => 'Key Contacts',
        ),
        'adminForm' => array(
            'elements' => array(
								array(
									'Select',
									'listtype',
									array(
										'label' => 'Contacts Order Type',
										'multiOptions' => array(
											'creation' => 'Creation Date',
											'order' => 'Order',
											'random' => 'Random',
										),
										'value' => 'creation',
									)
								),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of member block (in pixels). [This setting will only work, if you choose to place this widget in "Middle / Right Extended / Left Extended / Full width column" from settings below]',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of member block (in pixels). [This setting will only work, if you choose to place this widget in "Middle / Right Extended / Left Extended / Full width column" from settings below]',
                        'value' => 200,
                    )
                ),
                array(
                    'Radio',
                    'nonloggined',
                    array(
                        'label' => 'Do you want to show this widget to non-logged in users?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'emailshow',
                    array(
                        'label' => 'Do you want to show email ids of the key contact members in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'blockposition',
                    array(
                        'label' => 'Choose the column of the page on which you are placing this widget.',
                        'multiOptions' => array(
                            1 => 'Left / Right Column',
                            0 => 'Middle / Right Extended / Left Extended / Full width column'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count (number of contacts to show)',
                        'value' => 3,
                    )
                ),
            ),
        ),
    ),
  )
?>
