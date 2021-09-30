<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


class Sitepage_ManageformsController extends Core_Controller_Action_Standard
{
    protected $_requireProfileType = true;


    protected $_fieldType = 'yndynamicform_entry';

    protected $_moduleName;

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm();


        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $form = Engine_Api::_()->getItem('sesmultipleform_form', $value)->delete();
                }
            }
        }
        $this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
        $page = $this->_getParam('page', 1);
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($page);
    }
    public function getDbTable() {
        return Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
    }
    /// dynamic form ///
    public function manageAction() {
        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm();

        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $keyword = $this->_getParam('keyword',null);
        $category_id = $this->_getParam('category_id',null);
        $start_date = $this->_getParam('start_date',null);
        $to_date = $this->_getParam('to_date',null);
        $status = $this->_getParam('status',null);
        if($keyword || $category_id || $start_date || $to_date || $status) {
            $this->view->search_exists = true;
        }
        else
            $this->view->search_exists = false;

        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_forms');
        if ($this -> getRequest() -> isPost()) {
            $values = $this -> getRequest() -> getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $form = Engine_Api::_() -> getItem('yndynamicform_form', $value);
                    if ($form)
                        $form -> delete();
                }
            }
        }

        $params = $this -> _getAllParams();
        $params['valid_form'] = true;
        $params['page_id'] = $page_id;
        $this->view-> sort_field = $params['fieldOrder'] = $this -> _getParam('fieldOrder', 'creation_date');
        $this->view->sort_direction = $params['direction'] = $this -> _getParam('direction', 'desc');
        $table = $this -> getDbTable();
        $this -> view -> paginator = $paginator = $table -> getOrganisationFormsPaginator($params);

        $this -> view -> paginator -> setItemCountPerPage(20);
        $page = $this -> _getParam('page', 1);
        $this -> view -> paginator -> setCurrentPageNumber($page);
        // Form Search Form
        $this -> view -> form = $form = new Yndynamicform_Form_Admin_Search();

        $form -> populate($params);
        $formValues = $form -> getValues();
        if (isset($params['fieldOrder'])) {
            $formValues['fieldOrder'] = $params['fieldOrder'];
        }
        if (isset($params['direction'])) {
            $formValues['direction'] = $params['direction'];
        }
        $this -> view -> params = $formValues;


    }
    public function createAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        // Generate and assign form
        $new_form = $this -> view -> form = new Yndynamicform_Form_Admin_NewForm();
        $new_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();

        $new_form->getElement('submit')->setLabel('Create and Manage Fields');

        // Check post
        if ($this -> getRequest() -> isPost() && $new_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $new_form -> getValues();
            if (strlen($values['title']) > 128) {
                $new_form -> title -> addError('Value must be less than 128 characters.');
                return;
            }
            $user = Engine_Api::_() -> user() -> getViewer();

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $form = $table -> createRow();

                $form -> setFromArray($values);
                $form -> user_id = $user -> getIdentity();

                $optionId = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($form->title);
                if (!empty($values['photo'])) {
                    $form->setPhoto($new_form->photo);
                }
                $form->option_id = $optionId;
                $form->privacy = 3; // 3 mean everyone can see this form
                $form->page_id = (int) $page_id;

                $form -> save();

                $last_form_id =  $form['form_id'];

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                foreach ($roles as $i => $role)
                {
                    $auth->setAllowed($form, $role, 'view', 1);
                    $auth->setAllowed($form, $role, 'comment', 1);
                    $auth->setAllowed($form, $role, 'submission', 1);
                }

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            $params = array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array('New form has been successfully added.')
            );

            //prvious working redirect
            //return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));


            //  return $this->_forward('success', 'utility', 'core', $params);
            //            return array(
            //
            //                'route' => 'sitepage_api',
            //                'controller' => 'manageforms',
            //                'action' => 'manage',
            //                'params' => array(
            //                    'page_id' => $page_id
            //                ),
            //             'messages' => array(Zend_Registry::get('Zend_Translate') -> _('New form has been successfully added.'))
            //            );

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'fields',
                    'option_id'=>$optionId,
                    'id' =>$last_form_id,
                    'page_id' => $page_id

                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('New form has been successfully added.'))
            ));

        }

        // Output
        $this -> renderScript('admin-form/create.tpl');
    }

    public function mainInfoAction()
    {

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $this -> _getParam('form_id'));

        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' =>true,
                'messages' => Array($this -> view -> message)
            ));
        }

        $this -> view -> form = $form;

        // Get edit form
        $this -> view -> editform = $editform = new Yndynamicform_Form_Admin_EditForm_MainInfo();
        $value = $form -> toArray();
        $editform -> populate($value);

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$editform -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        $value = $editform -> getValues();

        if (strlen($value['title']) > 128) {
            $editform -> title -> addError('Value must be less than 128 characters.');
            return;
        }

        $table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
        $db = $table -> getAdapter();

        // Begin transaction
        $db -> beginTransaction();
        try {
            $form -> setFromArray($value);
            if (!empty($value['photo'])) {
                $form->setPhoto($editform->photo);
            }
            $form -> modified_date = date('Y-m-d H:i:s');

            $form -> save();
            $db -> commit();
        } catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        $editform -> addNotice('Your changes have been saved.');
    }
    public function settingsAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $this -> _getParam('form_id'));

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Get edit form
        $this -> view -> editform = $editform = new Yndynamicform_Form_Admin_EditForm_FormSettings(array('form' => $form -> getIdentity()));
        $editform->entries_max->setValue('1');
        // Populate form
        $values = $form -> toArray();

        $values = array_filter($values, function ($val){
            return !is_null($val);
        });
        $editform -> populate($values);
        $editform -> privacy -> setValue(array(1 & $form -> privacy,2 & $form -> privacy));


        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$editform -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        // Get all values from edit form
        $values = $editform -> getValues();
        $params = $this ->_getAllParams();

        $values['conditional_logic'] = json_encode($params['conditional_logic']);
        $values['conditional_scope'] = $params['conditional_scope'];
        $values['conditional_show'] = $params['conditional_show'];

        // Validate valid form from and to
        if (!empty($values['valid_from_date']) && !empty($values['valid_to_date']) && !$values['unlimited_time']
            && (strtotime($values['valid_from_date']) > strtotime($values['valid_to_date']))) {
            $editform -> valid_to_date -> addError('Value must be after Valid Time From.');
            return;
        }

        if (!empty($values['unlimited_time']) && $values['unlimited_time']) {
            $values['valid_to_date'] = null;
        } else if (empty($values['valid_to_date']) && !$values['unlimited_time'] && !empty($values['valid_from_date'])) {
            $editform->valid_to_date->addError('Value is required and can\'t be empty');
            return;
        }
        // TODO: Implement conditional logic for form settings.

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        try {
            /*
             * If user check both guest and registered user. Privacy is 3
             * else we save first value (guest or registered)
             */
            if (count($values['privacy']) > 1) {
                $values['privacy'] = 3;
            } elseif (!empty($values['privacy'])) {
                $values['privacy'] = $values['privacy'][0];
            }
            $form -> setFromArray($values);

            if (empty($values['valid_from_date'])) {
                $form -> valid_from_date = null;
            }

            if (empty($values['valid_to_date'])) {
                $form -> valid_to_date = null;
            }

            $form -> modified_date = date('Y:m:d H:i:s');

            $form -> save();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> message = 'Your changes have been saved.';
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array( 'module' => 'sitepage','controller' => 'manageforms', 'action' => 'settings', 'form_id' => $form->getIdentity(),'page_id'=>$page_id),  'default', true),
            'messages' => Array($this -> view -> message)
        ));
