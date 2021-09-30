<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchwidget.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_Searchwidget extends Engine_Form {

  protected $_searchForm;
  
  protected $_item;
  
  //Changes in onchange event function for mobile mode.
  protected $_hasMobileMode = false;

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }
  
  public function init() {
  
    // Add custom elements
    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => 'global_form_box',
		))
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('GET');

    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
    $this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setAction($view->url(array(), 'sitepagemember_browse', true))->getDecorator('HtmlTag')->setOption('class', '');
  }

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function getAdditionalOptionsElement() {
  
    $subform = new Zend_Form_SubForm(array(
			'name' => 'extra',
			'order' => 19999999,
			'decorators' => array(
				'FormElements',
			)
    ));
    Engine_Form::enableForm($subform);

    $search_column = array();
    $row = $this->_searchForm->getFieldsOptions('sitepage', 'category_id');
    $coreContent_table = Engine_Api::_()->getDbtable('content', 'core');
    $select_content = $coreContent_table->select()->where('name = ?', 'sitepagemember.search-sitepagemember');
    $params = $coreContent_table->fetchAll($select_content);
    foreach($params as $widget) {
      if(isset($widget['params']['search_column'])) {
				$search_column = $widget['params']['search_column'];
      }
		}

    $showTabArray = Zend_Controller_Front::getInstance()->getRequest()->getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4","4" => "5"));

    $enabledColumns = array_intersect($search_column, $showTabArray);
    if(empty($enabledColumns)) {
      $enabledColumns = $showTabArray;
    }

    $i = 5000;

    //$row = $this->_searchForm->getFieldsOptions('sitepage', 'search');

    if (in_array("4", $enabledColumns)) {
      $this->addElement('Text', 'search_member', array(
				'label' => 'Member Keywords',
				'order' => $row->order - 4,
        'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
      ));
    }

    if (in_array("3", $enabledColumns)) {
      $this->addElement('Text', 'title', array(
				'label' => 'Page Title',
				'order' => $row->order - 3,
				'autocomplete' => 'off',
        'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
      ));
     }

		if (in_array("2", $enabledColumns)) {
			$this->addElement('Select', 'orderby', array(
				'label' => 'Browse By',
				'multiOptions' => array(
					'' => '',
					'join_date' => 'Most Recent',
					'member_count' => "Top Page Joiners",
					'featured_member' => 'Featured Members',
				),
				'order' => $row->order - 1,
				'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepagevideos();',
			));
    }

    $row = $this->_searchForm->getFieldsOptions('sitepage', 'category_id');
      if (in_array("5", $enabledColumns)) {
	    // prepare categories
	    $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
	    if (count($categories) != 0) {
	      $categories_prepared[0] = "";
	      foreach ($categories as $category) {
	        $categories_prepared[$category->category_id] = $category->category_name;
	      }

				if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
					$onChangeEvent = "subcategoryies(this.value, '', '');";
					$categoryFiles = 'application/modules/Sitepage/views/scripts/_Subcategory.tpl';
				}
				else {
					$onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
					$categoryFiles = 'application/modules/Sitepage/views/sitemobile/scripts/_Subcategory.tpl';
				}

	      // category field
	      $this->addElement('Select', 'category_id', array(
					'label' => 'Page Category',
					'order' => $row->order,
					'multiOptions' => $categories_prepared,
					'onchange' => $onChangeEvent,
	      ));
	    }

  		$this->addElement('Select', 'subcategory_id', array(
				'RegisterInArrayValidator' => false,
				'order' => $row->order + 1,
				'decorators' => array(array('ViewScript', array(
					'viewScript' => $categoryFiles,
					'class' => 'form element'
				)))
 			 ));

  		$this->addElement('Select', 'subsubcategory_id', array(
				'RegisterInArrayValidator' => false,
				'order' => $row->order + 2,
				'decorators' => array(array('ViewScript', array(
					'viewScript' => $categoryFiles,
					'class' => 'form element')))
 			 ));
    } else {
		    $this->addElement('Hidden', 'category_id', array(
		             'order' => $i++,
		    ));
		
		    $this->addElement('Hidden', 'subcategory_id', array(
		             'order' => $i++,
		    ));

        $this->addElement('Hidden', 'subsubcategory_id', array(
		             'order' => $i++,
		    ));
    }

    $this->addElement('Hidden', 'page', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'category', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'subcategory', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'categoryname', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
             'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
             'order' => $i++,
    ));

    // init to
    $this->addElement('Hidden', 'resource_id', array( 'order' => $i++,));

    $subform->addElement('Button', 'done', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
    ));

    $this->addSubForm($subform, $subform->getName());

    return $this;
  }
}