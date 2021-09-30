<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/31/2016
 * Time: 11:01 AM
 */
class Yndynamicform_AdminImportExportController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_import-export');
    }

    public function indexAction()
    {
        if ($this -> getRequest() -> isPost()) {
            $values = $this -> getRequest() -> getPost();
            if (empty($values['export_form']) || (int)$values['export_form'] <= 0) {
                $this->view->error = $this->view->translate('Please select at least one form to export');
            } else {
                $yndform = Engine_Api::_() -> getItem('yndynamicform_form', (int)$values['export_form']);
                if ($yndform) {
                    $this->export($yndform);
                }
                else {
                    $this->view->error = $this->view->translate('The form you are looking for not found');
                }
            }
        }

        $params = $this -> _getAllParams();
        $params['valid_form'] = true;
        $table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
        $this -> view -> paginator = $paginator = $table -> getFormsPaginator($params);

        $this -> view -> paginator -> setItemCountPerPage(10);
        $page = $this -> _getParam('page', 1);
        $this -> view -> paginator -> setCurrentPageNumber($page);
    }

    public function importAction()
    {
        $this -> view -> form = $form = new Yndynamicform_Form_Admin_Import();
        if ($this -> getRequest() -> isPost() && $form->isValid($this->getRequest()->getParams())) {
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $file = $_FILES['file_import'];
            move_uploaded_file($file['tmp_name'], "$path/". $file['name']);
            if (file_exists("$path/". $file['name'])) {
                $xml = simplexml_load_file("$path/". $file['name']);

                $db = Engine_Db_Table::getDefaultAdapter();
                $db -> beginTransaction();

                try {
                    // Import yndform
                    $yndform_xml = $xml->yndform;
                    $yndform_values = (array) $yndform_xml;
                    $yndform_values = $this -> validateForm($yndform_values);

                    if (is_array($yndform_values) && !is_null($yndform_values)) {
                        $yndform_table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
                        $yndform = $yndform_table -> createRow();
                        $yndform -> user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
                        $yndform -> setFromArray($yndform_values);
                        $optionId = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($yndform->title);
                        $yndform->option_id = $optionId;
                        $yndform -> save();

                        // Auth
                        $auth = Engine_Api::_()->authorization()->context;
                        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                        foreach ($roles as $i => $role)
                        {
                            $auth->setAllowed($yndform, $role, 'view', 1);
                            $auth->setAllowed($yndform, $role, 'comment', 1);
                            $auth->setAllowed($yndform, $role, 'submission', 1);
                        }

                        // Import confirmation
                        $confirmation_xml = $xml->confirmations;
                        $confirmations = (array)json_decode(json_encode($confirmation_xml));
                        $confirmations = $this -> validateConfirmations($confirmations);
                        $confirmation_table = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform');
                        foreach ($confirmations as $item)
                        {
                            $confirmation = $confirmation_table -> createRow();
                            $confirmation -> form_id = $yndform -> getIdentity();
                            $confirmation -> setFromArray($item);
                            $confirmation -> save();
                        }

                        // Import notification
                        $notification_xml = $xml->notifications;
                        $notifications = (array)json_decode(json_encode($notification_xml));
                        $notifications = $this -> validateNotifications($notifications);
                        $notification_table = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform');
                        if (is_array($notifications) && !empty($notifications)) {
                            foreach ($notifications as $item)
                            {
                                $notification = $notification_table -> createRow();
                                $notification -> form_id = $yndform -> getIdentity();
                                $notification -> setFromArray($item);
                                $notification -> save();
                            }
                        }

                        // Import fields
                        $fields_xml = $xml->allfields;
                        $fields = (array)json_decode(json_encode($fields_xml));
                        $fields = $this -> validateAllfields($fields);

                        $page_break_config = $fields['page_break_config'];

                        // Set page break config to form
                        if (!empty($page_break_config) && !is_null($page_break_config)) {
                            $yndform -> page_break_config = $page_break_config;
                            $yndform -> save();
                        }

                        $fields = $fields['fields'];

                        if (is_array($fields) && !empty($fields)) {
                            $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                            foreach ($fields as $key => $item)
                            {
                                $field = Engine_Api::_()->fields()->createField('yndynamicform_entry', array_merge(array(
                                    'option_id' => ( is_object($yndform) ? $yndform->option_id : '0' ),
                                ), $item));
                                if (isset($item['options']) && !empty($item['options'])) {
                                    foreach ($item['options'] as $option) {
                                        // Create new option
                                        Engine_Api::_()->fields()->createOption('yndynamicform_entry', $field, array(
                                            'label' => $option,
                                        ));
                                    }
                                }
                                $map = $mapData -> getRowMatching('child_id', $field -> field_id);
                                $map -> order = $key + 1;
                                $map -> save();
                            }
                        }

                        $db -> commit();
                    } else {
                        $db -> rollBack();
                        $this -> view -> form = new Yndynamicform_Form_Admin_Import();
                        echo 'An error has occurred';
                        return;
                    }
                } catch (Exception $e) {
                    $db -> rollBack();
                    throw $e;
                }

                $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Import successfully.');
                return $this -> _forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                    'messages' => Array($this -> view -> message)
                ));
            } else {
                echo 'Failed to open test.xml.';
            }
            @unlink("$path/". $file['name']);
            die;
        }
    }

    public function validateForm($values)
    {
        $isValid = true;
        // Check
        $main_info = new Yndynamicform_Form_Admin_NewForm();
        $main_info->removeElement('photo');
        if (!$main_info -> isValid($values)) {
            $isValid = false;
        }
        $form_setting = new Yndynamicform_Form_Admin_EditForm_FormSettings();

        // Speacial value for privacy
        $privacy = $values['privacy'];
        $values['privacy'] = array(1 & $privacy,2 & $privacy);

        if (!$form_setting -> isValid($values)) {
            $isValid = false;
        }
        $values = array_merge($main_info -> getValues(), $form_setting -> getValues());
        unset($values['valid_from_date'],$values['valid_to_date'],$values['unlimited_time'],$values['status']);
        // Privacy
        if (count($values['privacy']) > 1) {
            $values['privacy'] = 3;
        } elseif (!empty($values['privacy'])) {
            $values['privacy'] = $values['privacy'][0];
        }
        if ($isValid) return $values; else return null;
    }

    public function validateConfirmations($values)
    {
        $confirmations = array();
        $form = new Yndynamicform_Form_Admin_EditForm_Confirmation();
        foreach ($values as $key => $value)
        {
            if ($form -> isValid((array) $value)) {
                $val = $form -> getValues();
                $val['conditional_enabled'] = $value -> conditional_enabled;
                $val['conditional_show'] = $value -> conditional_show;
                $val['conditional_logic'] = $value -> conditional_logic;
                $val['conditional_scope'] = $value -> conditional_scope;
                $confirmations[] = array_filter($val);
            }
        }
        return $confirmations;
    }

    public function validateNotifications($values)
    {
        $notifications = array();
        $form = new Yndynamicform_Form_Admin_EditForm_Notification();
        foreach ($values as $key => $value)
        {
            if ($form -> isValid((array) $value)) {
                $val = $form -> getValues();
                $val['conditional_enabled'] = $value -> conditional_enabled;
                $val['conditional_show'] = $value -> conditional_show;
                $val['conditional_logic'] = $value -> conditional_logic;
                $val['conditional_scope'] = $value -> conditional_scope;
                $notifications[] = array_filter($val);
            }
        }

        return $notifications;
    }

    public function validateAllfields($values)
    {
        $fields = array();
        $options = array();
        $page_break_config = null;
        foreach ($values as $value)
        {
            $value = (array) $value;

            // Check type param and get form class
            $cfType = $value['type'];
            $adminFormClass = null;
            if( !empty($cfType) ) {
                $adminFormClass = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'adminFormClass');
            }
            if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
                $adminFormClass = 'Yndynamicform_Form_Admin_Field';
            }
            if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
                $page_break_config = $value['config'];
            }
            // Create form
            $form = new $adminFormClass();
            $config =(array) json_decode($value['config']);
