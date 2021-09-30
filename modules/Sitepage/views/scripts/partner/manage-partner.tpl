<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageadmins.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                    'sitepage_id'=>$this->sitepage->page_id,
                    'sectionTitle'=> 'Sister Pages',
                    'sectionDescription' => '')); ?>

            <div class="sitepage_edit_content">
                <div id="show_tab_content">

                    <!-- Joined as a partners -->
                    <div class="joined-as-partners">
                        <div class="joined-as-partners-header">
                            <div class="fleft"><h4 class="container-title">Invited or Joined as a sister into organization(s)</h4></div>
                        </div>
                        <hr>
                        <br/>
                        <?php if(count($this->joinedAsPartner) > 0 ): ?>
                            <div class="sitepage_partners_container">
                                <div class="sitepage_partners">
                                    <?php foreach ($this->joinedAsPartner as $partner): ?>
                                        <div id='<?php echo $partner->partner_page_id ?>_page_main'  class='sitepage_partners_list'>
                                            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $partner->page_id); ?>
                                            <div class='sitepage_partners_thumb' id='<?php echo $sitepage->page_id ?>_pagethumb'>
                                                <a href="<?php echo $sitepage->getHref(); ?>">
                                                    <?php echo $this->itemBackgroundPhoto($sitepage, null, null, array('tag' => 'i')); ?>
                                                </a>
                                            </div>
                                            <div id='<?php echo $sitepage->page_id ?>_page' class="sitepage_partners_detail">
                                                <div class="sitepage_partners_cancel_web">

                                                    <!-- if nothing then any one can be done accept or rejected -->
                                                    <?php if(empty($partner->accepted) && empty($partner->rejected) ):?>
                                                        <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'ACCEPT' ), $this->translate('Accept'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                        <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'REJECT' ), $this->translate('Reject'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <!-- if accepted already then can rejected -->
                                                    <?php if( !empty($partner->accepted) && empty($partner->rejected ) ):?>
                                                        <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'REJECT'), $this->translate('Reject'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <!-- if rejected already then can accept -->
                                                    <?php if(!empty($partner->rejected ) && empty($partner->accepted)):?>
                                                        <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'ACCEPT' ), $this->translate('Accept'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                </div>
                                                <h2 class="site_page_title">
                                                    <?php echo $this->htmlLink($sitepage->getHref(), $sitepage->getTitle()) ?>
                                                </h2>
                                                <?php if(empty($partner->accepted) && empty($partner->rejected) ):?>
                                                    <h3>Pending Invitation</h3>
                                                <?php endif; ?>

                                                <?php if( !empty($partner->accepted) && empty($partner->rejected ) ):?>
                                                    <h3>You have accepted the invitation</h3>
                                                <?php endif; ?>

                                                <?php if(!empty($partner->rejected ) && empty($partner->accepted)):?>
                                                    <h3>You have rejected the invitation</h3>
                                                <?php endif; ?>
                                                <div class="sitepage_partners_cancel_mob" style="display: none">

                                                    <!-- if nothing then any one can be done accept or rejected -->
                                                    <?php if(empty($partner->accepted) && empty($partner->rejected) ):?>
                                                    <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'ACCEPT' ), $this->translate('Accept'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'REJECT' ), $this->translate('Reject'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <!-- if accepted already then can rejected -->
                                                    <?php if( !empty($partner->accepted) && empty($partner->rejected ) ):?>
                                                    <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'REJECT'), $this->translate('Reject'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <!-- if rejected already then can accept -->
                                                    <?php if(!empty($partner->rejected ) && empty($partner->accepted)):?>
                                                    <span class="sitepage_link_wrap mright5">
                                                            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'ACCEPT' ), $this->translate('Accept'), array(
                                                                'class' => ' smoothbox button'
                                                            ))?>
                                                        </span>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="tip">
                                <span>You did not joined or invited as a sister into any organization(s) </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <br/>
                    <br/>
                    <br/>

                    <!-- My Partners -->
                    <div class="my-partners">
                        <div class="my-partners-header">
                            <div class="fleft"><h4 class="container-title">Added organization as sister organization(s)</h4></div>
                            <div class="fright">
                                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'controller' => 'partner' ,'action' => 'add-partner', 'page_id' => $this->page_id), 'sitepage_extended', true)); ?>">
                                    <span><?php echo $this->translate("Add Sister Pages"); ?></span>
                                </a>
                            </div>
                        </div>
                        <hr>
                        <br/>
                        <?php if(count($this->myPartners) > 0 ): ?>
                            <div class="sitepage_partners_container">
                                <div class="sitepage_partners sitepage_partners_list">
                                    <?php foreach ($this->myPartners as $partner): ?>
                                        <div id='<?php echo $partner->partner_page_id ?>_page_main'  class=''>
                                            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $partner->partner_page_id); ?>

                                            <div style="display: flex" id='<?php echo $sitepage->page_id ?>_page' class="sitepage_partners_detail">
                                                <div class="sitepage_partners_cancel_web"
                                                    <span class="sitepage_link_wrap mright5">
                                                        <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'DELETE' ), $this->translate('Remove'), array(
                                                            'class' => ' smoothbox button'
                                                        ))?>
                                                    </span>
                                                </div>
                                            <div class='sitepage_partners_thumb' id='<?php echo $sitepage->page_id ?>_pagethumb'>
                                                <a href="<?php echo $sitepage->getHref(); ?>">
                                                    <?php echo $this->itemBackgroundPhoto($sitepage, null, null, array('tag' => 'i')); ?>
                                                </a>
                                            </div>
                                             <div style="position: relative;left: 5%;">
                                                 <h2 class="site_page_title">
                                                     <?php echo $this->htmlLink($sitepage->getHref(), $sitepage->getTitle()) ?>
                                                 </h2>
                                                 <?php if(empty($partner->accepted) && empty($partner->rejected) ):?>
                                                 <h3>Invitation Request Sent</h3>
                                                 <?php endif; ?>

                                                 <?php if( !empty($partner->accepted) && empty($partner->rejected ) ):?>
                                                 <h3>Accepted your invitation</h3>
                                                 <?php endif; ?>

                                                 <?php if(!empty($partner->rejected ) && empty($partner->accepted)):?>
                                                 <h3>Rejected your invitation</h3>
                                                 <?php endif; ?>
                                                 <div class="sitepage_partners_cancel_mob" style="display: none"
                                                 <span class="sitepage_link_wrap mright5">
                                                        <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'partner', 'action' => 'delete-partner', 'partner_id' => $partner->partner_id , 'action_type' => 'DELETE' ), $this->translate('Remove'), array(
                                                            'class' => ' smoothbox button'
                                                        ))?>
                                                    </span>
                                             </div>
                                             </div>

                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="tip">
                                <span>You have not added any organization as sister organization(s)</span>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>
