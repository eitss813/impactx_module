<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Form_PageSearch extends Engine_Form
{

    public function init()
    {

        $this->setAttrib('id', 'searchForm')->setMethod('post');

        // page no
        $this->addElement('hidden', 'page_no', array('value' => '1','order' => 1));

        // tab enable
        $this->addElement('hidden', 'tab_link', array('value' => 'all_tab','order' => 2));

        // set search from page
        $this->addElement('hidden', 'searched_from_page', array('order' => 3));
        $this->addElement('hidden', 'searched_from_page_id', array('order' => 4));
        $this->addElement('hidden', 'searched_from_initiative_id', array('order' => 5));
        $this->addElement('hidden', 'searched_from_project_id', array('order' => 6));
        $this->addElement('hidden', 'selected_goal_id', array('order' => 7));
        $this->addElement('hidden', 'selected_category_id', array('order' => 8));

        // searched string
        $this->addElement('Text', 'query', array(
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // type dropdown
        $this->addElement('Select', 'type', array(
            'multiOptions' => array(
                '' => 'Everything in ImpactX',
            ),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'button',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}