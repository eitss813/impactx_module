<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 1:50 PM
 */
class Yndynamicform_FormController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $id = $this -> _getParam('form_id', null);
        if( $id )
        {
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $id);
            if( $yndform )
            {
                Engine_Api::_() -> core() -> setSubject($yndform);

                if (!$this -> _helper -> requireAuth -> setAuthParams($yndform, null, 'view') -> isValid()) {
                    return;
                }
            }
        }
    }

    public function detailAction()
    {
        // Render
        $this -> _helper -> content -> setEnabled();

        if (!$this -> _helper -> requireSubject('yndynamicform_form') -> isValid()) return;

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $yndform = Engine_Api::_() -> core() -> getSubject();

        if (!Engine_Api::_()->authorization()->isAllowed($yndform, $viewer, 'submission')) {
            $this -> view-> error = true;
            $this -> view-> message = 'You do not have permission to submit this form.';
            return;
        }

        if (!$yndform -> isViewable()) {
            $this -> _helper -> requireSubject -> forward();
        }

        if (!$yndform -> isReachedMaximumFormsByLevel()) {
            $this -> view-> error = true;
            $this -> view-> message = 'Number of your submitted forms is maximum. Please try again later or delete some entries for submitting new.';
            return;
        }

        // Increase view count
        $yndform -> view_count += 1;
        $yndform -> save();

        // Get new entry form
        $topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('yndynamicform_entry');
        if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
            $profileTypeField = $topStructure[0] -> getChild();
        }

        $this -> view -> new_entry_form = $new_entry_form = new Yndynamicform_Form_Standard(
            array(
                'item' => new Yndynamicform_Model_Entry(array()),
                'topLevelId' => $profileTypeField -> field_id,
                'topLevelValue' => $yndform -> option_id,
                'mode' => 'create',
            ));

        if (!$yndform -> isSubmittable()) {
            $new_entry_form -> removeElement('submit_button');
        }

        // Get data for conditional logic
        $conditional_params = Engine_Api::_()-> yndynamicform() -> getParamsConditionalLogic($yndform, true);
        $conf_params = Engine_Api::_() -> yndynamicform() -> getConditionalLogicConfirmations($yndform -> getIdentity());
        $noti_params = Engine_Api::_() -> yndynamicform() -> getConditionalLogicNotifications($yndform -> getIdentity());
        $this -> view -> prefix = '1_'.$yndform -> option_id.'_';
        $this -> view -> form = $yndform;
        $this -> view -> fieldsValues = $conditional_params['arrConditionalLogic'];
        $this -> view -> fieldIds = $conditional_params['arrFieldIds'];
        $this -> view -> totalPageBreak = $conditional_params['pageBreak'];
        $this -> view -> arrErrorMessage = $conditional_params['arrErrorMessage'];
        $this -> view -> pageBreakConfigs = $yndform -> page_break_config;
        $this -> view -> doCheckConditionalLogic = true;
        $this -> view -> viewer = $viewer;
        $this -> view -> confConditionalLogic = $conf_params['confConditionalLogic'];
        $this -> view -> confOrder = $conf_params['confOrder'];
        $this -> view -> notiConditionalLogic = $noti_params['notiConditionalLogic'];
        $this -> view -> notiOrder = $noti_params['notiOrder'];

        // Check post
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        /*
         * Cheat: Because some field are not valid conditional logic but admin config they are required.
         *          So we need to ignore them when validate form.
         *          arrayIsValid is very important to make this work. So if some one change it in front-end
         *          will make this not work or work not correctly.
         *        If they are not valid conditional logic we will clear value of them.
         */
        $arrayIsValid = $this -> getRequest() -> getParam('arrayIsValid');
        $arrayIsValid = json_decode($arrayIsValid);

        foreach ($arrayIsValid as $key => $value) {
            if (!$value) {
                $ele = $new_entry_form -> getElement($key);
                if ($ele instanceof Zend_Form_Element)
                    if ($ele -> isRequired()) {
                        $ele = $ele -> setRequired(false);
                        $ele = $ele -> setValue('');
                    }
            }
        }

        // Validate file upload
        if (isset($_FILES) && $viewer -> getIdentity()) {
            $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
            foreach ($_FILES as $key => $value)
            {
                $array_filtered = array_filter($value['name']);
                if (empty($array_filtered) || !count($array_filtered)) continue;

                // Validate file extension
                $field_id = explode('_', $key)[2];
                $map = $mapData -> getRowMatching('child_id', $field_id);
                $field = $map->getChild();

                $max_file = $field->config['max_file'];
                if ($max_file && count($array_filtered) > $max_file) {
                    $new_entry_form -> addError('You have input reached maximum allowed files.');
                    return;
                }

                $allowed_extension = $field->config['allowed_extensions'];
                if ($allowed_extension == '*') continue;
                $allowed_extension = str_replace('.', '', $allowed_extension);
                $allowed_extension = str_replace(' ', '', $allowed_extension);
                $allowed_extension = explode(',', $allowed_extension);

                $max_file_size = $field->config['max_file_size'];
                foreach ($value['name'] as $k => $filename) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!in_array($ext,$allowed_extension) ) {
                        $new_entry_form -> addError('File type or extension is not allowed.');
                        return;
                    }
                    if ($max_file_size && $value['size'][$k] > $max_file_size*1024) {
                        $new_entry_form -> addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                        return;
                    }
                }
            }
        }
        
        if(!$new_entry_form -> isValid($this -> getRequest() -> getPost())) {
            foreach ($arrayIsValid as $key => $value) {
                if (!$value) {
                    $ele = $new_entry_form -> getElement($key);
                    if ($ele instanceof Zend_Form_Element)
                        $ele = $ele -> setRequired(true);
                }
            }
            return;
        }

        $tableEntries = Engine_Api::_() -> getDbTable('entries', 'yndynamicform');

        // Process to save entry
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try {
            $new_entry = $tableEntries -> createRow();
            $new_entry -> form_id = $yndform -> getIdentity();
            if (!$viewer -> getIdentity()) {
                $ipObj = new Engine_IP();
                $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                $new_entry -> ip = $ipExpr;
                $new_entry -> user_email = $this -> getRequest() -> getParam('email_guest');
            }
            $new_entry -> owner_id = $viewer -> getIdentity();
            $new_entry -> save();

            $yndform -> total_entries++;
            $yndform -> save();

            if (isset($_FILES) && $viewer -> getIdentity()) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value)
                {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;

                    // Validate file extension
                    $field_id = explode('_', $key)[2];
                    $map = $mapData -> getRowMatching('child_id', $field_id);
                    $field = $map->getChild();

                    $elementFile = $new_entry_form -> getElement($key);
                    $file_ids = $new_entry -> saveFiles($value);
                    unset($value['tmp_name']);
                    unset($value['error']);
                    $value['file_ids'] = $file_ids;
                    $elementFile -> setValue(json_encode($value));
                }
            }

            $new_entry_form -> setItem($new_entry);
            $new_entry_form -> saveValues();

            // Auth
            $auth = Engine_Api::_() -> authorization() -> context;
            $auth -> setAllowed($new_entry, 'owner', 'view', 1);

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Send notifications
        $moderators = $yndform -> getAllModeratorsID();
        $supperAdmins = $yndform -> getSuperAdminsID();
        $user_ids = array_merge($moderators,$supperAdmins);
        $user_ids = array_unique($user_ids);

        if (count($user_ids) > 0) {
            // Prepare params send notification
            $users = Engine_Api::_()->getItemMulti('user', $user_ids);
            if ($viewer -> getIdentity()) {
                $notificationType = 'yndynamicform_user_submitted';
            } else {
                $notificationType = 'yndynamicform_anonymous_submitted';
            }

            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach( $users as $user ) {
                if (!$viewer->isSelf($user)) {
                    $notificationTable->addNotification($user, $viewer, $yndform, $notificationType);
                }
            }

            // Get notification email
            $selected_notification = Engine_Api::_() -> getItem('yndynamicform_notification', $this->getRequest()->getParam('selected_notification'));
            if ($selected_notification && $selected_notification instanceof Yndynamicform_Model_Notification) {
                // Prepare params send email notification
                $mail_api = Engine_Api::_() -> getApi('mail', 'core');
                $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
                $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
                $subjectTemplate = $selected_notification -> notification_email_subject;
                $bodyTextTemplate = $selected_notification -> notification_email_body;

                $rParams['website_name'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Communication');
                $rParams['website_link'] = $this->view->baseUrl();
                $rParams['form_name'] = $yndform -> title;
                $rParams['form_link'] = $yndform -> getHref();

                foreach( $rParams as $var => $val )
                {
                    $var = '[' . $var . ']';
                    // Fix nbsp
                    $val = str_replace('&amp;nbsp;', ' ', $val);
                    $val = str_replace('&nbsp;', ' ', $val);
                    // Replace
                    $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
                }

                foreach( $rParams as $var => $val )
                {
                    $var = '[' . $var . ']';
                    // Fix nbsp
                    $val = str_replace('&amp;nbsp;', ' ', $val);
                    $val = str_replace('&nbsp;', ' ', $val);
                    // Replace
                    $subjectTemplate = str_replace($var, $val, $subjectTemplate);
                }

                $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');


                foreach( $users as $user )
                {
                    if (!$viewer->isSelf($user)) {
                        if ($notificationSettingsTable->checkEnabledNotification($user, $notificationType)) {
                            $recipientEmail = $user->email;
                            $recipientName = $user->displayname;

                            $mail = $mail_api->create()
                                ->addTo($recipientEmail, $recipientName)
                                ->setFrom($fromAddress, $fromName)
                                ->setSubject($subjectTemplate)
                                ->setBodyText($bodyTextTemplate);
                            $mail_api->sendRaw($mail);
                        }
                    }
                }
            }
        }


        // Remove old confirmation
        session_start();
        unset($_SESSION["confirmation_id"]);
        // Get confirmation
        $selected_confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $this->getRequest()->getParam('selected_confirmation'));
        if ($selected_confirmation instanceof Yndynamicform_Model_Confirmation) {
            $_SESSION["confirmation_id"] = $this->getRequest()->getParam('selected_confirmation');
            if ($selected_confirmation -> type == 'url') {
                $conf_url = $selected_confirmation -> confirmation_url;
                if (strpos($conf_url, 'http://') == -1 && strpos($conf_url, 'https://') == -1)
                    $conf_url = 'http://'.$conf_url;
                header('Location: '. $conf_url);
            } else {
                return $this -> _helper -> redirector -> gotoRoute(array('action' => 'confirmation'), 'yndynamicform_form_general');
            }
        } else {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'action' => 'view',
                    'entry_id' => $new_entry -> getIdentity()
                ), 'yndynamicform_entry_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }

    public function showPopUpEmailAction()
    {
        $this -> _helper -> layout -> setLayout('default-simple');

        $require_email = $this -> _getParam('require_email');
        $this -> view -> form_email = $form_email = new Yndynamicform_Form_Entry_EmailPopUp(array('requireEmail' => $require_email));

        if ($this -> getRequest() -> isPost() && $form_email -> isValid($this -> getRequest() -> getParams())) {
            $values = $form_email -> getValues();
            $this -> view -> closeSmoothbox = true;
            $this -> view -> email = $values['email'];
        }

        // Ouput
        $this -> renderScript('form/email-popup.tpl');
    }

    public function confirmationAction()
    {
        session_start();

        if (!$_SESSION["confirmation_id"]) {
            $this -> _helper -> requireAuth -> forward();
        }

        $this -> _helper -> content -> setEnabled();
        $this -> view -> confirmation = $confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $_SESSION["confirmation_id"]);
    }
}