<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminIntegrationController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_AdminIntegrationController extends Core_Controller_Action_Admin {

    protected $_skeletonPath;
    protected $_outputPath;

    public function indexAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegateway_admin_main', array(), 'sitegateway_admin_main_integration');

        $this->_outputPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'new-gateway-skeleton';

        $this->_skeletonPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sitegateway' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'new-gateway-skeleton';

        // Require in advance
        require_once 'PEAR.php';
        require_once 'Archive/Tar.php';

        // Form
        $this->view->form = $form = new Sitegateway_Form_Admin_Integration_Create();
        
        $this->view->gatewayNameUc = "<span class='gatewayNameUc'>Newgatewayname</span>";
        $this->view->gatewayNameLc = "<span class='gatewayNameLc'>newgatewayname</span>";            

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();

        // Now let's build it
        $archiveDirectory = $this->_outputPath . DIRECTORY_SEPARATOR .
                $values['name'];

        if (file_exists($archiveDirectory)) {
            Engine_Package_Utilities::fsRmdirRecursive($archiveDirectory, true);
        }

        $archiveFilename = $this->_outputPath . DIRECTORY_SEPARATOR .
                $values['name'] . '.tar';

        $archive = new Archive_Tar($this->_outputPath . DIRECTORY_SEPARATOR .
                $values['name'] . '.tar');
        $archive->setIgnoreList(array('CVS', '.svn'));

        // Prepare search and replace
        $searchAndReplace = array(
            'newgatewayname' => strtolower($values['name']),
            'Newgatewayname' => ucfirst($values['name']),
        );
        $search = array_keys($searchAndReplace);
        $replace = array_values($searchAndReplace);

        // Build skeleton directory
        //$path = 'application/modules';
        $skeleton_path = $this->_skeletonPath;

        $filesArray = array();
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($skeleton_path), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if (!$file->isFile())
                continue;
            if (FALSE !== stripos($file->getPathname(), '.svn'))
                continue;

            $filename = $file->getPathname();

            $target_filename = ltrim(str_replace($skeleton_path, '', $filename), '/\\');
            $target_filename = str_replace($search, $replace, $target_filename);

            $target_data = file_get_contents($filename);
            $target_data = str_replace($search, $replace, $target_data);

            $target_path = $archiveDirectory . '/' . $target_filename;

            if (!is_dir(dirname($target_path))) {
                if (!mkdir(dirname($target_path), 0777, true)) {
                    throw new Engine_Exception(sprintf('Unable to create folder: %s', dirname($target_path)));
                }
            }

            if (false === file_put_contents($target_path, $target_data)) {
                throw new Engine_Exception(sprintf('Unable to put data to file: %s', $target_path));
            }

            $filesArray[] = $target_path;
        }

        $modifiedPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'new-gateway-skeleton' . DIRECTORY_SEPARATOR;
        $archive->addModify($filesArray, null, $modifiedPath);

        // Output the archive
        include_once APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license2.php';

        try {
            Engine_Package_Utilities::fsRmdirRecursive($archiveDirectory, true);
        } catch (Exception $e) {
            
        }
        exit();
    }

}
