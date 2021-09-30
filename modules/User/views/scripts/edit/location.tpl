<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: profile.tpl 9984 2013-03-20 00:00:04Z john $
 * @author     John
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)):?>
      <?php echo $this->translate('Edit My Profile');?>
    <?php endif;?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<style>
    .sitecrowdfunding_dashboard_innerbox{
        background-color: white;
    }
</style>
<div class="sitecrowdfunding_dashboard_content">
    <div class="sitecrowdfunding_dashboard_innerbox">
            <?php if (!empty($this->location)): ?>
                <div class="sitecrowdfunding_edit_location_form b_medium sitecrowdfunding_review_block">
                    <div class="global_form_box">
                        <div class="sitecrowdfunding_project_location">
                            <div class="formlocation_edit_label"><?php echo $this->translate('Location: '); ?></div>
                            <div class="formlocation_add fleft"><?php echo $this->location['location'] ?></div>
                            <?php  echo $this->htmlLink(
                                array(
                                'controller' => 'edit',
                                'route' => "user_extended",
                                'action' => 'edit-location',
                                ),
                                $this->translate("Edit Location"),
                                array('class' => 'smoothbox icon seaocore_icon_edit_sqaure fright')
                            ); ?>
                        </div>
                    </div>
                </div>
            <div class="sitecrowdfunding_edit_location_form b_medium sitecrowdfunding_review_block">
                <?php echo $this->form->render($this); ?>
            </div>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'edit','action' => 'edit-location',), "user_extended", true); ?>
                    <?php echo $this->translate('You have not added a location. %1$sClick here%2$s to add a location.', "<a onclick='javascript:Smoothbox.open(this.href);return false;' href='$url'>", "</a>"); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>


