<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Block.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_Block extends Core_Form_Admin_Widget_Standard
{

  public function init()
  {
    parent::init();

    // Set form attributes
    $this
      ->setTitle(SITECORETHEME_PLUGIN_NAME.' - Informative Block')
      ->setDescription('Please choose an block.');
    $table = Engine_Api::_()->getDbtable('blocks', 'sitecoretheme');
    $blocks = $table->fetchAll($table->getBlocksSelect());

    $this->removeElement('title');

    if( count($blocks) > 0 ) {
      $this->addElement('Select', 'block_id', array(
        'label' => 'Select the Block',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          array('NotEmpty', true),
        )
      ));

      $this->block_id->addMultiOption(0, '');
      foreach( $blocks as $block ) {
        $this->block_id->addMultiOption($block->getIdentity(), $block->getTitle());
      }

      $this->addElement('Select', 'photosPositions', array(
        'label' => 'Select Image Position',
        'multiOptions' => array('left' => 'Left Side', 'right' => 'Right Side'),
        'value' => 'right'
      ));

      $this->addElement('Select', 'backendBorder', array(
        'label' => 'Show Border Frame',
        'multiOptions' => array('0' => 'No', '1' => 'Yes'),
        'value' => 'right'
      ));
    }
  }

}