<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Graph.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Settings_Graph extends Engine_Form {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Graph Settings for Insights')
            ->setDescription("Page Admins can view graphical insights of various performance metrics like Views, Likes, Comments and Active Users of their pages over different time periods. Below, you can customize the theme and other parameters of the graph.");

    // COLOR VALUE FOR GRAPH BACKGROUND
    $this->addElement('Text', 'sitepage_graph_bgcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowGraphbg.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH VIEWS LINES WIDTH
    $this->addElement('Text', 'sitepage_graphview_width', array(
        'label' => 'Views Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Views in the graph. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphview.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR VIEWS LINE OF GRAPH
    $this->addElement('Text', 'sitepage_graphview_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowViews.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH LIKES LINES WIDTH
    $this->addElement('Text', 'sitepage_graphlike_width', array(
        'label' => 'Likes Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Likes in the graph. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphlike.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR LIKES LINE OF GRAPH
    $this->addElement('Text', 'sitepage_graphlike_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowLikes.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH ACTIVE USERS LINES WIDTH
    $this->addElement('Text', 'sitepage_graphuser_width', array(
        'label' => 'Active Users Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Active Users in the graph. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphuser.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR ACTIVE USERS LINE OF GRAPH
    $this->addElement('Text', 'sitepage_graphuser_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowActiveusers.tpl',
                    'class' => 'form element'
            )))
    ));

    // check if comments should be displayed or not
    $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();
    if (!empty($show_comments)) {

      // Element: GRAPH COMMENTS LINE  WIDTH
      $this->addElement('Text', 'sitepage_graphcomment_width', array(
          'label' => 'Comments Line Width',
          'description' => 'Enter the width of the lines in pixels which are used to represent Comments in the graph. (Enter a number between 1 and 9.)',
          'maxlength' => 1,
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphcomment.width', 3),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '1')),
          )
      ));

      // COLOR VALUE FOR COMMENTS LINE OF GRAPH
      $this->addElement('Text', 'sitepage_graphcomment_color', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_formImagerainbowComments.tpl',
                      'class' => 'form element'
              )))
      ));
    }

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>