<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: app.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >

    function owner(thisobj) {
        var Obj_Url = thisobj.href;

        Smoothbox.open(Obj_Url);
    }
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
    <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
    <div class="layout_middle">
        <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
        <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Photos/Videos', 'sectionDescription' => '')); ?>
        <div class="sitepage_edit_content">
            <div id="show_tab_content">
            <?php endif; ?>  
            <?php $canShowMessage = true; ?>
            <ul class="sitepage_getstarted">
                <?php $i = 1; ?>		   	
                <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')): ?>
                    <?php if (!empty($this->allowed_upload_photo)): ?>
                        <li> <?php $canShowMessage = false; ?>
                            <div class="sitepage_getstarted_num">																
                                <a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitepage_photoalbumupload', true) ?>'><i class="icon_app_photo"></i></a>						
                            </div>
                            <div class="sitepage_getstarted_des">
                                <b><?php echo $this->translate('Photos'); ?></b>
                                <p><?php echo $this->translate('Create albums and add photos for this page.'); ?></p>
                                <div class="sitepage_getstarted_btn">
                                    <a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitepage_photoalbumupload', true) ?>'><?php echo $this->translate('Add Photos'); ?></a>
                                    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->albumtab_id)), $this->translate('Manage Albums')) ?>
                                </div>
                            </div>	
                        </li>
                    <?php endif; ?> 
                <?php endif; ?>

                <?php if ($this->can_invite): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepageinvite_invite', 'user_id' => $this->viewer_id, 'sitepage_id' => $this->page_id), '<i class="icon_app_invite"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Invite &amp; Promote'); ?></b>
                            <p><?php echo $this->translate('Tell your friends, fans and customers about this page and make it popular.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('user_id' => $this->viewer_id, 'sitepage_id' => $this->page_id), 'sitepageinvite_invite', true) ?>'><?php echo $this->translate('Invite Friends &amp; Fans'); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ($this->can_create_poll): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepagepoll_create', 'page_id' => $this->page_id, 'tab' => $this->polltab_id), '<i class="icon_app_poll"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Polls'); ?></b>
                            <p><?php echo $this->translate('Get feedback from visitors to your page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->polltab_id), 'sitepagepoll_create', true) ?>'><?php echo $this->translate('Create a Poll'); ?></a> 
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->polltab_id)), $this->translate('Manage Polls')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->can_create_doc): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepagedocument_create', 'page_id' => $this->page_id, 'tab' => $this->documenttab_id), '<i class="icon_app_document"></i>') ?>
                        </div>	
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Documents'); ?></b>
                            <p><?php echo $this->translate('Add and showcase documents on your page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->documenttab_id), 'sitepagedocument_create', true) ?>'><?php echo $this->translate('Add a Document'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->documenttab_id)), $this->translate('Manage Documents')) ?>
                            </div>
                        </div>
                    </li>
                    <?php else:?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'document_create', 'parent_id' => $this->page_id, 'parent_type' => 'sitepage_page', 'tab' => $this->documenttab_id), '<i class="icon_app_document"></i>') ?>
                        </div>	
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Documents'); ?></b>
                            <p><?php echo $this->translate('Add and showcase documents on your page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('parent_id' => $this->page_id, 'parent_type' => 'sitepage_page','tab' => $this->documenttab_id), 'document_create', true) ?>'><?php echo $this->translate('Add a Document'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->documenttab_id)), $this->translate('Manage Documents')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>

                <?php if ($this->moduleEnable && !empty($this->can_offer)): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepageoffer_general', 'page_id' => $this->page_id, 'tab' => $this->offertab_id), '<i class="icon_app_offer"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Offers'); ?></b>
                            <p><?php echo $this->translate('Create and display attractive offers on your page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('action' => 'create', 'page_id' => $this->page_id, 'tab' => $this->offertab_id), 'sitepageoffer_general', true) ?>'><?php echo $this->translate('Add an Offer'); ?></a>
                                <a href='<?php echo $this->url(array('action' => 'index', 'page_id' => $this->page_id, 'tab' => $this->offertab_id), 'sitepageoffer_general', true) ?>'><?php echo $this->translate('Manage Offers'); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ($this->option_id && !empty($this->can_form)): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepageform_general', 'option_id' => $this->option_id, 'page_id' => $this->page_id, 'tab' => $this->formtab_id), '<i class="icon_app_question"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Form'); ?></b>
                            <p><?php echo $this->translate('Gather useful information from visitors by creating your form with relevant questions.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('action' => 'index', 'option_id' => $this->option_id, 'page_id' => $this->page_id, 'tab' => $this->formtab_id), 'sitepageform_general', true) ?>'><?php echo $this->translate('Manage Form'); ?></a>
                                <?php $can_edit_tabname = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageform.edit.name', 1); ?>
                                <?php if (!empty($can_edit_tabname)): ?>
                                    <?php echo $this->htmlLink(array('route' => 'default', 'page_id' => $this->page_id, 'module' => 'sitepageform', 'controller' => 'siteform', 'action' => 'edit-tab'), $this->translate("Edit Form Tab’s Name"), array('onclick' => 'owner(this);return false')) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->can_create_video): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepagevideo_create', 'page_id' => $this->page_id, 'tab' => $this->videotab_id), '<i class="icon_app_video"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->videotab_id), 'sitepagevideo_create', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php else:?>
                     <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            
                            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id,), '<i  class="icon_app_video"></i>', array('class' => 'seao_smoothbox', 'data-SmoothboxSEAOClass' => 'seao_add_video_lightbox')); ?> 
                            <?php else:?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id,), '<i class="icon_app_video"></i>'); ?> 
                            <?php endif;?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_video_lightbox" href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>

                <?php if ($this->can_create_event): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')): ?>
                        <li> 
                            <?php $canShowMessage = false; ?>
                            <div class="sitepage_getstarted_num">
                                <?php echo $this->htmlLink(array('route' => 'sitepageevent_create', 'page_id' => $this->page_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                            </div>
                            <div class="sitepage_getstarted_des">
                                <b><?php echo $this->translate('Events'); ?></b>
                                <p><?php echo $this->translate('Organize events for this page.'); ?></p>
                                <div class="sitepage_getstarted_btn">
                                    <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab_id' => $this->eventtab_id), 'sitepageevent_create', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                                    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->eventtab_id)), $this->translate('Manage Events')) ?>
                                </div>
                            </div>
                        </li>		
                    <?php else: ?>
                        <li> 
                            <?php $canShowMessage = false; ?>
                            <div class="sitepage_getstarted_num">
                                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                    <?php echo $this->htmlLink(array('route' => 'siteevent_package', 'action' => 'index', 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                                <?php else: ?>
                                    <?php echo $this->htmlLink(array('route' => 'siteevent_general', 'action' => 'create', 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                                <?php endif; ?>
                            </div>
                            <div class="sitepage_getstarted_des">
                                <b><?php echo $this->translate('Events'); ?></b>
                                <p><?php echo $this->translate('Organize events for this page.'); ?></p>
                                <div class="sitepage_getstarted_btn">
                                    <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                        <a href='<?php echo $this->url(array('parent_type' => 'sitepage_page', 'action' => 'index', 'parent_id' => $this->page_id, 'tab_id' => $this->eventtab_id), 'siteevent_package', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                                    <?php else: ?>
                                        <a href='<?php echo $this->url(array('parent_type' => 'sitepage_page', 'action' => 'create', 'parent_id' => $this->page_id, 'tab_id' => $this->eventtab_id), 'siteevent_general', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                                    <?php endif; ?>

                                    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->eventtab_id)), $this->translate('Manage Events')) ?>
                                </div>
                            </div>
                        </li>		
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($this->can_create_notes): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepagenote_create', 'page_id' => $this->page_id, 'tab' => $this->notetab_id), '<i class="icon_app_note"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Notes'); ?></b>
                            <p><?php echo $this->translate('Share updates and lots more by publishing notes in this blog-like section of your page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->notetab_id), 'sitepagenote_create', true) ?>'><?php echo $this->translate('Write a Note'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->notetab_id)), $this->translate('Manage Notes')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->can_create_discussion): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php
                            echo $this->htmlLink(array(
                                'route' => 'sitepage_extended',
                                'controller' => 'topic',
                                'action' => 'create',
                                'subject' => $this->subject()->getGuid(),
                                'tab' => $this->discussiontab_id,
                                'page_id' => $this->page_id
                                    ), '<i class="icon_app_topic"></i>')
                            ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Discussions'); ?></b>
                            <p><?php echo $this->translate('Enable interactions and information sharing on your page using threaded discussions.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'sitepage_extended',
                                    'controller' => 'topic',
                                    'action' => 'create',
                                    'subject' => $this->subject()->getGuid(),
                                    'tab' => $this->discussiontab_id,
                                    'page_id' => $this->page_id
                                        ), $this->translate('Post a Topic'))
                                ?>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->discussiontab_id)), $this->translate('Manage Discussions')) ?>
                            </div>
                        </div>	
                    </li>  
                <?php endif; ?>
                <?php if ($this->can_create_musics): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitepagemusic_create', 'page_id' => $this->page_id, 'tab' => $this->musictab_id), '<i class="icon_app_music"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Music'); ?></b>
                            <p><?php echo $this->translate('Add and share music for this page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->notetab_id), 'sitepagemusic_create', true) ?>'><?php echo $this->translate('Upload Music'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->musictab_id)), $this->translate('Manage Music')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>
                <?php if ($this->can_create_project): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitepage_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_project_general', 'page_id' => $this->page_id, 'tab' => $this->projecttab_id, 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id), '<i class="icon_app_music"></i>') ?>
                        </div>
                        <div class="sitepage_getstarted_des">
                            <b><?php echo $this->translate('Projects'); ?></b>
                            <p><?php echo $this->translate('Add project for this page.'); ?></p>
                            <div class="sitepage_getstarted_btn">
                                <a href='<?php echo $this->url(array('action'=>'create','page_id' => $this->page_id, 'tab' => $this->projecttab_id, 'parent_type' => 'sitepage_page', 'parent_id' => $this->page_id), 'sitecrowdfunding_project_general', true) ?>'><?php echo $this->translate('Add Project'); ?></a>
                                <?php echo $this->htmlLink($this->sitepage->getHref(array('tab' => $this->projecttab_id)), $this->translate('Manage Project')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>
                  
                <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN.//  ?>
                <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pageintergration_app.tpl'; ?>
                <?php //END FOR INRAGRATION WORK WITH OTHER PLUGIN// ?>

            </ul>

            <?php if ($canShowMessage): ?>
                <ul class="sitepage_getstarted">
                    <li> 
                        <div class="tip">
                            <span>
                                <?php if (Engine_Api::_()->sitepage()->hasPackageEnable()): ?>
                                    <?php
                                    $a = "<a  href='" . $this->url(array('action' => 'update-package', 'page_id' => $this->page_id), 'sitepage_packages', true) . "'>" . $this->translate('here') . "</a>";
                                    echo $this->translate("Your current package does not provide any apps for your page. Please click %s to upgrade your page package.", $a)
                                    ?>
                                <?php else: ?>
                                    <?php echo $this->translate("Please upgrade member level.") ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </li>
                </ul>
            <?php endif; ?>
            <?php if (empty($this->is_ajax)) : ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php endif; ?>