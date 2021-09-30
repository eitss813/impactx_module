<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_SearchSitememberController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $browseWidget = Engine_Api::_()->seaocore()->getWidgetContentInfo($this->view->identity, array("name"=>'sitemember.browse-members-sitemember'));
        $this->view->content_id = $browseWidget["content_id"];
        $this->view->browse_params = $browseWidget["params"];
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        //FORM CREATION
        $this->view->viewType = $this->_getParam('viewType', 'horizontal');
        $this->view->whatWhereWithinmile = $this->_getParam('whatWhereWithinmile', 0);
        $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
        $this->view->locationDetection = $this->_getParam('locationDetection', 0);

        $widgetSettings = array(
            'viewType' => $this->view->viewType,
            'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
            'advancedSearch' => $this->view->advancedSearch,
            'locationDetection' => $this->view->locationDetection,
        );

        $this->view->form = $form = new Sitemember_Form_Search(array('widgetSettings' => $widgetSettings));

        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $order = Engine_Api::_()->sitemember()->showSelectedBrowseBy($this->view->identity);
            $form->orderby->setValue("$order");
        }

        if (!empty($orderBy)) {
            $params['orderby'] = $orderBy;
        }

        if (!empty($params))
            $form->populate($params);

        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $form->removeElement('show');
        }

        $this->setAutosuggest();
    }

    public function setAutosuggest() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profile_field_id = $topStructure[0]->getChild()->field_id;
        }
        $field_option_child = '';
        $field_option_child_city = '';
        $profile_type_id = 1;
        if (!$profile_type_id) {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            if (count($options) == 1) {
                $profile_type_id = $options[0]->option_id;
            }
        }

        if ($profile_field_id && $profile_type_id) {
            $mapstable = Engine_Api::_()->fields()->getTable('user', 'maps');
            $metatable = Engine_Api::_()->fields()->getTable('user', 'meta');
            $mapstableName = $mapstable->info('name');
            $metatableName = $metatable->info('name');
            $select = $mapstable->select()
                    ->setIntegrityCheck(false)
                    ->from($mapstableName, array('*'))
                    ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                    ->where($metatableName . '.type = ?', 'location')
                    ->where($mapstableName . '.field_id = ?', $profile_field_id)
                    ->where($mapstableName . '.option_id = ?', $profile_type_id);
            $row = $mapstable->fetchRow($select);
            if ($row) {
                $field_option_child = $row->field_id . '_' . $row->option_id . '_' . $row->child_id . '_alias_location';
            }

            $select = $mapstable->select()
                    ->setIntegrityCheck(false)
                    ->from($mapstableName, array('*'))
                    ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                    ->where($metatableName . '.type = ?', 'city')
                    ->where($mapstableName . '.field_id = ?', $profile_field_id)
                    ->where($mapstableName . '.option_id = ?', $profile_type_id);
            $row = $mapstable->fetchRow($select);
            if ($row) {
                $field_option_child_city = $row->field_id . '_' . $row->option_id . '_' . $row->child_id . '_alias_city';
            }

            if ($field_option_child || $field_option_child_city):

                //GET API KEY
                $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                $view->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
                $view->headScript()->appendFile($view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/core.js");
                $city = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities');
                if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $script = <<<EOF

                            var field_option_child = "$field_option_child";
                    
                            var field_option_child_city = "$field_option_child_city";
                            var city = "$city";
                            window.addEvent('domready', function() {
            if(field_option_child_city) {                  
            locationAutoSuggest(city, field_option_child, field_option_child_city);

           } else {
            window.addEvent('domready', function() {
                                                                                                                                              new google.maps.places.Autocomplete(document.getElementById(field_option_child));
                                                                                                                                      });
            }
                            });
EOF;

                    $view->headScript()
                            ->appendScript($script);
                } else {
                    $script = <<<EOF
        var field_option_child = "$field_option_child";
        var field_option_child_city = "$field_option_child_city";
        var city = "$city";
         sm4.core.runonce.add(function() {
            if(field_option_child_city) {                  
            locationAutoSuggest(city, field_option_child, field_option_child_city);
           } else {
           sm4.core.runonce.add(function() {
             new google.maps.places.Autocomplete(document.getElementById(field_option_child));
});
            }
        });
EOF;
                    $view->headScriptSM()
                            ->appendScript($script);
                }
            endif;
        }
    }

}
