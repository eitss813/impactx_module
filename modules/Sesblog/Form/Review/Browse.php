<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Browse.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Review_Browse extends Engine_Form {

  protected $_reviewTitle;
  protected $_reviewSearch;
  protected $_reviewStars;
  protected $_reviewRecommended;
  protected $_allsetting;
  protected $_ratinglist;
  protected $_reviewlist;

  public function setReviewTitle($title) {
    $this->_reviewTitle = $title;
    return $this;
  }

  public function getReviewTitle() {
    return $this->_reviewTitle;
  }
  public function setAllsetting($title)
  {

    $this->_ratinglist=isset($title['ratingreviews']) ? $title['ratingreviews'] : isset($title['allsetting']['ratingreviews']) ? $title['allsetting']['ratingreviews'] : array() ;
    $this->_reviewlist=isset($title['recommended_reviews']) ? $title['recommended_reviews'] : isset($title['allsetting']['recommended_reviews']) ? $title['allsetting']['recommended_reviews'] : array() ;
  }
  public function getReveiwlist()
  {
    return $this->_reviewlist;
  }
  public function getRatinglist()
  {
    return $this->_ratinglist;
  }

  public function setReviewSearch($title) {
    $this->_reviewSearch = $title;
    return $this;
  }

  public function getReviewSearch() {
    return $this->_reviewSearch;
  }

  public function setReviewStars($title) {
    $this->_reviewStars = $title;
    return $this;
  }

  public function getReviewStars() {
    return $this->_reviewStars;
  }

  public function setReviewRecommended($title) {
    $this->_reviewRecommended = $title;
    return $this;
  }

  public function getReviewRecommended() {
    return $this->_reviewRecommended;
  }

  public function init() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $identity = $view->identity;

    $blog_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('blog_id', 0);
    $this->setAttribs(array('id' => 'filter_form_review', 'class' => 'global_form_box'))->setMethod('GET');
    if($blog_id)
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('blog_id' => $blog_id), 'sesblog_entry_view', true));
    else
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'sesblog_review', true));

    if ($this->getReviewTitle()) {
      $this->addElement('Text', 'search_text', array(
          'label' => 'Search Reviews:',
          'order' => -999999,
      ));
    }

    if ($this->getReviewSearch()) {
      $this->addElement('Select', 'order', array(
          'label' => 'Browse By:',
          'multiOptions' => array(),
          'order' => -999998,
      ));
    }

    if ($this->getReviewStars() && !empty($this->getRatinglist())) {
      $rarr=array();
      $rarr['']='';
      if(in_array('1star',$this->getRatinglist())){ $rarr['1']="1 Stars"; }
      if(in_array('2star',$this->getRatinglist())){ $rarr['2']="2 Stars"; }
      if(in_array('3star',$this->getRatinglist())){ $rarr['3']="3 Stars"; }
      if(in_array('4star',$this->getRatinglist())){ $rarr['4']="4 Stars"; }
      if(in_array('5star',$this->getRatinglist())){ $rarr['5']="5 Stars"; }
      //Add Element: rating stars
      $this->addElement('Select', 'review_stars', array(
          'label' => "Review Stars:",
          'registerInArrayValidator' => false,
          'required' => false,
          'multiOptions' => $rarr,
      ));
    }
    if(!empty($this->getReveiwlist()))
    {
      $arr=array();
      if(in_array('allreviews',$this->getReveiwlist())){ $arr['']="All Reviews"; }
      if(in_array('recommendedonly',$this->getReveiwlist())){ $arr[1]="Recommended Only"; }
      if ($this->getReviewRecommended()) {
        //Add Element: rating stars
        $this->addElement('Select', 'review_recommended', array(
          'label' => "Recommended Reviews Only:",
          'registerInArrayValidator' => false,
          'required' => false,
          'multiOptions' => $arr,
        ));
      }
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit',
        'order' => '9999',
    ));
    $this->addElement('Dummy', 'loading-img-sesblog-review', array(
        'content' => '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />',
        'order' => '10001',
    ));
    parent::init();
  }

}
