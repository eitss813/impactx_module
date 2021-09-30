<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Form_Search extends Engine_Form {

  public function init() {

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'text', array(
        'label' => 'Search',
        'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
    ));

    $this->addElement('hidden', 'tag', array(
        'order' => 990,
    ));

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Most Rated',
            'comment_count' => 'Most Commented',
            'like_count' => 'Most Liked',
            'featured' => 'Featured',
        ),
        'onchange' => 'this.form.submit();',
    ));
  }

}