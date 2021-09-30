<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>		  
  </h2>
</div>
<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
        <h3><?php echo $this->translate('Delete Page?'); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the Page with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->sitepage->title, $this->timestamp($this->sitepage->modified_date)); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete'); ?></button>
          <?php echo $this->translate('or'); ?> <a href='<?php echo $this->url(array('action' => 'manage'), 'sitepage_general', true) ?>'><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>