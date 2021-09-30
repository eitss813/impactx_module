<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminThemesController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminThemesController extends Core_Controller_Action_Admin
{

	public function init()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_theme_custom');
		$this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_theme_custom', array(), 'sitecoretheme_admin_theme_custom_color');
	}

	public function indexAction()
	{

		// Get themes
		$themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->fetchAll();
		$activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
		// Install any themes that are missing from the database table
		$reload_themes = false;
		foreach( glob(APPLICATION_PATH . '/application/themes/sitecoretheme/*', GLOB_ONLYDIR) as $dir ) {
			if( file_exists("$dir/manifest.php") && is_readable("$dir/manifest.php") && file_exists("$dir/theme.css") && is_readable("$dir/theme.css") ) {
				$name = basename($dir);
				if( !$themes->getRowMatching('name', $name) ) {
					$meta = include("$dir/manifest.php");
					$row = $themes->createRow();
					// @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
					if( isset($meta['package']['meta']) ) {
						$meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
						unset($meta['package']['meta']);
					}

					$row->title = $meta['package']['title'];
					$row->name = $name;
					$row->description = isset($meta['package']['description']) ? $meta['package']['description'] : '';
					$row->active = 0;
					$row->save();
					$reload_themes = true;
				}
			}
		}
		foreach( $themes as $theme ) {
			if( !is_dir(APPLICATION_PATH . '/application/themes/sitecoretheme/' . $theme->name) ) {
				$theme->delete();
				$reload_themes = true;
			}
		}
		if( $reload_themes ) {
			$themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->fetchAll();
			$activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
			if( empty($activeTheme) ) {
				$themes->getRow(0)->active = 1;
				$themes->getRow(0)->save();
				$activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
			}
		}

		// Process each theme
		$manifests = array();
		$writeable = array();
		$modified = array();
		$colorVariants = array();
		$hasLess = defined('_ENGINE_HAS_VENDOR') ? true : false;
		foreach( $themes as $key => $theme ) {
			// Get theme manifest
			$themePath = "application/themes/sitecoretheme/{$theme->name}";
			$manifest = @include APPLICATION_PATH . "/$themePath/manifest.php";
			if( !is_array($manifest) ) {
				$manifest = array(
					'package' => array(),
					'files' => array()
				);
			}
			// sort($manifest['files']);
			// Pre-check manifest thumb
			// @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
			if( isset($manifest['package']['meta']) ) {
				$manifest['package'] = array_merge($manifest['package']['meta'], $manifest['package']);
				unset($manifest['package']['meta']);
			}

			if( !isset($manifest['package']['thumb']) ) {
				$manifest['package']['thumb'] = 'thumb.jpg';
			}
			$thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['thumb']);
			if( file_exists(APPLICATION_PATH . "/$themePath/$thumb") ) {
				$manifest['package']['thumb'] = "$themePath/{$thumb}";
			} else {
				$manifest['package']['thumb'] = null;
			}

			// Check if theme files are writeable
			$writeable[$theme->name] = false;
			try {
				foreach( array_merge(array(''), $manifest['files']) as $key => $file ) {
					if( !file_exists(APPLICATION_PATH . "/$themePath/$file") ) {
						throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['title']);
					} else {
						$this->checkWriteable(APPLICATION_PATH . "/$themePath/$file");
					}
				}
				$writeable[$theme->name] = true;
			} catch( Exception $e ) {
				if( $activeTheme->name == $theme->name ) {
					$this->view->errorMessage = $e->getMessage();
				}
			}

			// Check if theme files have been modified
			$modified[$theme->name] = array();
			foreach( $manifest['files'] as $path ) {
				$originalName = 'original.' . $path;
				if( file_exists(APPLICATION_PATH . "/$themePath/$originalName") ) {
					if( file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/$path") ) {
						$modified[$theme->name][] = $path;
					}
				}
			}

			// Child themes (color variants)
			if( isset($manifest['colorVariants']) ) {
				foreach( $manifest['colorVariants'] as $key => $val ) {
					$colorVariants[$key] = array(
						'version' => $manifest['package']['version'],
						'parentTheme' => $theme->name,
					);
				}
			}

			$manifests[$theme->name] = $manifest;
		}

		$this->view->manifest = $manifests;
		$this->view->writeable = $writeable;
		$this->view->modified = $modified;
		$this->view->colorVariants = $colorVariants;

		foreach( $manifests[$activeTheme->name]['files'] as $key => $file ) {
			if( substr($file, -5) == '.less' && !$hasLess ) {
				unset($manifests[$activeTheme->name]['files'][$key]);
			}
		}

		$manifests[$activeTheme->name]['files'] = array_values($manifests[$activeTheme->name]['files']); 

		//FETCH THE SEPERATE RESULTS TO SHOW THEM IN SEPERATE SECTIONS
		$this->view->customThemes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->getThemes(array('type' => 0, 'themeIdDesc' => true));
		$this->view->defaultWhiteHeaderThemes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->getThemes(array('type' => 1));
		$this->view->defaultLightThemes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->getThemes(array('type' => 2));
		$this->view->defaultDarkThemes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->getThemes(array('type' => 3));
    $this->view->defaultDoubleColorThemes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->getThemes(array('type' => 4));
	}

	public function changeAction()
	{
		$themeName = $this->_getParam('theme');
		$themeTable = Engine_Api::_()->getDbtable('themes', 'sitecoretheme');
		$themeSelect = $themeTable->select()
			->orWhere('theme_id = ?', $themeName)
			->orWhere('name = ?', $themeName)
			->limit(1)
		;
		$theme = $themeTable->fetchRow($themeSelect);

		if( $theme && $this->getRequest()->isPost() ) {
			$db = $themeTable->getAdapter();
			$db->beginTransaction();

			try {
				$themeTable->update(array(
					'active' => 0,
					), array(
					'1 = ?' => 1,
				));
				$theme->active = true;
				$theme->save();

				// clear scaffold cache
				Core_Model_DbTable_Themes::clearScaffoldCache();

				// Increment site counter
				$settings = Engine_Api::_()->getApi('settings', 'core');
				$settings->core_site_counter = $settings->core_site_counter + 1;

				$db->commit();
			} catch( Exception $e ) {
				$db->rollBack();
				throw $e;
			}
		}

		return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
	} 
	
	public function cloneAction()
	{
		$themes = Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->fetchAll();
		$form = $this->view->form = new Sitecoretheme_Form_Admin_Themes_Clone();
		$viewer = Engine_Api::_()->user()->getViewer();
		$theme_array = array();
		foreach( $themes as $theme ) {
			$themeTitle = $theme->title;
			if($theme->type == 2) {
				$themeTitle = $theme->title . "(Dark)";
			}  
			$theme_array[$theme->name] = $themeTitle;
		}
		$form->getElement('clonedname')->setMultiOptions($theme_array)->setValue($this->_getParam('name'));

		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$orig_theme = $this->_getParam('clonedname');
			if( !($row = $themes->getRowMatching('name', $orig_theme)) ) {
				throw new Engine_Exception("Theme not found: " . $this->_getParam('clonedname'));
			}

			$new_theme = array(
				'name' => 'custom-' . preg_replace('/[^a-z-0-9_]/', '', strtolower($this->_getParam('title'))),
				'title' => $this->_getParam('title'),
				'description' => $this->_getParam('description'),
			);
			$orig_dir = APPLICATION_PATH . '/application/themes/sitecoretheme/' . $orig_theme;
			$new_dir = dirname($orig_dir) . '/' . $new_theme['name'];

			Engine_Package_Utilities::fsCopyRecursive($orig_dir, $new_dir);
			chmod($new_dir, 0777);
			foreach( self::rscandir($new_dir) as $file ) {
				chmod($file, 0777);
			}

			$meta = include("$new_dir/manifest.php");
			// @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
			if( isset($meta['package']['meta']) ) {
				$meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
				unset($meta['package']['meta']);
			}

			$meta['package']['name'] = $new_theme['name'];
			$meta['package']['version'] = null;
			$meta['package']['path'] = substr($new_dir, 1 + strlen(APPLICATION_PATH));
			$meta['package']['title'] = $new_theme['title'];
			$meta['package']['description'] = $new_theme['description'];
			$meta['package']['author'] = $viewer->displayname;
			$meta['package']['directories'][0] = substr($new_dir, 1 + strlen(APPLICATION_PATH));
			file_put_contents("$new_dir/manifest.php", '<?php return ' . var_export($meta, true) . '; ?>');

			try {
				Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->createRow(array(
					'name' => $new_theme['name'],
					'title' => $new_theme['title'],
					'description' => $new_theme['description'],
				));
				Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->insert(array(
					'name' => $new_theme['name'],
					'title' => $new_theme['title'],
					'description' => $new_theme['description'],
				));
			} catch( Exception $e ) { /* do nothing */
			}

			$this->_helper->redirector->gotoRoute(array('action' => 'index'));
		}
	}

	public function updateColorsAction()
	{
		$themeName = $this->_getParam('name', '');
		$form = $this->view->form = new Sitecoretheme_Form_Admin_Themes_EditColors(array('theme' => $themeName));
		if( !$this->getRequest()->isPost() ) {
			$this->view->status = false;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
			return;
		}
		if( !$this->getRequest()->isPost() || !$form->isValid($this->_getAllParams()) ) {
			return;
		}

		// Get theme
		$themeTable = Engine_Api::_()->getDbtable('themes', 'sitecoretheme');
		$themeSelect = $themeTable->select()
			->where('name = ?', $themeName)
			->limit(1)
		;
		$theme = $themeTable->fetchRow($themeSelect);

		if( !$theme ) {
			$this->view->status = false;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_("Missing theme");
			return;
		}

		// Check file
		$basePath = APPLICATION_PATH . '/application/themes/sitecoretheme/' . $theme->name;
		$manifestData = include $basePath . '/manifest.php';
		$file = 'colorConstants.css';
		$fullFilePath = $basePath . '/' . $file;
		try {
			$this->checkWriteable($fullFilePath);
		} catch( Exception $e ) {
			$this->view->status = false;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_("Not writeable");
			return;
		}

		// Check for original file (try to create if not exists)
		if( !file_exists($basePath . '/original.' . $file) ) {
			if( !copy($fullFilePath, $basePath . '/original.' . $file) ) {
				$this->view->status = false;
				$this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
				return;
			}
			chmod("$basePath/original.$file", 0777);
		}
		$values = $form->getValues();
		$body = '';
		$updateMethod = $values['sitecoretheme_update_method'];
		if( $values['sitecoretheme_update_method'] == 'group' ) {
			foreach( $form->getColorConstants() as $key => $colorvalue ) {
				$name = 'spwgroupcolor-' . md5($colorvalue);
				if( isset($values[$name]) ) {
					$colorvalue = $values[$name];
				}
				$body .= $key . ':' . $colorvalue . ';' . "\n";
			}
		} else {
			foreach( $form->getColorConstants() as $key => $value ) {
				$body .= $key . ':' . $values[$key] . ';' . "\n";
			}
		}
		// Now lets write the custom file
		if(!$body || !file_put_contents($fullFilePath, $body) ) {
			$this->view->status = false;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Could not save contents');
			return;
		}

		// clear scaffold cache
		Core_Model_DbTable_Themes::clearScaffoldCache();

		// Increment site counter
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$settings->core_site_counter = $settings->core_site_counter + 1;
		$form = $this->view->form = new Sitecoretheme_Form_Admin_Themes_EditColors(array('theme' => $themeName, 'updateMethod' => $updateMethod));
		$this->view->status = true;
	} 

	public function deleteAction()
	{
		$this->view->name = $this->_getParam('name');
		$dir = APPLICATION_PATH . '/application/themes/sitecoretheme/' . $this->_getParam('name');
		$manifest = require($dir . '/manifest.php');
		$packageFile = APPLICATION_PATH . '/application/packages/theme-' . $manifest['package']['name'] .
			'-' . $manifest['package']['version'] . '.json';
		if( $this->getRequest()->isPost() ) {
			try {
				if( is_dir($dir) ) {
					Engine_Package_Utilities::fsRmdirRecursive($dir, true);
				}

				if( file_exists($packageFile) ) {
					unlink($packageFile);
				}

				Engine_Api::_()->getDbtable('themes', 'sitecoretheme')->delete(array(
					'name = ?' => $this->view->name,
				));

				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array(Zend_Registry::get('Zend_Translate')->_("Scheme deleted successfully"))
				));
			} catch( Exception $e ) {
				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array($e->getMessage())
				));
			}
		}
	}

	public function checkWriteable($path)
	{
		if( !file_exists($path) ) {
			throw new Core_Model_Exception('Path doesn\'t exist');
		}
		if( !is_writeable($path) ) {
			@chmod($path, 0777);
			if( !is_writeable($path) ) {
				throw new Core_Model_Exception('Path is not writeable');
			}
		}
		if( !is_dir($path) ) {
			if( !($fh = fopen($path, 'ab')) ) {
				throw new Core_Model_Exception('File could not be opened');
			}
			fclose($fh);
		}
	}

	
	public static function rscandir($base = '', &$data = array())
	{
		$array = array_diff(scandir($base), array('.', '..')); // remove ' and .. from the array */
		foreach( $array as $value ) { /* loop through the array at the level of the supplied $base */
			if( is_dir("$base/$value") ) { /* if this is a directory */
				$data[] = "$base/$value/"; /* add it to the $data array */
				$data = self::rscandir("$base/$value", $data); /* then make a recursive call with t he
				  current $value as the $base supplying the $data array to carry into the recursion */
			} elseif( is_file("$base/$value") ) { /* else if the current $value is a file */
				$data[] = "$base/$value"; /* just add the current $value to the $data array */
			}
		}
		return $data; // return the $data array
	}
}