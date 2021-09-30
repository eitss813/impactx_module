<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sectionHeader">
    <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->sitepage_id); ?>
    <div class="section_header_details">
        <h3><?php echo $this->translate($this->sectionTitle); ?></h3>
        <p><?php echo $this->translate($this->sectionDescription); ?></p>
    </div>
    <!--<div class="section_header_info">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()),$this->translate('VIEW_PAGE'),array('class' => 'button')) ?>
        <br/>
        <br/>
        <?php if(!empty($sitepage->declined) ): ?>
            <button class="page_declined_btn"><?php echo $this->translate("Declined")?></button>
        <?php endif; ?>

        <?php if( !empty($sitepage->pending) ): ?>
            <button class="page_approval_pending_btn"><?php echo $this->translate("Approval Pending")?></button>
        <?php endif; ?>

        <?php if( !empty($sitepage->approved) ): ?>
            <button class="page_approved_btn"><?php echo $this->translate("Approved")?></button>
        <?php endif; ?>

        <?php if( empty($sitepage->approved) ): ?>
            <button class="page_dis_approved_btn"><?php echo $this->translate("Dis-Approved")?></button>
        <?php endif; ?>

    </div>-->
    <!-- Existing status hided
  <div class="section_header_info" style="display: flex;flex-direction: column;align-items: center;">
      <div class="status_container">
          <h3 class="status_text_custom"><?php echo $sitepage->state; ?></h3>
      </div>
      <div class="btn_container_custom">
          <?php if($sitepage->state === 'draft' || $sitepage->state === 'rejected' ): ?>
          <?php echo $this->htmlLink(array('route'=> 'sitepage_extended', 'controller' => 'status', action => 'submit', 'sitepage_id' =>  $this->sitepage_id, format=> 'smoothbox' ), $this->translate('Submit for approval'), array("class" => 'common_btn_custom submit_for_approval_btn smoothbox')) ?>
          <?php endif; ?>
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()),$this->translate('VIEW_PAGE'),array('class' => 'common_btn_custom view_org')) ?>
      </div>
      <?php if($sitepage->state === 'rejected'): ?>
      <?php echo $this->htmlLink(array('route'=> 'sitepage_extended', 'controller' => 'status', action => 'view-notes', 'sitepage_id' => $this->sitepage_id, format=> 'smoothbox' ), $this->translate('View rejected reasons'), array("class" => 'rejected_notes_btn smoothbox', 'style' => 'background: none !important;color: #444 !important;') ) ?>
      <?php endif; ?>
  </div>
  -->
</div>

<style>
    .sectionHeader{
        display: flex;
        justify-content: space-between;
    }
    .section_header_details h3{
        font-size: 24px !important;
    }
    .section_header_details{
        display: flex;
        flex-direction: column;
    }
    .btn_container_custom{
        margin: 15px;
    }
    .status_container{
        background: gray;
        padding: 10px;
        border-radius: 5%;
        min-width: 150px;
        text-align: center;
    }
    .status_text_custom{
        font-size: 20px;
        color: white;
    }
    .common_btn_custom{
        margin: 5px;
        padding: 8px 8px;
        border-radius: 3px;
    }
    .rejected_notes_btn{
        text-align: center;
        text-decoration: underline !important
    }
    .submit_for_approval_btn{
        background: #37bb6f;
        border: 1px solid #2F954E;
        color: #fff !important;
    }
    .view_org{
        color: #ffffff !important;
        background: #44AEC1;
    }
    .sectionHeader{
        display: flex;
        justify-content: space-between;
    }
    .section_header_details h3{
        font-size: 24px !important;
    }
    .section_header_details{
        display: flex;
        flex-direction: column;
    }
</style>