//        $params = array(
//            'smoothboxClose' => true,
//            'parentRefresh' => true,
//            'messages' => array('Your changes have been saved.')
//        );

        //return $this->_forward('success', 'utility', 'core', $params);
    }



    // Fields
    public function fieldsAction()
    {



        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



        // Parse module name from class
        if( !$this->_moduleName ) {
            $this->_moduleName = substr(get_class($this), 0, strpos(get_class($this), '_'));
        }

        // Try to set item type to module name (usually an item type)
        if( !$this->_fieldType ) {
            $this->_fieldType = Engine_Api::deflect($this->_moduleName);
        }

        if( !$this->_fieldType || !$this->_moduleName || !Engine_APi::_()->hasItemType($this->_fieldType) ) {
            throw new Fields_Model_Exception('Invalid fieldType or modulePath');
        }

        $this->view->fieldType = $this->_fieldType;

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);









        // Hack up the view paths
        $this->view->addHelperPath(dirname(dirname(__FILE__)) . '/views/helpers', 'Fields_View_Helper');
        $this->view->addScriptPath(dirname(dirname(__FILE__)) . '/views/scripts');

        $this->view->addHelperPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/helpers', $this->_moduleName . '_View_Helper');
        $this->view->addScriptPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/scripts');








        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array());
        $form_id = $this->_getParam('id');

        $this->view->form_id = $form_id;
        $form = Engine_Api::_()->getItem('yndynamicform_form', $form_id);


        if (!$form->getIdentity()) return;

        $this->view->form = $form;
        // Add type
        $categories = Engine_Api::_()->yndynamicform()->getFieldInfo('categories');
        $advanced = Engine_Api::_()->yndynamicform()->getFieldInfo('advanced_fields');
        $ua = Engine_Api::_()->yndynamicform()->getFieldInfo('user_analytics_fields');
        $types = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
        $fieldByCat = array();
        $standardFields = array();
        $advancedFields = array();
        $analyticsFields = array();
      
        foreach( $types as $fieldType => $info ) {


            $fieldByCat[$info['category']][$fieldType] = $info['label'];
            $fieldByCat[$fieldType] = $info['label'];
            if (in_array($fieldType, $advanced))
            {
                $advancedFields[$fieldType] = $info['label'];

            }
            else if (in_array($fieldType, $ua))
                $analyticsFields[$fieldType] = $info['label'];
        }
        foreach( $categories as $catType => $categoryInfo ) {
            $label = $categoryInfo['label'];

            $standardFields[$label] = $fieldByCat[$catType];
        }

        $this->view->standardFields = $standardFields;
        $this->view->advancedFields = $advancedFields;
        $this->view->analyticsFields = $analyticsFields;

        // Set data
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($this->_fieldType);
        $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($this->_fieldType);
        $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->_fieldType);

        // Get top level fields
        $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
        $topLevelFields = array();
        foreach( $topLevelMaps as $map ) {
            $field = $map->getChild();
            $topLevelFields[$field->field_id] = $field;
        }
        $this->view->topLevelMaps = $topLevelMaps;
        $this->view->topLevelFields = $topLevelFields;


        // Get top level field
        // Only allow one top level field
        if( count($topLevelFields) > 1 ) {
            throw new Engine_Exception('Only one top level field is currently allowed');
        }
        $topLevelField = array_shift($topLevelFields);
        // Only allow the "profile_type" field to be a top level field (for now)
        if( $topLevelField->type !== 'profile_type' ) {
            throw new Engine_Exception('Only profile_type can be a top level field');
        }
        $this->view->topLevelField = $topLevelField;
        $this->view->topLevelFieldId = $topLevelField->field_id;

        // Get top level options
        $topLevelOptions = array();
        foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
            $topLevelOptions[$option->option_id] = $option->label;
        }
        $this->view->topLevelOptions = $topLevelOptions;

        // Get selected option
        $option_id = $this->_getParam('option_id');
        if( empty($option_id) || empty($topLevelOptions[$option_id]) ) {
            $option_id = current(array_keys($topLevelOptions));
        }
        $topLevelOption = $optionsData->getRowMatching('option_id', $option_id);
        if( !$topLevelOption ) {
            throw new Engine_Exception('Missing option');
        }
        $this->view->topLevelOption = $topLevelOption;
        $this->view->topLevelOptionId = $topLevelOption->option_id;

        // Get second level fields
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if( !empty($option_id) ) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if( !empty($secondLevelMaps) ) {
                foreach( $secondLevelMaps as $map ) {
                    $secondLevelFields[$map->child_id] = $map->getChild();
                }
            }
        }
        $this->view->secondLevelMaps = $secondLevelMaps;
        $this->view->secondLevelFields = $secondLevelFields;
    }


    public function fieldCreateAction()
    {
        
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
        $formItem = Engine_Api::_()->getItemTable('yndynamicform_form')->fetchAll()->getRowMatching('option_id', $this->_getParam('option_id'));

        // Check type param and get form class
        $cfType = $this->_getParam('type');
        $adminFormClass = null;
        if( !empty($cfType) ) {
            $adminFormClass = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'adminFormClass');
            $fieldLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'label');
        }
        if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
            $adminFormClass = 'Yndynamicform_Form_Admin_Field';
        }
        
        $this->view->adminFormClass = $adminFormClass;
        
        // Create form
        $this->view->form = $form = new $adminFormClass();
        $form->setTitle('Create Form Field: ' . $fieldLabel);

        // add default/min/max for numbers
        if($cfType == 'integer' || $cfType == 'float'){
            $form->addElement('float', 'default_value', array(
                'label' => 'Default Value',
                'value'=> null,
            ));
            $form->addElement('float', 'min_value', array(
                'label' => 'Minimum Value',
                'value'=> null,
            ));
            $form->addElement('float', 'max_value', array(
                'label' => 'Maximum Value',
                'value'=> null ,
            ));
        }



            echo " <script>document.getElementById('enable_prepopulate-wrapper').style.display='none'</script> ";

        if( $this->getRequest()->isPost() ) {
            $_POST['label'] = addslashes($_POST['label']);
             if(strlen($_POST['label']) > 250) {
                 ?>
                 <script>
                     var tag = document.createElement("p");
                     var text = document.createTextNode("* Please enter characters only upto 250");
                     tag.setAttribute('id','label-limit-err');
                     tag.appendChild(text);
                     var element = document.getElementById("label-element");
                     element.appendChild(tag);

                     let dd = '<?php echo $_POST['label']; ?>';
                     document.getElementById("label").value = dd;
                 </script>
                  <?php
                 return;
             }
        }


        // Check method/data
        if( !$this->getRequest()->isPost() ) {
            $form->populate($this->_getAllParams());

            // special case for page break
            if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
                if( is_array($formItem->page_break_config) ){
                    $form->populate($formItem->page_break_config);
                    $form->populate(array(
                        'page_names_hidden' => json_encode($formItem->page_break_config['page_names']),
                    ));
                }
                $totalPages = $this->getTotalPageBreaks() + 2;
                $form->populate(array(
                    'total_pages' => $totalPages,
                ));
            }
            return;
        }

        $params = $this->_getAllParams();
        
        if( isset($params['type']) && !empty($params['type']) && ($params['type'] == 'metrics') )
            $this->view->values = $params;
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        
        if( isset($values['temp_label']) && !empty($values['temp_label']) )
            $values['label'] = $values['temp_label'];
        
        if( isset($values['temp_description']) && !empty($values['temp_description']) )
            $values['description'] = $values['temp_description'];
        
        // Send the form posted values to show the prefields values in the form.
        if( isset($params['metric_aggregate_type']) && !empty($params['metric_aggregate_type']) && ($params['metric_aggregate_type'] == 'own_formula')) {
            if( empty($params['own_formula_input']) ) {
                return $form->addError('Formula could not be empty! Please enter the valid formula for the metric.');
            }
            
            if( empty($params['own_formula_metric_all_list']) ) {
                return $form->addError('Metric information not found, please contact to the administrator.');
            }
            
            $validateOwnFormulaInput = $params['own_formula_input'];
            $params['own_formula_by_id'] = $params['own_formula_input'];
            $params['own_actual_formula'] = $params['own_formula_input'];
            $ownFormulaMetricsAlList = @json_decode($params['own_formula_metric_all_list']);
            foreach( $ownFormulaMetricsAlList as $metrics ) {
                $searchText = '[' . $metrics->label . ']';
                $tempReplaceText = 'field_id_' . $metrics->field_id;
                $replaceText = $metrics->label; // . '_' . $tempReplaceText;
                $validateOwnFormulaInput = @str_replace($searchText, '', $validateOwnFormulaInput);
                $params['metric_aggregate_fields'][] = $metrics->field_id;
                $params['own_formula_by_id'] = @str_replace($searchText, $tempReplaceText, $params['own_formula_by_id']);
                $params['own_formula_input'] = @str_replace($searchText, $replaceText, $params['own_formula_input']);
            }
            
            $values['own_formula_input'] = $params['own_formula_input'];
            $values['own_formula_by_id'] = $params['own_formula_by_id'];
            $values['own_actual_formula'] = $params['own_actual_formula'];
            
            // After replace all formula made tags, now it formula should be blank, If it's not blank then it means, user entered the wrong input.
            $validateOwnFormulaInput = @str_replace(".", "", $validateOwnFormulaInput);
            $validateOwnFormulaInput = @str_replace(["(", ")", "%", "/", "-", "+", "*", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"], "", $validateOwnFormulaInput);
            if( !empty($validateOwnFormulaInput) )
                return $form->addError('It seems that you entered the wrong formula. Please insert the allowed tag only!');
        }
        
        unset($params['conditional_logic_tpl']);
        $values['conditional_enabled'] = $params['conditional_enabled'];
        $values['conditional_logic'] = $params['conditional_logic'];
        $values['conditional_show'] = $params['conditional_show'];
        $values['conditional_scope'] = $params['conditional_scope'];
        $values['page_names'] = $params['page_names'];

        $values['label'] = str_replace("\\'","'",$values['label']);
        

        // save the metrics sum fields
        if(isset($params['metric_aggregate_type']) &&
            $params['metric_aggregate_type']!= '' &&
            $params['metric_aggregate_type']!= null
        ){
            $values['metric_aggregate_type'] = $params['metric_aggregate_type'];
        }
        if(isset($params['metric_aggregate_fields']) &&
            count($params['metric_aggregate_fields']) &&
            $params['metric_aggregate_fields']!= '' &&
            $params['metric_aggregate_fields']!= null
        ){
            $values['metric_aggregate_fields'] = $params['metric_aggregate_fields'];
        }

        $values['selected_metric_id'] = $params['selected_metric_id'];

        if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_UserAnalytics')
            $values['label'] = $fieldLabel;

        //convert aphostrophe to encoded code
        $fieldLabel = (string)$values['label'];
        $fieldLabel = str_replace("'","#540",$fieldLabel);
        $values['label'] = $fieldLabel;
        

        if($cfType == 'metrics'){
            if(!$values['selected_metric_id']){
                return $form->addError('Metrics is not selected, make sure it is created or selected');
            }else{
                $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
                    'option_id' => ( is_object($option) ? $option->option_id : '0' ),
                ), $values));
            }
        }else{
            $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
                'option_id' => ( is_object($option) ? $option->option_id : '0' ),
            ), $values));
        }

        // save filed config to form page break config
        if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
            unset($field->config['conditional_enabled']);
            unset($field->config['conditional_logic']);
            $formItem->page_break_config = $field->config;
            $formItem->save();
        }

        $this->view->status = true;
        $this->view->field = $field->toArray();
        $this->view->option = is_object($option) ? $option->toArray() : array('option_id' => '0');
        $this->view->form = null;

        // Re-render all maps that have this field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;

        return $this -> _forward('success', 'utility', 'core', array(
            'layout' => 'default-simple',
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The field is created successfully.'))
        ));

    }

    public function fieldEditAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $option_id = $this->_getParam('option_id');
        $field_id = $this->_getParam('field_id');


        //check form is cloned or not
        $db = Engine_Db_Table::getDefaultAdapter();
        $formDetails =  $db->select()
            ->from('engine4_yndynamicform_forms')
            ->where('option_id = ?', $option_id)
            ->limit()
            ->query()
            ->fetchAll();

        $formDetails = $formDetails ? $formDetails[0] : null;
        $isFormCloned = count($formDetails) >0 ? $formDetails['form_cloned'] : 0;



        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
        $formItem = Engine_Api::_()->getItemTable('yndynamicform_form')->fetchAll()->getRowMatching('option_id', $this->_getParam('option_id'));
        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

        // Check type param and get form class
        $cfType = $this->_getParam('type', $field->type);
        $adminFormClass = null;
        if( !empty($cfType) ) {
            $adminFormClass = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'adminFormClass');
            $fieldLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'label');
        }
        if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
            $adminFormClass = 'Yndynamicform_Form_Admin_Field';
        }

        // Create form
        $this->view->form = $form = new $adminFormClass();
        $form->setTitle('Edit Form Field: ' . $fieldLabel);

        // add default/min/max for numbers
        if($cfType == 'integer' || $cfType == 'float'){

            $form->addElement('float', 'default_value', array(
                'label' => 'Default Value',
                'value'=> null,
            ));
            $form->addElement('float', 'min_value', array(
                'label' => 'Minimum Value',
                'value'=> null,
            ));
            $form->addElement('float', 'max_value', array(
                'label' => 'Maximum Value',
                'value'=> null,
            ));
        }

        if( $this->getRequest()->isPost() ) {
            $_POST['label'] = addslashes($_POST['label']);
            if(strlen($_POST['label']) > 250) {
                ?>
                <script>
                    var tag = document.createElement("p");
                    var text = document.createTextNode("* Please enter characters only upto 250");
                    tag.setAttribute('id','label-limit-err');
                    tag.appendChild(text);
                    var element = document.getElementById("label-element");
                    element.appendChild(tag);
                    let dd = '<?php echo $_POST['label']; ?>';
                    document.getElementById("label").value = dd;
                </script>
                <?php
                return;
            }
        }

        // Check method/data
        if( !$this->getRequest()->isPost() ) {


            //convert encode code to aphostrophe
            $field->label = str_replace("#540","'",$field->label);


            if($isFormCloned) {

                $fieldValues =  $field->toArray();
                //get field details
                $db = Engine_Db_Table::getDefaultAdapter();
                $fieldDetails =  $db->select()
                    ->from('engine4_yndynamicform_entry_fields_meta')
                    ->where('field_id = ?', $fieldValues['field_id'])
                    ->limit()
                    ->query()
                    ->fetchAll();
                $fieldDetails = $fieldDetails[0];
                $val = (int)$fieldDetails['enable_prepopulate'] ? 1 : 0;


                // Search
              //  $form->getElement('enable_prepopulate')->setCheckedValue(0);




                echo " <script>document.getElementById('enable_prepopulate-wrapper').style.display='block'</script> ";
            }
            else {
                echo " <script>document.getElementById('enable_prepopulate-wrapper').style.display='none'</script> ";
            }



            $form->populate($field->toArray());
            $form->populate($this->_getAllParams());
            if( is_array($field->config) ){
                $form->populate($field->config);
                $form->populate(array(
                    'conditional_logic'=> json_encode($field->config['conditional_logic'])
                ));
            }
            // special case for page break
            if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
                if( is_array($formItem->page_break_config) ){
                    $form->populate($formItem->page_break_config);
                    $form->populate(array(
                        'page_names_hidden' => json_encode($formItem->page_break_config['page_names']),
                    ));
                }
                $totalPages = $this->getTotalPageBreaks() + 1;
                $form->populate(array(
                    'total_pages' => $totalPages,
                ));
            }
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }


        if( $this->getRequest()->isPost() ) {
            if($form->getElement('enable_prepopulate')){
                $valss = $form->getElement('enable_prepopulate')->getValue() ? 1 : 0;
                $db->update('engine4_yndynamicform_entry_fields_meta', array(
                    'enable_prepopulate' => $valss,
                ), array(
                    'field_id = ?' => $field_id,
                ));
            }
        }


        $params = $this ->_getAllParams();
        $values = $form->getValues();
        
        if( isset($values['temp_label']) && !empty($values['temp_label']) )
            $values['label'] = $values['temp_label'];
        
        if( isset($values['temp_description']) && !empty($values['temp_description']) )
            $values['description'] = $values['temp_description'];
        
        // Update the formula values for metrics
        if( !empty($params['type']) && ($params['type'] == 'float') ) {
            Engine_Api::_()->impactx()->updateFormulaOnEditNumberField($params); 
        }
        
        // Send the form posted values to show the prefields values in the form.
        if( isset($params['metric_aggregate_type']) && !empty($params['metric_aggregate_type']) && ($params['metric_aggregate_type'] == 'own_formula')) {
            if( empty($params['own_formula_input']) ) {
                return $form->addError('Formula could not be empty! Please enter the valid formula for the metric.');
            }
            
            if( empty($params['own_formula_metric_all_list']) ) {
                return $form->addError('Metric information not found, please contact to the administrator.');
            }
            
            $validateOwnFormulaInput = $params['own_formula_input'];
            $params['own_formula_by_id'] = $params['own_formula_input'];
            $params['own_actual_formula'] = $params['own_formula_input'];
            $ownFormulaMetricsAlList = @json_decode($params['own_formula_metric_all_list']);
            foreach( $ownFormulaMetricsAlList as $metrics ) {
                $searchText = '[' . $metrics->label . ']';
                $tempReplaceText = 'field_id_' . $metrics->field_id;
                $replaceText = $metrics->label; // . '_' . $tempReplaceText;
                $validateOwnFormulaInput = @str_replace($searchText, '', $validateOwnFormulaInput);
                $params['metric_aggregate_fields'][] = $metrics->field_id;
                $params['own_formula_by_id'] = @str_replace($searchText, $tempReplaceText, $params['own_formula_by_id']);
                $params['own_formula_input'] = @str_replace($searchText, $replaceText, $params['own_formula_input']);
            }
            
            $values['own_formula_input'] = $params['own_formula_input'];
            $values['own_formula_by_id'] = $params['own_formula_by_id'];
            $values['own_actual_formula'] = $params['own_actual_formula'];
            
            // After replace all formula made tags, now it formula should be blank, If it's not blank then it means, user entered the wrong input.
            if(substr_count($validateOwnFormulaInput, "(") != substr_count($validateOwnFormulaInput, ")"))
                return $form->addError('It seems that you entered the wrong formula. Please insert the allowed tag only!');
            
            if((substr_count($validateOwnFormulaInput, "[") != 0) || (substr_count($validateOwnFormulaInput, "]") != 0))
                return $form->addError('It seems that you entered the wrong formula. Please insert the allowed tag only!');
            
//            if( strstr($validateOwnFormulaInput, "%%") || strstr($validateOwnFormulaInput, "//") || strstr($validateOwnFormulaInput, "--") || strstr($validateOwnFormulaInput, "++") || strstr($validateOwnFormulaInput, "**") )
//                    return $form->addError('It seems that you entered the wrong formula. Please insert the allowed tag only!');
            
            $validateOwnFormulaInput = @str_replace(".", "", $validateOwnFormulaInput);
            $validateOwnFormulaInput = @str_replace(["(", ")", "%", "/", "-", "+", "*", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"], "", $validateOwnFormulaInput);
            if( !empty($validateOwnFormulaInput) )
                return $form->addError('It seems that you entered the wrong formula. Please insert the allowed tag only!');
        }
        
        unset($params['conditional_logic_tpl']);
        $values['conditional_enabled'] = $params['conditional_enabled'];
        $values['conditional_logic'] = $params['conditional_logic'];
        $values['conditional_show'] = $params['conditional_show'];
        $values['conditional_scope'] = $params['conditional_scope'];
        $values['label'] = str_replace("\\'","'",$values['label']);
        // save the metrics sum fields
        if(isset($params['metric_aggregate_type']) &&
            $params['metric_aggregate_type']!= '' &&
            $params['metric_aggregate_type']!= null
        ){
            $values['metric_aggregate_type'] = $params['metric_aggregate_type'];
        }
        if(isset($params['metric_aggregate_fields']) &&
            count($params['metric_aggregate_fields']) &&
            $params['metric_aggregate_fields']!= '' &&
            $params['metric_aggregate_fields']!= null
        ){
            $values['metric_aggregate_fields'] = $params['metric_aggregate_fields'];
        }

        if( !empty($params['selected_metric_id']) && empty($values['selected_metric_id']) )
            $values['selected_metric_id'] = $params['selected_metric_id'];

        //convert aphostrophe to encoded code
        $fieldLabel = (string)$values['label'];
        $fieldLabel = str_replace("'","#540",$fieldLabel);
        $values['label'] = $fieldLabel;
        
        if($cfType == 'metrics'){
            if(!$values['selected_metric_id']){
                return $form->addError('Metrics is not selected, make sure it is created or selected');
            }else{
                Engine_Api::_()->fields()->editField($this->_fieldType, $field, $values);
            }
        }else{
            Engine_Api::_()->fields()->editField($this->_fieldType, $field, $values);
        }

        // special case for page break
        if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
            // save filed config to form page break config
            unset($params['label']);
            unset($params['description']);
            unset($params['conditional_enabled']);
            unset($params['conditional_logic']);
            $formItem->page_break_config = $params;
            $formItem->save();
        }

        // edit the first page break too

        $this->view->status = true;
        $this->view->field = $field->toArray();
        $this->view->form = null;

        // Re-render all maps that have this field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }

    public function fieldDeleteAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

        $this->view->form = $form = new Engine_Form(array(
            'method' => 'post',
            'action' => $_SERVER['REQUEST_URI'],
            'elements' => array(
                array(
                    'type' => 'submit',
                    'name' => 'submit',
                )
            )
        ));

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        $this->view->status = true;
        Engine_Api::_()->fields()->deleteField($this->_fieldType, $field);
    }


    // Headings
    public function headingCreateAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

        // Create form
        $this->view->form = $form = new Yndynamicform_Form_Admin_Heading();

        if( $this->getRequest()->isPost() ) {
            if(strlen($_POST['label']) > 250) {
                ?>
                <script>
                    var tag = document.createElement("p");
                    var text = document.createTextNode("* Please enter characters only upto 250");
                    tag.setAttribute('id','label-limit-err');
                    tag.appendChild(text);
                    var element = document.getElementById("label-element");
                    element.appendChild(tag);
                    let dd = '<?php echo $_POST['label']; ?>';
                    document.getElementById("label").value = dd;
                </script>
                <?php
                return;
            }
        }

        // Check method/data
        if( !$this->getRequest()->isPost() ) {
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Process
        $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
            'option_id' => $option->option_id,
            'type' => 'heading',
            'display' => 1
        ), $form->getValues()));

        $this->view->status = true;
        $this->view->field = $field->toArray();
        $this->view->option = $option->toArray();
        $this->view->form = null;

        // Re-render all maps that have this field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
        );
        $html = array();

        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }

        $this->view->htmlArr = $html;
    }

    public function headingEditAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

        // Create form
        $this->view->form = $form = new Yndynamicform_Form_Admin_Heading();
        $form->submit->setLabel('Edit Heading');

        // Check method/data
        if( !$this->getRequest()->isPost() ) {
            $form->populate($field->toArray());
            return;
        }

        if( $this->getRequest()->isPost() ) {
            if(strlen($_POST['label']) > 250) {
                ?>
                <script>
                    var tag = document.createElement("p");
                    var text = document.createTextNode("* Please enter characters only upto 250");
                    tag.setAttribute('id','label-limit-err');
                    tag.appendChild(text);
                    var element = document.getElementById("label-element");
                    element.appendChild(tag);
                    let dd = '<?php echo $_POST['label']; ?>';
                    document.getElementById("label").value = dd;
                </script>
                <?php
                return;
            }
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Process
        Engine_Api::_()->fields()->editField($this->_fieldType, $field, $form->getValues());

        $this->view->status = true;
        $this->view->field = $field->toArray();
        $this->view->form = null;

        // Re-render all maps that have this field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }
    public function moderatorsAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $form_id = $this -> _getParam('form_id', 0);

        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'admin-manage' , 'action' => 'manage'), 'default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Prepare params to get form's moderators
        $ids = $form -> getAllModeratorsID();
        $this -> view -> toObjects = Engine_Api::_() -> getItemMulti('user', $ids);
        $this -> view -> toValues = implode(',', $ids);

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        // Get new moderator
        $paramToValues = $this -> _getParam('toValues');
        $newToValues = explode(',', $paramToValues);
        $newToValues = array_filter($newToValues);

        // Get table
        $table = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');

        // Add new moderatos
        if (!empty($newToValues)) {
            $newModerator = array_diff($newToValues, $ids);
            $db = $table -> getAdapter();
            foreach ($newModerator as $k => $id)
            {
                try {
                    $newItem = $table -> createRow();
                    $newItem -> form_id = $form_id;
                    $newItem -> moderator_id = $id;

                    $newItem -> save();
                    $db -> commit();
                } catch (Exception $e) {
                    $db -> rollBack();
                    throw $e;
                }

            }
        }

        // Remove removed moderatos if
        $removeModerator = array_diff($ids, $newToValues);
        $db = $table -> getAdapter();
        if (!empty($removeModerator)) {
            foreach ($removeModerator as $id) {
                try {
                    $item = $table->getModerator($id);
                    if ($item) {
                        $item->delete();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }

            }
        }

        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.');
//        return $this -> _forward('success', 'utility', 'core', array(
//            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'form' , 'action' => 'moderators', 'form_id' => $form_id), 'admin_default', true),
//            'messages' => Array($this -> view -> message)
//        ));
//        $params = array(
//            'smoothboxClose' => true,
//            'parentRefresh' => true,
//            'messages' => array('Your changes have been saved.')
//        );

        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'module' => 'organizations',
                'controller' => 'manageforms',
                'action' => 'moderators',
                'form_id' => $form -> getIdentity(),
                'page_id' => $page_id
            ), 'default', true),
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
        ));
    }

    // Option

    public function optionCreateAction()
    {
        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
        $label = $this->_getParam('label');

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        // Create new option
        $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
            'label' => $label,
        ));

        $this->view->status = true;
        $this->view->option = $option->toArray();
        $this->view->field = $field->toArray();

        // Re-render all maps that have this options's field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }

    public function optionEditAction()
    {
        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
        $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);

        // Create form
        $this->view->form = $form = new Fields_Form_Admin_Option();
        $form->submit->setLabel('Edit Choice');

        // Check method/data
        if( !$this->getRequest()->isPost() ) {
            $form->populate($option->toArray());
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $form->getValues());

        // Process
        $this->view->status = true;
        $this->view->form = null;
        $this->view->option = $option->toArray();
        $this->view->field = $field->toArray();

        // Re-render all maps that have this options's field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }

    public function optionDeleteAction()
    {
        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        // Delete all values
        $option_id = $option->option_id;
        Engine_Api::_()->fields()->deleteOption($this->_fieldType, $option);
    }

    // to support json
    public function customOptionCreateAction()
    {
        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
        $label = $this->_getParam('label');

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        // Create new option
        $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
            'label' => $label,
        ));

        $data['status'] = true;
        $data['option'] = $option->toArray();
        $data['field'] = $field->toArray();

        return $this->_helper->json($data);
    }

    // to support json
    public function customOptionEditAction()
    {
        $option_id = $this->_getParam('edit_id');

        $option = Engine_Api::_()->fields()->getOption($option_id, $this->_fieldType);
        $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);

        $value = $this->_getParam('text');

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $value);

        $data['status'] = true;
        $data['value'] = $value;
        $data['option'] = $option->toArray();
        $data['field'] = $field->toArray();

        return $this->_helper->json($data);
    }

    // to support json
    public function customOptionDeleteAction()
    {
        $delete_id = $this->_getParam('delete_id');
        $option = Engine_Api::_()->fields()->getOption($delete_id, $this->_fieldType);

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        // Delete all values
        $option_id = $option->option_id;
        Engine_Api::_()->fields()->deleteOption($this->_fieldType, $option);

        $data['status'] = true;
        return $this->_helper->json($data);
    }

    public function mapCreateAction()
    {
        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
        //$field = Engine_Api::_()->fields()->getField($this->_getParam('parent_id'), $this->_fieldType);

        $child_id = $this->_getParam('child_id', $this->_getParam('field_id'));
        $label = $this->_getParam('label');
        $child = null;

        if( $child_id ) {
            $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowMatching('field_id', $child_id);
        } else if( $label ) {
            $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowsMatching('label', $label);
            if( count($child) > 1 ) {
                throw new Fields_Model_Exception('Duplicate label');
            }
            $child = current($child);
        } else {
            throw new Fields_Model_Exception('No child field specified');
        }

        if( !($child instanceof Fields_Model_Meta) ) {
            throw new Fields_Model_Exception('No child field found');
        }

        $fieldMap = Engine_Api::_()->fields()->createMap($child, $option);

        $this->view->field = $child->toArray();
        $this->view->fieldMap = $fieldMap->toArray();

        // Re-render all maps that have this field as a parent or child
        $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
        );
        $html = array();
        foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }

    /*
     * Call this method on number fields deletion
     */
    public function validateMapDeleteAction() {
        $map = Engine_Api::_()->fields()->getMap($this->_getParam('child_id'), $this->_getParam('option_id'), $this->_fieldType);
        
        $validateMetricsFormulaOnNumFieldDeletion = Engine_Api::_()->impactx()->validateMetricsFormulaOnNumFieldDeletion($map);
        if( !empty($validateMetricsFormulaOnNumFieldDeletion) ) {
            echo 'Error: Please delete the metrics first';
            exit;
        }
        
        echo 'Success';
        exit;
    }
    
    public function mapDeleteAction()
    {
        $map = Engine_Api::_()->fields()->getMap($this->_getParam('child_id'), $this->_getParam('option_id'), $this->_fieldType);
        
        Engine_Api::_()->fields()->deleteMap($map);
    }
    public function deleteAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $page_id = $this -> _getParam('page_id');
        $this -> view -> form_id = $id;

        // Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $id);
                if ($yndform)
                    $yndform -> delete();

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            return $this -> _forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The form is deleted successfully.'))
            ));
        }

        // Output
        // $this -> _helper -> layout -> setLayout('default-simple');
        // $this -> renderScript('admin-form/delete.tpl');

    }
    public function cloneAction()
    {

        $this->view->page_id = $page_id = $this->_getParam('page_id');

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('form_id');
        $this -> view -> form_id = $id;
        // Check post
        if ($this -> getRequest() -> isPost()) {
            $table = Engine_Api::_()->getDbTable('forms', 'yndynamicform');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                // Get form will be generated
                $form = Engine_Api::_() -> getItem('yndynamicform_form', $id);
                $option_id = $form->option_id;
                $values = $form -> toArray();
                unset($values['creation_date'], $values['modified_date'],$values['form_id'], $values['total_entries'],$values['view_count'],$values['comment_count'],$values['like_count'],$values['valid_from_date'],$values['valid_to_date'],$values['unlimited_time'],$values['status']);
                // Generate form
                $new_form = $table -> createRow();
                $new_form -> setFromArray($values);
                $new_form -> title = $form -> getTitle().' (' . $this->view->translate("Clone") . ')';
                $new_option_id = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($new_form->title);
                $new_form->option_id = $new_option_id;
                $new_form->form_cloned = 1;
                $new_form -> save();

                // clone fields
                $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
                $count = 1;

                $fieldsss = $fieldMaps[0]->getChild();
                $valuess = $fieldsss->toArray();


                $start_id = $valuess['field_id'];
                $currentStartId = array();

                $temp_formula_array = $all_fields_ids = array();
                foreach ($fieldMaps as $item)
                {
                    $field = $item->getChild();
                    $values = $field->toArray();

                    $field_data = array();

                    $arr = $values['config']['conditional_logic']['field_id'];
                    $store_count = array();
                    foreach ($arr as $vall) {
                        array_push($store_count,$vall - $start_id);
                    }
             
                    if( isset($values['config']['own_formula_by_id']) && !empty($values['config']['own_formula_by_id']) )
                        $temp_formula_array[$values['field_id']] = $values['config']['own_formula_by_id'];

                    unset($values['field_id']);
                    unset($values['config']);

                    // change ids in metric_aggregate_fields
                    $new_config = $field->config;
                    if(isset($new_config['metric_aggregate_fields'])){

                        $new_metric_aggregate_fields = array();
                        foreach($new_config['metric_aggregate_fields'] as $data1) {
                            foreach($all_fields_ids as $data2) {
                                if($data1 == $data2['old_field_id']){
                                    $new_metric_aggregate_fields[] = $data2['new_field_id'];
                                }
                            }
                        }
                        $new_config['metric_aggregate_fields'] = $new_metric_aggregate_fields;

                    }

                    $new_values = array_merge($new_config, $values);

                    $new_field = Engine_Api::_()->fields()->createField('yndynamicform_entry', array_merge(array(
                        'option_id' => $new_option_id,
                    ), $new_values));

                    $field_data['new_field_id'] = $new_field['field_id'];
                    $field_data['old_field_id'] = $field->field_id;


                    array_push($currentStartId,$new_field['field_id']);

                    $tempConfig = $new_config;
                //    print_r( count($field->config['conditional_logic']['field_id']));

                    for ( $i=0; $i < count($field->config['conditional_logic']['field_id']) ; $i++) {

                        $tempConfig['conditional_logic']['field_id'][$i] = $currentStartId[0] + $store_count[$i];


                    }
                    //   $values = array_merge($field->config, $values);
                    $new_field -> config = $tempConfig;
                    $new_field -> cloned_parent_field_mapping = '{"parent_field_id":'.$field->field_id.'}';
                    $new_field -> save();


                    $db->update('engine4_yndynamicform_entry_fields_meta', array(
                        'enable_prepopulate' => 1,
                    ), array(
                        'field_id = ?' => $new_field['field_id'],
                    ));

                    // clone options
                    $old_options = $field->getOptions();
                    if (!empty($old_options)) {
                        foreach ($old_options as $option) {

                            if( $values['type'] != 'gender') {
                                // Create new option
                                Engine_Api::_()->fields()->createOption('yndynamicform_entry', $new_field, array(
                                    'label' => $option->label,
                                ));
                            }

                        }
                    }
                    // update map order
                    $map = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry') -> getRowMatching('child_id', $new_field -> field_id);
                    $map -> order = $count;
                    $count++;
                    $map -> save();

                    $all_fields_ids[] = $field_data;

                }
                
                /*
                 * [Start] Update the Formula and It's values for the newly cloned form field.
                 */
                if( !empty($temp_formula_array) ) {
                    foreach( $temp_formula_array as $field_id => $formula ) {
                        if( !empty($field_id) ) {
                            $metricAggregateFieldsArray = array();
                            foreach( $all_fields_ids as $fields ) {
                                if( $fields['old_field_id'] == $field_id )
                                    $new_field_id = $fields['new_field_id'];
                                
                                // make the formua with new fields ids
                                $formula = @str_replace($fields['old_field_id'], $fields['new_field_id'], $formula);
                                
                                // make an array for "metric_aggregate_fields" field
                                if( strstr($formula, 'field_id_' . $fields['new_field_id']) )
                                    $metricAggregateFieldsArray[] = $fields['new_field_id'];
                            }
                            
                            $db = Engine_Db_Table::getDefaultAdapter();
                            $fieldDetails =  $db->select()
                                ->from('engine4_yndynamicform_entry_fields_meta')
                                ->where('field_id = ?', $new_field_id)
                                ->limit(1)
                                ->query()
                                ->fetchAll();
                            $fieldDetails = $fieldDetails[0];
                            
                            if( isset($fieldDetails['config']) && !empty($fieldDetails['config']) ) {
                                $config = json_decode($fieldDetails['config'], true);
                                $config['own_formula_by_id'] = $formula;
                                $config['metric_aggregate_fields'] = $metricAggregateFieldsArray;
                                
                                $tempConfig = @json_encode($config);
                                
                                $db->update('engine4_yndynamicform_entry_fields_meta', array(
                                    'config' => $tempConfig,
                                ), array(
                                    'field_id = ?' => $new_field_id,
                                ));
                            }
                        }
                    }
                }
                /*
                 * [End] Update the Formula and It's values for the newly cloned form field.
                 */

                // clone other data
                $new_form_id = $new_form->getIdentity();
                $modTable = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');
                $notiTable = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform');
                $confTable = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform');

                $preModerators = $modTable->fetchAll($modTable->select()->where('form_id = ?', $id));
                foreach ($preModerators as $item) {
                    $values = $item->toArray();
                    unset($values['id']);
                    $newItem = $modTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $preNotifications = $notiTable->fetchAll($notiTable->select()->where('form_id = ?', $id));
                foreach ($preNotifications as $item) {
                    $values = $item->toArray();
                    unset($values['notification_id']);
                    $newItem = $notiTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $preConfirmations = $confTable->fetchAll($confTable->select()->where('form_id = ?', $id));
                foreach ($preConfirmations as $item) {
                    $values = $item->toArray();
                    unset($values['confirmation_id']);
                    $newItem = $confTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                foreach ($roles as $i => $role)
                {
                    $auth->setAllowed($new_form, $role, 'view', 1);
                    $auth->setAllowed($new_form, $role, 'comment', 1);
                    $auth->setAllowed($new_form, $role, 'submission', 1);
                }

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            return $this -> _forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The form is cloned successfully.'))
            ));



//            return $this -> _forward('success', 'utility', 'core', array(
//                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
//                    'module' => 'yndynamicform',
//                    'controller' => 'form',
//                    'action' => 'main-info',
//                    'form_id' => $new_form -> getIdentity()
//                ), 'admin_default', true),
//                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The form cloned successfully.'))
//            ));
        }

        // Output
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> renderScript('admin-form/clone.tpl');
    }
    // Other
    
    public function orderAction()
    {
        if( !$this->getRequest()->isPost() ) {
            return;
        }

        // Get params
        $fieldOrder = (array) $this->_getParam('fieldOrder');
        $optionOrder = (array) $this->_getParam('optionOrder');

        // Sort
        ksort($fieldOrder, SORT_NUMERIC);
        ksort($optionOrder, SORT_NUMERIC);

        // Get data
        $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
        $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
        $optionData = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

        // Parse fields (maps)
        $i = 0;
        foreach( $fieldOrder as $index => $ids ) {
            $map = $mapData->getRowMatching(array(
                'field_id' => $ids['parent_id'],
                'option_id' => $ids['option_id'],
                'child_id' => $ids['child_id'],
            ));
            $map->order = ++$i;
            $map->save();
        }

        // Parse options
        $i = 0;
        foreach( $optionOrder as $index => $ids ) {
            $option = $optionData->getRowMatching('option_id', $ids['suboption_id']);
            $option->order = ++$i;
            $option->save();
        }

        // Flush cache
        $mapData->getTable()->flushCache();
        $metaData->getTable()->flushCache();
        $optionData->getTable()->flushCache();

        $this->view->status = true;
    }

    public function getTotalPageBreaks()
    {
        $option_id = $this->_getParam('option_id', 0);
        $count = 0;
        // get first page break
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('option_id', $option_id);
        foreach( $fieldMaps as $map ) {
            $field = $map->getChild();
            if ($field->type == 'page_break') {
                $count++;
            }
        }

        return $count;
    }


    public function listAction()
    {
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->form_id = $form_id =$this -> _getParam('form_id');
        $this->view->form = $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        // CHECK FOR FORM EXISTENCE
        $id = $this -> _getParam('form_id', null);
        if( !$id || !$form = Engine_Api::_() -> getItem('yndynamicform_form', $id))
        {
            $this -> _helper -> requireSubject()->forward();
            return;
        }

        $this->view->page_no = $page_no = $this -> _getParam('page', 1);
        $params = $this->_getAllParams();

        //fetch only submitted result

        if(!$params['tab']){
            $params['tab'] = 'form_submitted';
        }

        $form_submitted_page_no = 1;
        $form_assigned_page_no = 1;

        if($params['tab'] == 'form_submitted'){
            if(!empty($page_no)){
                $form_submitted_page_no = $page_no;
            }
        }

        if($params['tab'] == 'form_assigned'){
            if(!empty($page_no)){
                $form_assigned_page_no = $page_no;
            }
        }
        
        $this->view->totalSubmission = Engine_Api::_()->impactx()->getTotalSubmittedEntries($form_id);
        
//        $this->view->totalSubmission = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getTotalSubmittedEntries($form_id);
        $this->view->totalAssign = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->totalFormByPageId($form_id,$page_id);

//        $form_submitted_paginator = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getSubmittedEntries($form_id,$form_submitted_page_no);
        $form_submitted_paginator = Engine_Api::_()->impactx()->getSubmittedEntries($form_id, $form_submitted_page_no);
        $this->view->form_submitted_paginator = $form_submitted_paginator;

        $form_assigned_paginator = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->formByPageId($form_id,$page_id,$form_assigned_page_no);
        $this -> view ->form_assigned_paginator = $form_assigned_paginator;

       if($params['tab'] == 'form_submitted') {
           $this -> view -> tab = 'form_submitted';
       }else if($params['tab'] == 'form_assigned') {
           $this -> view -> tab = 'form_assigned';
       }

        $this -> view -> params = $params;
        $this -> view -> yndform = $form;
    }

    public function selectProjectAction() {


        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET PAGE ID, PAGE OBJECT AND PAGE VALIDAITON
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->form_id = $form_id = $this->_getParam('form_id');
        $this->view->tab_link = $tab_link = $this->_getParam('tab_link');
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id',null);


        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->searchForm = $searchForm = new Sitepage_Form_ProjectsFilter();
        $searchForm->populate($_POST);

        $this->view->sort_field = $_POST['sort_field'];
        $this->view->sort_direction = $_POST['sort_direction'];

        $projectFormTable = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        $assignProjectsIds =  $projectFormTable->getProjectIdsByFormIdPageId($form_id,$page_id);



        if($tab_link == 'all_projects') {
            $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getAllActiveProjectsByPageId($page_id);
            $ids = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getProjectAssiginedCount($form_id,$page_id);

            $this->view->assignStatus = $assignStatus = count($ids) == count($projectsIds) ? true  : false;

        }

        if($tab_link == 'projects_assigned') {
            $this->view->projectsIds = $projectsIds =Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getProjectsIdsAssignedByformId($form_id);
        }

        if($tab_link == 'projects_byinitiative') {
            $this->view->initiatives = $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);
            $id = count($initiatives)   > 0 && $initiatives[0]['initiative_id'] ? $initiatives[0]['initiative_id'] : null;
            $initiative_id = $initiative_id ? $initiative_id : $id;

            if($initiative_id) {
                $this->view->initiative_id = $initiative_id;
                $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageIdAndInitiativesId($page_id,$initiative_id,null);
                $projectsIds  = [];
                foreach ($projects as $project) {
                    array_push($projectsIds,$project['project_id']);
                }
                //  print_r(array_intersect($projectsIds, $assignProjectsIds));
                //  print_r($projectsIds);
                $this->view->projectsIds = $projectsIds;
                $this->view->assignStatus = $assignStatus = count(array_intersect($projectsIds, $assignProjectsIds)) == count($projectsIds) ? true  : false;

            }

        }

        $allProjectParams = array();
        $allProjectParams['page'] = $this->_getParam('page', 1);
        $allProjectParams['project_ids'] = $projectsIds;

        if(!empty($projectsIds) && count($projectsIds) > 0
            && ($tab_link != 'all_projects_users' && $tab_link != 'projects_admins' && $tab_link != 'projects_members'
            && $tab_link != 'all_users' && $tab_link != 'org_admins' && $tab_link != 'org_members' )) {

            $values = $searchForm->getValues();
            if (isset($_POST['search'])) {
                $allProjectParams['project_name'] = $values['project_name'];
                $allProjectParams['project_id'] = $values['project_id'];
                $allProjectParams['user_name'] = $values['user_name'];
                $allProjectParams['user_id'] = $values['user_id'];
                $allProjectParams['project_status'] = $values['project_status'];
                $allProjectParams['funding_status'] = $values['funding_status'];
                $allProjectParams['is_published_yn'] = $values['is_published_yn'];
                $allProjectParams['is_funding_enabled_yn'] = $values['is_funding_enabled_yn'];
                $allProjectParams['is_payment_edit'] = $values['is_payment_edit'];
                $allProjectParams['goal_amount_min'] = $values['goal_amount_min'];
                $allProjectParams['goal_amount_max'] = $values['goal_amount_max'];
                $allProjectParams['project_order'] = $values['project_order'];
                $allProjectParams['sort_field'] = $values['sort_field'];
                $allProjectParams['sort_direction'] = $values['sort_direction'];
            }

            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageProjectsByParamsPaginator($allProjectParams);

        }
        $this->view->flag =  false;
        if($tab_link == 'all_users') {

            $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
            $this->view->orgmembers  = $orgmembers = $membershipTable->getallsitepagemembersSelect($page_id);

            $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
            $this->view->manageAdminUsers= $manageAdminUsers = $manageadminsTable->getManageAdminIds($page_id,null);
            $ids = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);


            //admins
            $arr= [];
            foreach ($manageAdminUsers as $i) {
                if($i)
                    array_push($arr,$i);
            }
             // members
            foreach ($orgmembers as $i) {
                if($i->user_id)
                   array_push($arr,$i->user_id);
            }
            //all assigned in form tables
            $Id= [];
            foreach ($ids as $i) {
                array_push($Id,$i['user_id']);
            }


            $this->view->paginatorss = $paginatorss = array_unique($arr);
            $this->view->userIds = array_unique($arr);



            $this->view->assignStatus = $assignStatus = count(array_intersect($Id, $arr)) == count(array_unique($arr)) ? true  : false;


        }
        if($tab_link == 'org_admins') {
            $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
            $this->view->manageAdminUsers= $manageAdminUsers = $manageadminsTable->getManageAdminIds($page_id,null);
            $ids = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);

           $paginatorss = $manageAdminUsers;

            $arr= [];
            foreach ($paginatorss as $i) {
                if($i && $i!=0)
                   array_push($arr,$i);
            }

            $Id= [];
            foreach ($ids as $i) {
                array_push($Id,$i['user_id']);
            }

            $this->view->paginatorss = $paginatorss = array_unique($arr);
            $this->view->userIds = $arr;



            $this->view->assignStatus = $assignStatus = count(array_intersect($Id, $arr)) == count(array_unique($arr)) ? true  : false;


        }
        if($tab_link == 'org_members') {
            $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
            $this->view->orgmembers  = $orgmembers = $membershipTable->getallsitepagemembersSelect($page_id);
            $ids = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);

            $this->view->flag =  false;
            $paginatorss = $orgmembers;

            $arr= [];
            foreach ($paginatorss as $i) {
                if($i->user_id)
                 array_push($arr,$i->user_id);
            }
            $Id= [];
            foreach ($ids as $i) {
                array_push($Id,$i['user_id']);
            }

            $this->view->paginatorss = $paginatorss = array_unique($arr);


            $this->view->userIds = $arr;
            $this->view->assignStatus = $assignStatus = count(array_intersect($Id, $arr)) == count(array_unique($arr)) ? true  : false;


        }
        if($tab_link == 'all_project_users') {

                $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getAllActiveProjectsByPageId($page_id);
                $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
                $this->view->manageAdminUsers= $manageAdminUsers = $manageadminsTable->getManageAdminIds($page_id,null);

                $this->view->paginatorss = $paginatorss = $manageAdminUsers;
                $idss = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);

               //admins
                $adminUs = array();
                $c= 0;
                foreach ($projectsIds as $ids) {


                    $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$ids['project_id']);
                    $this->view->list = $list = $project->getLeaderList();

                    $list_id = $list['list_id'];

                    $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                    $listItemTableName = $listItemTable->info('name');

                    $userTable = Engine_Api::_()->getDbtable('users', 'user');
                    $userTableName = $userTable->info('name');
                    $selectLeaders = $listItemTable->select()
                        ->from($listItemTableName, array('child_id'))
                        ->where("list_id = ?", $list_id)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                    $selectLeaders[] = $project->owner_id;

                    $select = $userTable->select()
                        ->from($userTableName, array('user_id'))
                        ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                        ->order('displayname ASC');

                    $this->view->adminMembers = $adminMembers = $select->query()->fetchAll();
                    // array_push($adminUs,$adminMembers[0]);
                    $adminUs = array_merge($adminUs,$adminMembers);

                }

                $this->view->userIds = $adminUs;


                $this->view->flag = 'p_memebers';

                $a = array_unique($adminUs, SORT_REGULAR);
                $b = $idss;

                $arr1= [];
                foreach ($a as $i) {
                    array_push($arr1,$i['user_id']);
                }

                $arr2 = [];
                foreach ($b as $i) {
                    array_push($arr2,$i['user_id']);
                }

            //members
            $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            $projectTablesName = $projectTable->info('name');

            $currentDate = date('Y-m-d H:i:s');
            $membershipsTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');

            $membershipsName = $membershipsTable->info('name');

            $select1 = $membershipsTable->select()
                ->from($membershipsTable->info('name'), 'user_id')
                ->joinRight($projectTablesName, "$projectTablesName.project_id = $membershipsName.project_id ",array())
                ->where("$membershipsName.user_id <> (?)", 0)
                ->where("$membershipsName.project_id IN (?)", $projectsIds);

            $result =  $select1->query()->fetchAll();

            $finalData = array_unique($result, SORT_REGULAR);


            $finalData1 =  array_unique($adminUs, SORT_REGULAR);

           //combine both users
            $this->view->paginatorss = $paginatorss = array_unique(array_merge($finalData,$finalData1), SORT_REGULAR);



            $this->view->flag = 'p_memebers';



            $arr11= [];
            foreach ($finalData as $i) {
                array_push($arr11,$i['user_id']);
            }
            $arr1  = array_merge($arr1,$arr11);
            $this->view->userIds =  array_unique(array_merge($finalData,$finalData1), SORT_REGULAR);



            $this->view->assignStatus = $assignStatus  = count(array_intersect($arr1,$arr2)) ==  count($arr1) ? true  : false;


        }
        if($tab_link == 'project_admins') {
            $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getAllActiveProjectsByPageId($page_id);
            $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
            $this->view->manageAdminUsers= $manageAdminUsers = $manageadminsTable->getManageAdminIds($page_id,null);

            $this->view->paginatorss = $paginatorss = $manageAdminUsers;
            $idss = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);


            $adminUs = array();
            $c= 0;
            foreach ($projectsIds as $ids) {


                $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$ids['project_id']);
                $this->view->list = $list = $project->getLeaderList();

                $list_id = $list['list_id'];

                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');
                $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                $selectLeaders[] = $project->owner_id;

                $select = $userTable->select()
                    ->from($userTableName, array('user_id'))
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $this->view->adminMembers = $adminMembers = $select->query()->fetchAll();
                // array_push($adminUs,$adminMembers[0]);
                $adminUs = array_merge($adminUs,$adminMembers);

            }

            $this->view->userIds = $adminUs;

            $this->view->paginatorss = $paginatorss =array_unique($adminUs, SORT_REGULAR);
            $this->view->flag = 'p_memebers';

            $a = array_unique($adminUs, SORT_REGULAR);
            $b = $idss;

            $arr1= [];
            foreach ($a as $i) {
                array_push($arr1,$i['user_id']);
            }

            $arr2 = [];
            foreach ($b as $i) {
                array_push($arr2,$i['user_id']);
            }
            $this->view->assignStatus = $assignStatus  = count(array_intersect($arr1,$arr2)) ==  count($arr1) ? true  : false;


        }
        if($tab_link == 'project_members') {
            $this->view->projectsIds =     $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getAllActiveProjectsByPageId($page_id);

            // $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getAllActiveProjectsByPageId($page_id);
            $ids = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCount($form_id,$page_id);



            $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            $projectTablesName = $projectTable->info('name');

            $currentDate = date('Y-m-d H:i:s');
            $membershipsTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');

            $membershipsName = $membershipsTable->info('name');

            $select1 = $membershipsTable->select()
                ->from($membershipsTable->info('name'), 'user_id')
                ->joinRight($projectTablesName, "$projectTablesName.project_id = $membershipsName.project_id ",array())
                ->where("$membershipsName.user_id <> (?)", 0)
                ->where("$membershipsName.project_id IN (?)", $projectsIds);

            $result =  $select1->query()->fetchAll();

            $finalData = array_unique($result, SORT_REGULAR);
            $this->view->paginatorss = $paginatorss = $finalData;
            $this->view->flag = 'p_memebers';

            $this->view->userIds = $finalData;

            $arr1= [];
            foreach ($finalData as $i) {
                array_push($arr1,$i['user_id']);
            }

            $arr2 = [];
            foreach ($ids as $i) {
                array_push($arr2,$i['user_id']);
            }

            $this->view->assignStatus = $assignStatus  = count(array_intersect($arr1,$arr2)) ==  count($arr1) ? true  : false;



        }

    }


    public function assignFormToUserAction() {


        $page_id = $_POST['page_id'];
        $user_id = $_POST['user_id'];
        $form_id = $_POST['form_id'];

        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        $assign_status =  $tablePage->getUserAssiginedCountByFormId($form_id,$user_id);

        if((int)$assign_status == 0){
            $pagerow = $tablePage->createRow();
            $pagerow->user_id = $user_id;
            $pagerow->page_id = $page_id;
            $pagerow->form_id = (int)$form_id;
            // $pagerow->status = '';
            $pagerow->save();

            //send notification
            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notificationType = 'yndynamicform_user_assign_form';
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
            $viewer = Engine_Api::_() -> user() -> getViewer();


            $user = Engine_Api::_()->user()->getUser( $user_id);
            //send mail
            $type='user';
            $this->sendEmail($user,$user,$yndform,$type);

            //  return true;
            return;
        }else {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->query("DELETE FROM engine4_sitepage_projectforms WHERE user_id = '$user_id' AND form_id = '$form_id'");

            //   return true;
            return;
        }

    }

    //assign form to all users
    public function assignFormToAllUsersAction() {
        $page_id = $_POST['page_id'];
        $status = $_POST['status'];
        $form_id = $_POST['form_id'];
        $projectsIds = $_POST['userIds'];


        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');


        $projIds = json_decode($projectsIds);


        // assign to all projects ids
        if($status == 1 || $status == '1'){

            $i  = 0;
            static $sql='';
            $queryVals[]=null;
            $db = Engine_Db_Table::getDefaultAdapter();

            $data[]=null;
            $userid[]=null;

            foreach ($projIds as $id) {

                if($id->user_id) {
                    $id = $id->user_id;
                }

                // delete and add row to form
//                Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => (int)$id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
//                $pagerow = $tablePage->createRow();
//                $pagerow->user_id = (int)$id;
//                $pagerow->page_id = (int)$page_id;
//                $pagerow->form_id = (int)$form_id;
//                $pagerow->save();



                $db = Engine_Db_Table::getDefaultAdapter();
//                $db->insert('engine4_sitepage_projectforms', array(
//                    'user_id' => (int)$id,
//                    'page_id' => (int)$page_id,
//                    'form_id' =>(int)$form_id
//                ));


               // $sql = $sql + '(13, 328, '.(int)$id.'),';
                $data[] = '('.(int)$page_id.','.(int)$form_id.','.(int)$id.')';
                $userid[] = (int)$id;
                $i++;

                //send notification
                //     $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                //     $notificationType = 'yndynamicform_user_assign_form';
                   $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
                //    $viewer = Engine_Api::_() -> user() -> getViewer();


               //send mail
                $user = Engine_Api::_()->user()->getUser((int)$id);
                //send mail
                $type='user';
                $this->sendEmail($user,$user,$yndform,$type);


            }

            $table = Engine_Api::_() -> getDbTable('projectforms', 'sitepage');

            unset($data[0]);
            $tempData = implode(",", $data);
            unset($userid[0]);
            $tempIds = implode(",", $userid);
            $tempIds= '('.$tempIds.')';

             $stmt1 = $table->getAdapter()->prepare('DELETE FROM engine4_sitepage_projectforms where page_id='.(int)$page_id.' and form_id='.(int)$form_id.' and user_id IN '.$tempIds);
             $stmt1->execute();
             $stmt = $table->getAdapter()->prepare('INSERT INTO engine4_sitepage_projectforms (page_id, form_id, user_id) VALUES '.$tempData);
             $stmt->execute();



        }
        else {

            $i  = 0;
            foreach ($projIds as $id) {

                if($id->user_id) {
                    $id = $id->user_id;
                }
                Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => $id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
                $i++;
            }

            if($i == count($projIds) - 1) {
                return;
            }

        }
    }

    public function assignFormAction() {


        $page_id = $_POST['page_id'];
        $project_id = $_POST['project_id'];
        $form_id = $_POST['form_id'];

        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        $assign_status =  $tablePage->getProjectAssiginedCountByFormId($form_id,$project_id);

        if((int)$assign_status == 0){
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('project_id =?' => (int)$project_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
            $pagerow = $tablePage->createRow();
            $pagerow->project_id = $project_id;
            $pagerow->page_id = $page_id;
            $pagerow->form_id = (int)$form_id;
            // $pagerow->status = '';
            $pagerow->save();

            /***
             *
             * send notification and email to all project admins
             *
             ***/
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $list = $project->getLeaderList();
            $list_id = $list['list_id'];

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');
            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userTableName = $userTable->info('name');

            $selectLeaders = $listItemTable->select()
                ->from($listItemTableName, array('child_id'))
                ->where("list_id = ?", $list_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $project->owner_id;
            $selectUsers = $userTable->select()
                ->from($userTableName)
                ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                ->order('displayname ASC');

            $adminMembers = $userTable->fetchAll($selectUsers);

            // loop and send notification/email
            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notificationType = 'yndynamicform_user_assign_form';
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

            foreach($adminMembers as $adminMember){
                $user_id = $adminMember->user_id;
                $owner = Engine_Api::_()->getItem('user', $user_id);
                $notificationTable->addNotification($owner, $project, $yndform, $notificationType);

                $type='project';
                $this->sendEmail($owner,$project,$yndform,$type);
            }

            //  return true;
            return;
        }else {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->query("DELETE FROM engine4_sitepage_projectforms WHERE project_id = '$project_id' AND form_id = '$form_id'");

            //   return true;
            return;
        }

    }
    public function assignFormToAllProjectsAction() {
        $page_id = $_POST['page_id'];
        $status = $_POST['status'];
        $form_id = $_POST['form_id'];
        $projectsIds = $_POST['projectsIds'];

        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        //  $assign_status =  $tablePage->getProjectAssiginedCountByFormId($form_id,$project_id);

        $projIds = json_decode($projectsIds);

        // assign to all projects ids
        if($status == 1 || $status == '1'){

            $i  = 0;

            foreach ($projIds as $id) {

                if($id->project_id) {
                    $id = $id->project_id;
                }

                // delete and add row to form
//                Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('project_id =?' => (int)$id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
//                $pagerow = $tablePage->createRow();
//                $pagerow->project_id = (int)$id;
//                $pagerow->page_id = (int)$page_id;
//                $pagerow->form_id = (int)$form_id;
//                $pagerow->save();
                $data[] = '('.(int)$page_id.','.(int)$form_id.','.(int)$id.')';
                $userid[] = (int)$id;
                $i++;


                /***
                 *
                 * send notification and email to all project admins
                 *
                 ***/
                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $id);
                $list = $project->getLeaderList();
                $list_id = $list['list_id'];

                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');
                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                $selectLeaders[] = $project->owner_id;
                $selectUsers = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $adminMembers = $userTable->fetchAll($selectUsers);

                // loop and send notification/email
                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notificationType = 'yndynamicform_user_assign_form';
                $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

                foreach($adminMembers as $adminMember){
                    $user_id = $adminMember->user_id;
                    $owner = Engine_Api::_()->getItem('user', $user_id);
                    // send notification
                    $notificationTable->addNotification($owner, $project, $yndform, $notificationType);

                    $type='project';
                    $this->sendEmail($owner,$project,$yndform,$type);
                }

            }


            $table = Engine_Api::_() -> getDbTable('projectforms', 'sitepage');

           // unset($data[0]);
            $tempData = implode(",", $data);

            $tempIds = implode(",", $userid);
            $tempIds= '('.$tempIds.')';



            $stmt1 = $table->getAdapter()->prepare('DELETE FROM engine4_sitepage_projectforms where page_id='.(int)$page_id.' and form_id='.(int)$form_id.' and project_id IN '.$tempIds);
            $stmt1->execute();
            $stmt = $table->getAdapter()->prepare('INSERT INTO engine4_sitepage_projectforms (page_id, form_id, project_id) VALUES '.$tempData);
            $stmt->execute();



            if($i == count($projIds) - 1) {
              
                return;
            }

        }
        else {

            $i  = 0;
            foreach ($projIds as $id) {

                if($id->project_id) {
                    $id = $id->project_id;
                }
                Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('project_id =?' => $id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
                $i++;
            }

            if($i == count($projIds) - 1) {
                return;
            }

        }
    }


    public function sendEmail($owner,$object,$yndform,$type) {

        $view = Zend_Registry::get('Zend_View');
        $host = $_SERVER['HTTP_HOST'];

        $newVar = _ENGINE_SSL ? 'https://' : 'http://';

        if($_SERVER['SERVER_NAME'] == 'stage.impactx.co'){
            if($type == 'project'){
                $url =   $newVar . $_SERVER['HTTP_HOST'] .'/network/dynamic-form/entry/create/1/form_id/'. $yndform->getIdentity().'/project_id/'.$object->getIdentity();
                $project_name = $view->htmlLink($host . $object->getHref(), $object->getTitle());
            } else{
                $url =   $newVar . $_SERVER['HTTP_HOST'] .'/network/dynamic-form/entry/create/1/form_id/'. $yndform->getIdentity().'/user_id/'.$object->getIdentity();
                $user_name = $view->htmlLink($host . $object->getHref(), $object->getTitle());
            }
        } else{
            if($type == 'project'){
                $url =   $newVar . $_SERVER['HTTP_HOST'] .'/net/dynamic-form/entry/create/1/form_id/'. $yndform->getIdentity().'/project_id/'.$object->getIdentity();
                $project_name = $view->htmlLink($host . $object->getHref(), $object->getTitle());
            }else{
                $url =   $newVar . $_SERVER['HTTP_HOST'] .'/net/dynamic-form/entry/create/1/form_id/'. $yndform->getIdentity().'/user_id/'.$object->getIdentity();
                $user_name = $view->htmlLink($host . $object->getHref(), $object->getTitle());
            }
        }
        $member_name = $view->htmlLink($host . $owner->getHref(), $owner->getTitle());

        //send mail
        if($type == 'project'){
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'notify_yndynamicform_form_assign', array(
                'project_name' => $project_name,
                'form_name' => $yndform->getTitle(),
                'form_link' => $url,
                'member_name'  => $member_name,
                'queue' => false
            ));
        }else{
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'notify_yndynamicform_form_assigned_to_user', array(
                'user_name' => $user_name,
                'form_name' => $yndform->getTitle(),
                'form_link' => $url,
                'member_name'  => $member_name,
                'queue' => false
            ));
        }



   }





    public function assignFormsToUserAction() {


        $page_id = $_POST['page_id'];
        $user_id = $_POST['user_id'];
        $form_id = $_POST['form_id'];
        $status = $_POST['status'];


        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        //$assign_status =  $tablePage->getProjectAssiginedCountByFormIds($form_id,$project_id);

        if((int)$status == 0){
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => $user_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));


            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'user_id' => $user_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));
        }else {

//            $db = Engine_Db_Table::getDefaultAdapter();
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => $user_id,  'page_id =?' => $page_id,'form_id =?' => $form_id));
            $pagerow = $tablePage->createRow();
            $pagerow->project_id = null;
            $pagerow->user_id = $user_id;
            $pagerow->form_id = (int)$form_id;
            $pagerow->save();

            //send notification
            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notificationType = 'yndynamicform_user_assign_form';
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
            $viewer = Engine_Api::_() -> user() -> getViewer();


          //  $notificationTable->addNotification($owner, $project, $yndform, $notificationType);


            //send mail
