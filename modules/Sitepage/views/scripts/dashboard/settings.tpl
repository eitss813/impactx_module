<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editstyle.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css') ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Edit Settings', 'sectionDescription' => 'Configure settings for page')); ?>
            <div class="sitepage_edit_content">
                <div id="show_tab_content">
                    <?php  echo $this->form->render(); ?>
                </div>
            </div>

            <!-- Photos -->
            <div id="show_tab_content">
                <div class="global_form">
                    <div>
                        <?php echo $this->content()->renderWidget("sitepage.photos-sitepage", array()); ?>
                    </div>
                </div>
            </div>

            <!-- Video -->
            <div id="show_tab_content">
                <div class="global_form">
                    <div>
                        <?php include_once APPLICATION_PATH .'/application/modules/Sitepage/views/scripts/_pageEditVideos.tpl' ; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>