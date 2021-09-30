<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLocationController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminLocationController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGE LOCATION
    public function indexAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_shippinglocation');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->delete(array('country LIKE ?' => $value));
                }
            }
        }

        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getRegionsPaginator(array(
            'orderby' => 'country',
        ));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION FOR ADD LOCATION (IN SMOOTHBOX ON MANAGE LOCATION PAGE) 
    public function addLocationAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Location_AddLocation();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        $this->view->regions = array();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                //CHECK REGIONS
                $regions = (array) $this->_getParam('regionsArray');
                $regions = @array_filter(array_map('trim', $regions));
                $regions = @array_unique($regions);
                $regions = @array_slice($regions, 0, 100);
                $regionsArray = array();
                foreach ($regions as $region) {
                    $regionsArray[] = '\'' . $region . '\'';
                }

                $regionStr = @implode(',', $regionsArray);
                $this->view->regions = $regions;

                if (!empty($values['all_regions'])) {
                    $regionStr = '""';
                    $regions = array("");
                }

                $isALLRegionAlreadyExist = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getEmptyRegionCount($values['country']);
                if (!empty($isALLRegionAlreadyExist)) {
                    $form->addError($this->view->translate("ALL Regions / States already enabled for this country. If you want to create region then delete ALL region entry first."));
                    return;
                }


                $dontSaveInDatabase = false;
                $params = array();
                $params['country'] = $values['country'];
                $params['region'] = $regionStr;
                if (!empty($regionStr)) {
                    $regionAlreadyExist = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->isRegionAlreadyExist($params);
                    if (!empty($regionAlreadyExist) && $regionAlreadyExist != 1) {
                        return $form->addError("Entered Regions $regionAlreadyExist already exist.");
                    } else if (!empty($regionAlreadyExist) && $regionAlreadyExist == 1) {
                        $dontSaveInDatabase = true;
                    }
                }

                $regionsTable = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding');
                //TAKING COUNTRIES OBJECT
                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                $saveValues = array();
                foreach ($regions as $region) {
                    if (empty($dontSaveInDatabase)) {
                        $saveValues['country'] = $values['country'];
                        $saveValues['country_name'] = $countries[$values['country']];
                        $saveValues['region'] = $region;
                        $saveValues['status'] = 1;
                        $saveValues['country_status'] = 1;

                        $row = $regionsTable->createRow();
                        $row->setFromArray($saveValues);
                        $row->save();
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Location added successfully.'))
            ));
        }

        $this->renderScript('admin-location/add-location.tpl');
    }

    // ENABLE AND DISABLE REGION ON MANAGE COUNTRIES PAGE
    public function countryenableAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $country = $this->_getParam('country');
        $currentStatus = $this->_getParam('current_status', null);
        $newCounrtyStatus = !$currentStatus;

        $regionObj = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding');

        $db = $regionObj->getAdapter();
        $db->beginTransaction();

        try {
            // CREATE REGION ROW
            $regionObj->update(array('country_status' => $newCounrtyStatus), array('country LIKE ?' => $country));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION DELETE LOCATION ON CLICKING THE DELETE LINK
    public function deleteLocationAction() {
        // IN SMOOTHBOX
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->region_id = $id;

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $id);
                $region->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('location deleted successfully.'))
            ));
        }
    }

    //ACTION FOR IMPORTING DATA FROM CSV FILE
    public function importLocationFileAction() {

        //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->_helper->layout->setLayout('admin-simple');

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Import();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
            $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
            if (!in_array($ext, array('csv', 'CSV'))) {
                $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //START READING DATA FROM CSV FILE
            $fname = $_FILES['filename']['tmp_name'];
            $fp = fopen($fname, "r");

            if (!$fp) {
                echo "$fname File opening error";
                exit;
            }

            $formData = array();
            $formData = $form->getValues();

            if ($formData['import_seperate'] == 1) {
                while ($buffer = fgets($fp, 4096)) {
                    $explode_array[] = explode('|', $buffer);
                }
            } else {
                while ($buffer = fgets($fp, 4096)) {
                    $explode_array[] = explode(',', $buffer);
                }
            }
            //END READING DATA FROM CSV FILE

            $import_count = 0;
            $regionTable = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding');
            foreach ($explode_array as $explode_data) {

                //GET LOCATION DETAILS FROM DATA ARRAY
                $values = array();
                $values['country'] = trim($explode_data[0]);
                $values['status'] = $values['country_status'] = trim($explode_data[1]);

                //IF COUNTRY OR REGION IS EMPTY THEN CONTINUE;
                if (empty($values['country'])) {
                    continue;
                }

                //TAKING COUNTRIES OBJECT
                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                if (!array_key_exists($values['country'], $countries)) {
                    continue;
                }
                if (Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->isCountryExist($values['country'])) {
                    continue;
                }
                $db = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAdapter();
                $db->beginTransaction();

                try {
                    $region = $regionTable->createRow();
                    $region->setFromArray($values);
                    $region->save();

                    //COMMIT
                    $db->commit();

                    $import_count++;
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }

            //CLOSE THE SMOOTHBOX
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => false,
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
            ));
        }
    }

    public function importAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_shippinglocation');
    }

    //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
    public function downloadAction() {
        //GET PATH
        $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitecrowdfunding/settings");

        $path = $this->_getPath();

        if (file_exists($path) && is_file($path)) {
            //KILL ZEND'S OB
            $isGZIPEnabled = false;
            if (ob_get_level()) {
                $isGZIPEnabled = true;
//        while (ob_get_level() > 0) {
                @ob_end_clean();
//        }
            }

            header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/x-tar", true);
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            if (empty($isGZIPEnabled))
                header("Content-Length: " . filesize($path), true);

            readfile("$path");
        }

        exit();
    }

    protected function _getPath($key = 'path') {
        $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitecrowdfunding/settings");
        return $this->_checkPath($this->_getParam($key, ''), $basePath);
    }

    protected function _checkPath($path, $basePath) {
        //SANATIZE
        $path = preg_replace('/\.{2,}/', '.', $path);
        $path = preg_replace('/[\/\\\\]+/', '/', $path);
        $path = trim($path, './\\');
        $path = $basePath . '/' . $path;

        //Resolve
        $basePath = realpath($basePath);
        $path = realpath($path);

        //CHECK IF THIS IS A PARENT OF THE BASE PATH
        if ($basePath != $path && strpos($basePath, $path) !== false) {
            return $this->_helper->redirector->gotoRoute(array());
        }
        return $path;
    }

    public function viewCountriesCodeAction() {
        //TAKING COUNTRIES OBJECT
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $this->view->countriesCode = $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
    }

}
