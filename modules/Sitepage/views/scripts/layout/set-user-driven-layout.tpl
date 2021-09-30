<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: savelayout.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
  <h3><?php echo $this->translate('Reset Layout of your Page?'); ?></h3>
  <p>
    <?php echo $this->translate('Are you sure you want to reset the layout of this page? If you click "Continue", then you will be able to edit the layout of your page according to you.'); ?>
  </p>
  <br />
  <p>    
    <button onclick='continuelayout(); return false;'><?php echo $this->translate('Continue'); ?></button>
    or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
  </p>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>


<script type="text/javascript">

function continuelayout() {
  window.location.href= '<?php echo $this->url(array('action' => 'save-user-driven-layout', 'controller' => 'layout', 'module' => 'sitepage', 'page_id' => $this->page_id ), 'default', true)?>';
  Smoothbox.close();
}

</script>