//            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'notify_yndynamicform_form_assign', array(
//                'project_name' => $project->getTitle(),
//                'project_link' => $project->getHref(),
//                'form_name' => $yndform->getTitle(),
//                'form_link' =>      'http://' . $_SERVER['HTTP_HOST'] .'/dynamic-form/entry/create/1/form_id/'. $yndform->getIdentity().'/project_id/'.$project->getIdentity(),
//                'member_link'=> $owner->getHref(),
//                'member_name'  => $owner->getTitle()
//
//            ));


            //  return true;

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'page_id' => $page_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));

        }

    }


    public function assignFormsAction() {


        $page_id = $_POST['page_id'];
        $project_id = $_POST['project_id'];
        $form_id = $_POST['form_id'];
        $status = $_POST['status'];


        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        //$assign_status =  $tablePage->getProjectAssiginedCountByFormIds($form_id,$project_id);

        if((int)$status == 0){
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('project_id =?' => $project_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));


            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'page_id' => $page_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));
        }else {

//            $db = Engine_Db_Table::getDefaultAdapter();
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('project_id =?' => $project_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
            $pagerow = $tablePage->createRow();
            $pagerow->project_id = $project_id;
            $pagerow->page_id = $page_id;
            $pagerow->form_id = (int)$form_id;
            $pagerow->save();

            //send notification
            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notificationType = 'yndynamicform_user_assign_form';
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
            $viewer = Engine_Api::_() -> user() -> getViewer();

            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $owner = $project->getOwner();
            $notificationTable->addNotification($owner, $project, $yndform, $notificationType);


            //send mail
            $type = 'user';
       //     $this->sendEmail($owner,$project,$yndform,$type);


            //  return true;

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'page_id' => $page_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));

        }

    }


    public function assignFormsUserAction() {


        $page_id = $_POST['page_id'];
        $user_id = $_POST['user_id'];
        $form_id = $_POST['form_id'];
        $status = $_POST['status'];


        // insert into project's organisation
        $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
        //$assign_status =  $tablePage->getProjectAssiginedCountByFormIds($form_id,$project_id);

        if((int)$status == 0){
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => $user_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));


            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'page_id' => $page_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));
        }else {

//            $db = Engine_Db_Table::getDefaultAdapter();
            Engine_Api::_()->getDbtable('projectforms', 'sitepage')->delete(array('user_id =?' => $user_id, 'page_id =?' => $page_id,'form_id =?' => $form_id));
            $pagerow = $tablePage->createRow();
            $pagerow->user_id = $user_id;
            $pagerow->page_id = $page_id;
            $pagerow->form_id = (int)$form_id;
            $pagerow->save();

            //send notification
            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notificationType = 'yndynamicform_user_assign_form';
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
            $viewer = Engine_Api::_() -> user() -> getViewer();

            $user = Engine_Api::_()->getItem('sitecrowdfunding_project', $user_id);
            //  $owner = $project->getOwner();
            //  $notificationTable->addNotification($user, $user, $yndform, $notificationType);


            //send mail
            $type = 'user';
            $this->sendEmail($user,$user,$yndform,$type);


            //  return true;

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'organizations',
                    'controller' => 'manageforms',
                    'action' => 'moderators',
                    'form_id' => $form_id,
                    'page_id' => $page_id
                ), 'default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
            ));

        }

    }


    public function updateStatusAction()
    {

        $form_id = $_POST['form_id'];
        $status = $_POST['status'] ?  false: true;

        $packageTable = Engine_Api::_()->getDbtable('forms', 'yndynamicform');

        // Update default
        $packageTable->update(array(
            'enable' => $status,
        ), array(
            'form_id = ?' => $form_id,
        ));


    }
    public function updateEntryEditStatusAction()
    {

        $entry_id = $_POST['entry_id'];
        $status = $_POST['status'] ;

        echo "check1--".$status;echo "<br>";
        echo "check2--".$status == true; echo "<br>";
        echo "check3--".$status == "true"; echo "<br>";




        if( $status == "true" ){
            $status= 1;
            echo "if--".$status;
        }else{
            $status= 0;
            echo "else--".$status;
        }
        $packageTable = Engine_Api::_()->getDbtable('entries', 'yndynamicform');

        // Update default
        $packageTable->update(array(
            'allow_edit' =>  $status ,
        ), array(
            'entry_id = ?' => $entry_id,
        ));


    }
    /*
     * Change the publish status for the entry
     */
    public function updateEntryPublishStatusAction()
    {
        $entry_id = $_POST['entry_id'];
        $status = $_POST['status'];

        $status = ($status == "true")? 1: 0;
        $packageTable = Engine_Api::_()->getDbtable('entries', 'yndynamicform');

        // Update default
        $packageTable->update(array(
            'publish' =>  $status ,
        ), array(
            'entry_id = ?' => $entry_id,
        ));
    }

    public function viewsAction()
    {
        // Check permission
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return;
        }
        $this -> view -> entry = $entry = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);

        if (!$entry -> isViewable()) {
            $this -> _helper -> requireAuth -> forward();
        }

        $entry->updateView();

        //Get Field_View Helper
        $view = Zend_Registry::get('Zend_View');

        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yndynamicform/View/Helper', 'Yndynamicform_View_Helper');

        // Render
        $this -> _helper -> content -> setEnabled();

        if (!$this -> getRequest() -> isPost()) {
            return;
        }
    }

    /// all multiple forms ///
    public function managebackupAction() {
        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm();

        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);



        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getFormByPageId($page_id);


        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $form = Engine_Api::_()->getItem('sesmultipleform_form', $value)->delete();
                }
            }
        }
        $this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
        $page = $this->_getParam('page', 1);
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($page);


        // Setup
        $viewer = Engine_Api::_()->user()->getViewer();
        if($this->_getParam('id',false))
            $this->view->formobj = $formObj = Engine_Api::_()->getItem('sesmultipleform_form', $this->_getParam('id'));
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesmultipleform')->profileFieldId();
        // $this->view->form = $form = new Sesmultipleform_Form_Admin_Manageform(array('defaultProfileId' => $defaultProfileId, 'formId'=>$formObj->form_id));
