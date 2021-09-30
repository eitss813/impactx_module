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

<div class="headline">
    <span style="font-size: 24px">
        Dashboard: <?php echo $this->htmlLink($this->sitepage->getHref(), $this->sitepage->getTitle()) ?>
         <div class="status_container">
            <h3 class="status_text_custom" style="margin-bottom: 0px !important;font-size: initial"><?php echo $this->sitepage->state; ?></h3>
        </div>
  <div class="section_header_info" style="display: flex;flex-direction: column;align-items: center;">
        <div class="btn_container_custom">
            <?php if($this->sitepage->state === 'draft' || $this->sitepage->state === 'rejected' ): ?>
            <?php echo $this->htmlLink(array('route'=> 'sitepage_extended', 'controller' => 'status', action => 'submit', 'sitepage_id' =>  $this->sitepage->page_id, format=> 'smoothbox' ), $this->translate('Submit for approval'), array("class" => 'common_btn_custom submit_for_approval_btn smoothbox')) ?>
            <?php endif; ?>
            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE'),array('class' => 'common_btn_custom view_org')) ?>
        </div>
      <?php if($this->sitepage->state === 'rejected'): ?>
      <?php echo $this->htmlLink(array('route'=> 'sitepage_extended', 'controller' => 'status', action => 'view-notes', 'sitepage_id' => $this->sitepage_id, format=> 'smoothbox' ), $this->translate('View rejected reasons'), array("class" => 'rejected_notes_btn smoothbox', 'style' => 'background: none !important;color: #444 !important;') ) ?>
      <?php endif; ?>
    </div>

    </span>
</div>
<style>
    .headline .status_container {
        background: gray;
        padding: 7px;
        border-radius: 5%;
        min-width: 120px;
        text-align: center;
        margin-left: 10px;
    }
    .headline {
        display: flex;
    }
    .headline span {
        display: flex;
        align-items: center;
    }
    .section_header_info{
        display: flex;
        flex-direction: column;
        align-items: center;
        float: right;
        position: absolute;
        right: 4%;
    }
    @media (max-width: 767px) {
        .status_container{
            margin-left: 0px !important;
        }
        .headline a{
            text-align: center !important;
            margin-bottom: 8px;
            margin-top:8px;
        }
        .headline span{
            flex-direction: column;
        }
        .section_header_info{
            position: unset !important;
        }
    }
</style>