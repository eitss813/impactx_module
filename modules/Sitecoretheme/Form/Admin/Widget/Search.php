<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_Search extends Engine_Form {

    public function init() {

        if (Engine_Api::_()->hasModuleBootstrap('sitecitycontent') && Engine_Api::_()->hasModuleBootstrap('siteadvsearch')) {
            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'verticalSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search',
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'verticalSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 1,
                ));
            }

            $this->addElement('Radio', 'showLocationBasedContent', array(
                'label' => 'Show results based on the location, saved in user’s browser cookie.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));

            $this->addElement('Radio', 'showLocationSearch', array(
                'label' => 'Do you want to enable location based searching?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));
        } else {

            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'verticalSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search',
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'verticalSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 1,
                ));
            }
        }
    }

}