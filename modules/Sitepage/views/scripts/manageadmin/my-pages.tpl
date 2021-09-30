<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: myadminpages.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  function smoothboxpage(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
<script type="text/javascript">

  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
  <?php //echo $this->form->render($this) ?>

<div class='layout_middle'>

    <h3 class="sitepage_mypage_head"><?php echo $this->translate('Pages I Admin'); ?></h3>
  
  <?php
  $renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.renew.email', 2))));
  ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="seaocore_browse_list">
       <?php foreach ($this->paginator as $item): ?>
        <li>
          <div class='seaocore_browse_list_photo'>
						<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal')) ?> 
          </div>

          <div class='seaocore_browse_list_options'>
            <?php if ($this->can_edit): ?>
              <?php if (empty($item->declined)): ?>
                <a href='<?php echo $this->url(array('page_id' => $item->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard'); ?></a>

							<?php if ($item->draft == 0)
								echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $item->page_id), $this->translate('Publish Page'), array('class' => 'buttonlink icon_sitepage_publish', 'onclick' => 'smoothboxpage(this);return false')) ?>
                
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1)):?>  
                <?php if (!$item->closed): ?>
                  <a href='<?php echo $this->url(array('page_id' => $item->page_id, 'closed' => 1, 'check' => 1), 'sitepage_close', true) ?>' class='buttonlink icon_sitepages_close'><?php echo $this->translate('Close Page'); ?></a>
                <?php else: ?>
                  <a href='<?php echo $this->url(array('page_id' => $item->page_id, 'closed' => 0, 'check' => 1), 'sitepage_close', true) ?>' class='buttonlink icon_sitepages_open'><?php echo $this->translate('Open Page'); ?></a>
                <?php endif; ?>
              <?php endif; ?>
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

					<?php  $this->partial()->setObjectKey('sitepage');
					echo $this->partial('partial_views.tpl', $item); ?>

							<?php
							// Not mbstring compat
							echo substr(strip_tags($item->body), 0, 350);
							if (strlen($item->body) > 349)
								echo "...";
							?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="tip">
      <span> <?php echo $this->translate('You do not have any pages yet.'); ?>
  <?php if ($this->can_create): ?>
    <?php
    if (Engine_Api::_()->sitepage()->hasPackageEnable()):
      $createUrl = $this->url(array('action' => 'index'), 'sitepage_packages');
    else:
      $createUrl = $this->url(array('action' => 'create'), 'sitepage_general');
    endif;
    ?>
    <?php echo $this->translate('Get started by %1$screating%2$s a new page.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
  <?php endif; ?>
      </span>
    </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepage")); ?>
</div>

<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
  <input type="hidden" name="page_id_session" id="page_id_session" />
</form>

<script type="text/javascript">
  function submitSession(id){
    
    document.getElementById("page_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>