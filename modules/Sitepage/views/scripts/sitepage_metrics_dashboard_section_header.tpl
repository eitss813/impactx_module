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
    <div class="section_header_details">
        <h3><?php echo $this->translate($this->sectionTitle); ?></h3>
        <p><?php echo $this->translate($this->sectionDescription); ?></p>
    </div>
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