<style type="text/css">
    .global_form > div > div {background:none;border:none;padding:0px;}
    .sitepage_partners_list{
        margin-bottom: 10px;
        position: relative;
    }
    .sitepage_partners_list > div {
        display: inline-block;
        vertical-align: middle;
    }
    .sitepage_partners_thumb img, .sitepage_partners_thumb i {
        border-radius: 50%;
        width: 100px;
        height: 100px;
        background-repeat: no-repeat;
        background-position: top center;
        display: inline-block;
        background-size: cover;
    }
    .sitepage_partners_detail {
        padding: 7px 15px;
        font-size: 18px !important;
    }
    .sitepage_partners_cancel ,
    .sitepage_partners_cancel_web,
    .sitepage_partners_cancel_mob{
        position: absolute;
        right: 0;
    }
    .sitepage_partners_detail .site_page_title a,
    .sitepage_partners_detail .site_page_title a:hover,
    .sitepage_partners_cancel a:hover , .sitepage_partners_cancel_web a:hover ,  .sitepage_partners_cancel_mob a:hover ,
    .sitepage_partners_detail .sitepage_link_wrap a:hover{
        color: black;
    }
    .my-partners hr,
    .joined-as-partners hr{
        border-bottom: 1px solid #f2f0f0;
        width: 100%;
    }
    .container-title{
        font-size: 16px;
        font-weight: bold;
    }
    .sitepage_partners_container{
        border: 1px solid #eeeeee;
        padding: 10px;
    }
    @media (max-width: 767px)
    {
        .sitepage_partners_cancel_web{
          display: none !important;
        }
        .sitepage_partners_cancel_mob {
            display: block !important;
            margin-top: 18px;
        }
        .sitepage_partners_detail{
            height: 160px;
        }
    }
</style>