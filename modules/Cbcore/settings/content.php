<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
 
 // Get available files
    $logoOptions = array('' => 'Text-only (No logo)');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() ) continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) ) continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) ) continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }
    
return array( 
  array (
    'type' => 'widget',
    'name' => 'Cbcore.cb-mini-menu',
    'title' => 'CB Mini Menu',
    'description' => 'CB Mini Menu',
    'category' => 'Consecutive Bytes Core Module',
    'author' => 'Consecutive Bytes',
    'autoEdit' => true,
    'adminForm' => array(
  	'elements' => array(
		      array(
			      'select',
			      'logo',
			      array(
				      'label' => 'Select an Image',
				      'multioptions' => $logoOptions
			      )
		      )
	      )
      )
    ),
	array(
	  'type' => 'widget',
	  'name' => 'Cbcore.cb-top-members',
	  'version' => '4.8.7',
	  'title' => 'CB Top Members',
	  'description' => 'CB Top Members',
      'category' => 'Consecutive Bytes Core Module',
      'author' => 'Consecutive Bytes',
	  'autoEdit' => true,
    'adminForm' => array(
  	'elements' => array(
		      array(
			      'text',
			      'itemcount',
			      array(
				      'label' => 'Number of members to show in widget',
				      'multioptions' => array('1' => 'Yes','0' => 'No')
			      )
		      ),
          array(
			      'select',
			      'photo',
			      array(
				      'label' => 'Show members only with photos?',
				      'multioptions' => array('1' => 'Yes','0' => 'No')
			      )
		      )
	      )
      )
	),
    array (
    'type' => 'widget',
    'name' => 'Cbcore.cb-main-menu',
    'title' => 'CB Main Menu',
    'description' => 'CB Main Menu',
    'category' => 'Consecutive Bytes Core Module',
    'author' => 'Consecutive Bytes',
    'autoEdit' => true,
    'adminForm' => array(
  	'elements' => array(
		      array(
			      'select',
			      'search',
			      array(
				      'label' => 'Show Search?',
				      'multioptions' => array('1' => 'Yes','0' => 'No')
			      )
		      ),
          array(
			      'select',
			      'itemcount',
			      array(
				      'label' => 'Menus before more',
				      'multioptions' => array(1,2,3,4,5,6,7,8,9)
			      )
		      )
	      )
      )
    ),
) ?>