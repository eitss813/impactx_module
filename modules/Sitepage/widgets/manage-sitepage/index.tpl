<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='layout_middle'>
  <?php
    $renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.renew.email', 2))));?>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="seaocore_browse_list">
       
      <?php foreach ($this->paginator as $item): ?>
        <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?><?php if($item->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
           <?php if($item->featured):?>
             <span title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></span>
          <?php endif;?>
        <?php endif;?>
          <div class='seaocore_browse_list_photo'>
            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align'=>'left'))) ?> 
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
          <?php if (!empty($item->sponsored)): ?>
            <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
            if (!empty($sponsored)) { ?>
              <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>                 
              </div>
            <?php } ?>
          <?php endif; ?>
        <?php endif; ?>  
          </div>
        
          <div class='seaocore_browse_list_options'>
            <?php $this->can_edit = Engine_Api::_()->sitepage()->isManageAdmin($item, 'edit');  ?>
            <?php $this->can_delete = Engine_Api::_()->sitepage()->isManageAdmin($item, 'delete');  ?>
            <?php if ($this->can_edit): ?>
                <?php if(empty ($item->declined)): ?>
                <a href='<?php echo $this->url(array('page_id' => $item->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard'); ?></a>

                <?php if($item->draft == 0) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $item->page_id), $this->translate('Publish Page'), array('class'=>'buttonlink smoothbox icon_sitepage_publish')) ?>

                  <?php endif; ?>

              <?php endif; ?>
              <?php if ($this->can_delete): ?>
                <a href='<?php echo $this->url(array('page_id' => $item->page_id), 'sitepage_delete', true) ?>' class='buttonlink icon_sitepages_delete'><?php echo $this->translate('Delete Page'); ?></a>
              <?php endif; ?>
              <?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($item->page_id)): ?>
              <div class="tip">
                <span>
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->page_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                </span>
              </div>
            <?php endif; ?>

            <?php if (Engine_Api::_()->sitepage()->canShowRenewLink($item->page_id)): ?>
              <div class="tip">
                <span>
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->page_id ?>)"><?php echo $this->translate('Renew Page'); ?></a>
                </span>
              </div>
            <?php endif; ?>
          </div>

            <?php  //$this->partial()->setObjectKey('sitepage');
               echo $this->partial('partial_views.tpl','sitepage', array('sitepage'=> $item, 'showOwnerInfo' => $this->showOwnerInfo)); ?>
            <?php echo $this->viewMore($item->body,200,5000) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php elseif ($this->search): ?>
    <div class="tip"> <span> <?php echo $this->translate('You do not have any page which matches your search criteria.');?> </span> </div>
  <?php else: ?>
    <div class="tip">
      <span> <?php echo $this->translate('You do not have any pages yet.'); ?>
        <?php if ($this->can_create): ?>
          <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
          $createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
          else:
          $createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
          endif; ?>
          <?php echo $this->translate('Get started by %1$screating%2$s a new page.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepage")); ?>