//            if (!is_null($value['options'])) {
//                $value['options'] = (array)$value['options'];
//            }
            foreach ($config as $k => $v) {
                $value[$k] = $v;
            }

            if ($form->isValid($value)) {
                $field = $form -> getValues();
                $field['options'] = (array) $value['options'];
                $field = array_filter($field);
                $fields[] = $field;
            }
        }
        return array(
            'fields' => $fields,
            'page_break_config' => $page_break_config,
        );
    }

    public function array_to_xml($data, &$xml_data) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this -> array_to_xml($value, $subnode);
            } else {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                if (!empty($value) || is_numeric($value)) $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

    public function export($yndform)
    {
        if ($yndform instanceof Yndynamicform_Model_Form) {
            // initializing or creating array
            $data = array('yndform' => $yndform -> toArray());

            $moderators = $yndform -> getAllModeratorsID();
            // push moderators
            $data = array_merge($data, array('moderators' => json_encode($moderators)));

            // push all fields
            $fields = Engine_Api::_() -> yndynamicform() -> getAllFieldsToArray($yndform -> option_id);
            $data = array_merge($data, array('allfields' => $fields));

            // push all notification
            $notifications = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform') -> getNotifications(array('form_id' => $yndform -> getIdentity()));
            $arrNotification = array();
            foreach ($notifications as $item) {
                $arrNotification[] = $item -> toArray();
            }
            $data = array_merge($data, array('notifications' => $arrNotification));

            // push all confirmation
            $confirmations = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform') -> getConfirmations(array('form_id' => $yndform -> getIdentity()));
            $arrConfirmation = array();
            foreach ($confirmations as $item) {
                $arrConfirmation[] = $item -> toArray();
            }
            $data = array_merge($data, array('confirmations' => $arrConfirmation));

            // Remove all element null value
            $data = array_filter($data);

            // creating object of SimpleXMLElement
            $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

            // function call to convert array to xml
            $this -> array_to_xml($data,$xml_data);

            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $file_path = "$path/". "dynamicform_". $yndform -> getIdentity(). ".xml";
            //saving generated xml file;
            $result = $xml_data->asXML($file_path);

            header("Content-Disposition: attachment; filename=" . urlencode(basename($file_path)), true);
            header('Content-type: text/xml');
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            header("Content-Length: " . filesize($file_path), true);
            flush();

            $fp = fopen($file_path, "r");
            while( !feof($fp) )
            {
                echo fread($fp, 65536);
                flush();
            }
            fclose($fp);
            @unlink($file_path);
            die;
        } else {
            exit();
        }
    }
}