<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: deleteselected.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings">
  <div class='global_form'>
    <?php if ($this->ids): ?>
      <form method="post" class="global_form_popup">
        <div>
          <h3><?php echo $this->translate("Delete the selected page albums?") ?></h3>
          <p>
            <?php echo $this->translate("Are you sure that you want to delete the %d page albums? It will not be recoverable after being deleted.", $this->count) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value='true'/>
            <input type="hidden" name="ids" value="<?php echo $this->ids ?>"/>		
            <button type='submit'><?php echo $this->translate("Delete") ?></button>
            <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
            <a href='<?php echo $this->url(array('action' => 'index', 'id' => null)) ?>'>
              <?php echo $this->translate("cancel") ?></a>
          </p>
        </div>
      </form>
    <?php else: ?>
      <?php echo $this->translate("Please select a page album to delete.") ?> <br/><br/>
      <a href="<?php echo $this->url(array('action' => 'index')) ?>" class="buttonlink icon_back">
        <?php echo $this->translate("Go Back") ?>
      </a>
    <?php endif; ?>
  </div>
</div>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>