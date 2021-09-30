<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
     <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
     <div class="layout_middle">
      <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
      <div class="sitepage_edit_content">
        <div class="sitepage_edit_header">
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
          <?php if($this->sitepage->draft == 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
          <h3><?php echo $this->translate('Dashboard: ') . $this->sitepage->title; ?></h3>
        </div>

        <div id="show_tab_content">
        <div class="sitepage_manage_announcements_services">
          <h3> <?php echo $this->translate('Manage Services'); ?> </h3>
          <p class="form-description"><?php echo $this->translate("Below, you can manage the services for your page.") ?></p>
          <br />
          <div class="sitepage_getstarted_btn">
            <?php echo $this->htmlLink(array('action' => 'create-service', 'page_id' => $this->page_id),
              $this->translate('Add New Service'),
              array('class' => 'smoothbox')) ?>
            </div>
            <?php if (count($this->services) > 0) : ?>
              <?php foreach ($this->services as $item): ?>
                <div id='<?php echo $item->service_id ?>_page_main'  class='sitepage_manage_announcements_list'>
                  <div id='<?php echo $item->service_id ?>_page'>
                    <?php
                    $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_icon.png';
                    $temp_url = $item->getPhotoUrl();
                    if (!empty($temp_url)):
                      $url = $item->getPhotoUrl('thumb.icon');
                    endif;
                    ?>
                    <img src="<?php echo $url;?>">
                  </div>
                  <div class="sitepage_manage_announcements_info_dashboard">
                    <div class="sitepage_manage_announcements_title">
                      <div class="sitepage_manage_announcements_option" >

                        <a href='<?php echo $this->url(array('action' => 'edit-service', 'service_id' => $item->service_id ),'service_specific',true) ?>' class="buttonlink seaocore_icon_edit smoothbox"><?php echo $this->translate("Edit ");?></a>
                        <?php //if ( $this->owner_id != $item->user_id ) :?>
                        <a href='<?php echo  $this->url(array('action' => 'delete-service', 'service_id' => $item->service_id));?>', '<?php echo $this->page_id ?>')"; class="buttonlink seaocore_icon_delete smoothbox" ><?php echo $this->translate('Remove');?></a>
                        <?php //endif;?>
                      </div>
                      <span><?php echo $item->title; ?></span>
                    </div>
                    <div class="sitepage_manage_announcements_dates seaocore_txt_light">
                      <b><?php echo $this->translate("Duration: ")?></b> <?php echo $this->translate($item->duration); ?>&nbsp;<?php echo $this->translate($item->duration_type); ?>
                    </div>
                  </div>
                  <div class="sitepage_manage_announcements_body show_content_body">
                      <b><?php echo $item->body;?>
                    </div>
                </div>
              <?php endforeach; ?>
              <?php else: ?>
                <br />
                <div class="tip">
                  <span><?php echo $this->translate('No services have been posted for this page yet.'); ?></span>
                </div>
              <?php endif; ?>
            </div>
            <?php echo $this->paginationControl($this->services, null, null, array(
              'pageAsQuery' => true,
              'query' => $this->services,
          //'params' => $this->formValues,
            )); ?>
          </div>
        </div>
      </div>
      <br />
      <div id="show_tab_content_child">
      </div>
      </div>
    </div>
  </div>
</div>
</div>