//        $form->populate($formObj->toArray());
//        // Check method/valid
//        if( !$this->getRequest()->isPost() ) {
//            return;
//        }
//        if( !$form->isValid($this->getRequest()->getPost()) ) {
//            // return;
//        }
//        // Process
//        $db = Engine_Db_Table::getDefaultAdapter();
//        $db->beginTransaction();
//        try {
//            $values = $form->getValues();
//            $formObj->setFromArray($values);
//            $formObj->save();
//            $db->commit();
//        } catch( Exception $e ) {
//            $db->rollBack();
//            throw $e;
//        }
//        if (isset($_POST['submitsave'])){
//            $formUrl = rtrim($this->view->baseUrl(), '/') . '/admin/sesmultipleform/forms';
//            // Redirect
//            return $this->_helper->redirector->gotoUrl($formUrl, array('prependBase' => false));
//        }
//
//
//
//





    }
    public function createFormAction() {

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $this->_helper->layout->setLayout('admin-simple');
        $this->view->form = $form = new Sesmultipleform_Form_Admin_Form();
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($this->getRequest()->getPost()))
                return;
            $db = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getAdapter();
            $db->beginTransaction();
            try {
                $table = Engine_Api::_()->getDbtable('forms', 'sesmultipleform');
                $values = $form->getValues();
                $forms = $table->createRow();
                $forms->setFromArray($values);
                $forms->creation_date = date('Y-m-d h:i:s');
                $forms->save();
                $forms->order=$forms->form_id;
//                $forms->page_id=$page_id;
//                print_r($forms);die();
                $forms->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            die();
            if($this->_getParam('category',false))
                $redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'index','id'=>$forms->getIdentity()),'admin_default',true);
            else
                $redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesmultipleform', 'controller' => 'settings', 'action' => 'advance-setting','id'=>$forms->getIdentity()),'admin_default',true);
            $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => $redirect,
                'parentRefresh' => 10,
                'messages' => array('Form created successfully.')
            ));
        }
    }
    public function entryAction(){
        $id = $this->_getParam('id',false);
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
        $this->view->formFilter = $formFilter = new Sesmultipleform_Form_Admin_Filter(array('formId'=>$id));
        $this->view->formObj = Engine_Api::_()->getItem('sesmultipleform_form', $id);
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $entry = Engine_Api::_()->getItem('sesmultipleform_entry', $value);
                    $entry->delete();
                }
            }
        }
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        if (isset($_GET) && !empty($_GET['category_id'])) {
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
            $category_select = $categoryTable->select()
                ->from($categoryTable->info('name'))
                ->where('subcat_id = ?', $_GET['category_id'])
                ->where('form_id =?',$id);
            $subcategory = $categoryTable->fetchAll($category_select);
            $count_subcat = count($subcategory->toarray());
            $data = '';
            if ($subcategory && $count_subcat) {
                $data = array();
                $data[0] = 'Select';
                foreach ($subcategory as $category) {
                    $data[$category['category_id']] = $category['title'];
                }
                if (!empty($data) && $formFilter->getElement('subcat_id'))
                    $formFilter->getElement('subcat_id')->addMultiOptions($data);
            }
        }
        if (isset($_GET) && !empty($_GET['subcat_id'])) {
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
            $category_select = $categoryTable->select()
                ->from($categoryTable->info('name'))
                ->where('subsubcat_id = ?', $_GET['subcat_id'])
                ->where('form_id =?',$id);
            $subcategory = $categoryTable->fetchAll($category_select);
            $count_subcat = count($subcategory->toarray());
            $data = '';
            if ($subcategory && $count_subcat) {
                $data = array();
                $data[0] = 'Select';
                foreach ($subcategory as $category) {
                    $data[$category['category_id']] = $category['title'];
                }
                if (!empty($data) && $formFilter->getElement('subsubcat_id'))
                    $formFilter->getElement('subsubcat_id')->addMultiOptions($data);
            }
        }
        $this->view->assign($values);
        $entryTable = Engine_Api::_()->getDbTable('entries', 'sesmultipleform');
        $entryTableName = $entryTable->info('name');
        $select = $entryTable->select()->order('entry_id DESC')->where('form_id =?',$id);
        if (!empty($values['name']))
            $select->where('name LIKE ?', '%' . $values['name'] . '%');
        if (!empty($values['email']))
            $select->where('email LIKE ?', '%' . $values['email'] . '%');
        if (!empty($values['creation_date']))
            $select->where('creation_date =?', $values['creation_date']);
        if (!empty($_GET['category_id']))
            $select->where('category_id =?', $_GET['category_id']);
        if (!empty($_GET['subcat_id']))
            $select->where('subcat_id =?', $_GET['subcat_id']);
        if (!empty($_GET['subsubcat_id']))
            $select->where('subsubcat_id =?', $_GET['subsubcat_id']);
        if (!empty($values['description']))
            $select->where('description LIKE ?', '%' . $values['body'] . '%');
        $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator;
        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }
    public function advanceSettingAction() {
        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);





        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
        // Setup

        $this->view->form_id = $id = $this->_getParam('id');
        $this->view->formObj = $formObj =  Engine_Api::_()->getItem('sesmultipleform_form',$id);
        $this->view->formset = $formset = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getSetting(array('id'=> $id));
        $this->view->form = $form = new Sesmultipleform_Form_Admin_Settings_Advance();
        if(($formset)){
            $itemArray = $formset->toArray();
            $form->populate($itemArray);
        }
        $form->populate($formObj->toArray());
        // Check method/valid
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        // Process
        $db = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getAdapter();
        $db->beginTransaction();
        try {
            if(!($formset)){
                $table = Engine_Api::_()->getDbtable('settings', 'sesmultipleform');
                $formset = $table->createRow();
            }
            $values = $form->getValues();
            $values['form_id'] = $id;
            $formset->setFromArray($values);
            $formset->save();
            //save form
            $formObj->setFromArray($values);
            $formObj->save();
            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
        if (isset($_POST['submitsave'])){
            $formUrl = rtrim($this->view->baseUrl(), '/') . '/admin/sesmultipleform/forms';
            // Redirect
            return $this->_helper->redirector->gotoUrl($formUrl, array('prependBase' => false));
        }
    }
    public function categoriesAction() {


        //GET NAVIGATION

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);





        $this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
        if(!$id){
            $forms = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm(array('fetchAll'=>true,'limit'=>1,'active'=>true));
            if(count($forms)){
                $form_id = $forms[0];
                return $this->_helper->redirector->gotoRoute(array('module' => 'sesmultipleform', 'action' => 'index', 'controller' => 'categories','id' => $form_id->form_id), 'admin_default', true);
            }
        }
        if (isset($_POST['selectDeleted']) && $_POST['selectDeleted']) {
            if (isset($_POST['data']) && is_array($_POST['data'])) {
                $deleteCategoryIds = array();
                foreach ($_POST['data'] as $key => $valueSelectedcategory) {
                    $categoryDelete = Engine_Api::_()->getItem('sesmultipleform_category', $valueSelectedcategory);

                    $deleteCategory = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->deleteCategory($categoryDelete);
                    if ($deleteCategory) {
                        $deleteCategoryIds[] = $categoryDelete->category_id;
                        $categoryDelete->delete();
                    }
                }
                echo json_encode(array('diff_ids' => array_diff($_POST['data'], $deleteCategoryIds), 'ids' => $deleteCategoryIds));die;
            }
        }
        if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == 1) {
            $value['title'] = isset($_POST['title']) ? $_POST['title'] : '';
            $value['form_id'] = isset($_POST['form_id']) ? $_POST['form_id'] : '';
            $value['profile_type'] = isset($_POST['profile_type']) ? $_POST['profile_type'] : '';
            $value['parent'] = $cat_id = isset($_POST['parent']) ? $_POST['parent'] : '';
            if ($cat_id != -1) {
                $categoryData = Engine_Api::_()->getItem('sesmultipleform_category', $cat_id);
                if ($categoryData->subcat_id == 0) {
                    $value['subcat_id'] = $cat_id;
                    $seprator = '&nbsp;&nbsp;&nbsp;';
                    $tableSeprator = '-&nbsp;';
                    $parentId = $cat_id;
                    $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('subcat_id' => $cat_id));
                } else {
                    $value['subsubcat_id'] = $cat_id;
                    $seprator = '3';
                    $tableSeprator = '--&nbsp;';
                    $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('subsubcat_id' => $cat_id));
                    $parentId = $cat_id;
                }
            } else {
                $parentId = 0;
                $seprator = '';
                $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('category_id' => true));
                $tableSeprator = '';
            }
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
                //Create row in categories table
                $row = $categoriesTable->createRow();
                $row->setFromArray($value);
                $row->save();
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $tableData = '<tr id="categoryid-' . $row->category_id . '" data-article-id="' . $row->category_id . '" style="cursor:move;"><td><input type="checkbox" name="delete_tag[]" class="checkbox check-column" value="' . $row->category_id . '" /></td><td>' . $tableSeprator . $row->title . ' <div class="hidden" style="display:none" id="inline_' . $row->category_id . '"><div class="parent">' . $parentId . '</div></div></td><td>' . $this->view->htmlLink(array("route" => "admin_default", "module" => "sesmultipleform", "controller" => "categories", "action" => "edit-category", "id" => $row->category_id, "catparam" => "subsub",'form_id'=>$value['form_id']), $this->view->translate("Edit"), array()) . ' | ' . $this->view->htmlLink('javascript:void(0);', $this->view->translate("Delete"), array("class" => "deleteCat", "data-url" => $row->category_id)) . '</td></tr>';
            echo json_encode(array('seprator' => $seprator, 'tableData' => $tableData, 'id' => $row->category_id, 'name' => $row->title));die;
        }
        $this->view->getForms = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm(array('fetchAll'=>true,'active'=>true));
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_categories');
        //profile types
        $profiletype = array();
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesmultipleform_entry');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            $options = $profileTypeField->getElementParams('sesmultipleform');
            unset($options['options']['order']);
            unset($options['options']['multiOptions']['0']);
            $profiletype = $options['options']['multiOptions'];
        }
        $this->view->profiletypes = $profiletype;
        $this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
        //Get all categories
        $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getCategory(array('column_name' => '*', 'profile_type' => true,'id'=>$id));
    }

    public function customFieldsAction() {

    }


    public function exportFormSubmissionAsCsvAction(){
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form_id = $form_id = $this->_getParam('form_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        $this->view->form_submitted_paginator = $form_submitted_paginator = Engine_Api::_()->impactx()->getAllSubmittedEntries($form_id);

    }
    
    
    /*
     * Change the publish status for the entry
     */
    public function validateFormFieldsAction()
    {
        $post_val = @json_decode($_POST['post_val'], true);
        
       
        $new_entry_form = new Yndynamicform_Form_Standard(
        array(
            'item' => new Yndynamicform_Model_Entry(array()),
            'topLevelId' => $_POST['ajaxform_field_id'],
            'topLevelValue' => $_POST['ajaxform_option_id'],
            'mode' => 'create',
        ));
        
        $new_entry_form->isValid($post_val);
        $hasErrors = $new_entry_form->hasErrors();
        
        $scriptString = '';
        if( !empty($hasErrors) ) {
            $scriptString = 'ERROR:<script>';
            $getErrorMessageArray = $new_entry_form->getMessages();
            foreach( $getErrorMessageArray as $element_id => $messageArray ) {
                foreach( $messageArray as $errorMsg ) {
                    $scriptString .= 'document.getElementById("'.$element_id.'-label").innerHTML = document.getElementById("'.$element_id.'-label").innerHTML +\'<div style="color:#e82413;">' . $errorMsg . '</div>\';';
                }
                $scriptString .= 'document.getElementById("'.$element_id.'").style.borderColor = "#e82413";';
            }
            
            $scriptString .= '</script>';
        }
        die("dsa");
        echo $scriptString;
        exit();
        
        
    }

}

?>