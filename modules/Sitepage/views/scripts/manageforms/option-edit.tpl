<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: option-edit.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <div class="global_form_popup_message">
    <?php echo $this->translate("Your changes have been saved.") ?>
  </div>

  <script type="text/javascript">
    parent.onOptionEdit(
      <?php echo Zend_Json::encode($this->option) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>

<?php endif; ?>
<style>
  div#buttons-wrapper , div#label-wrapper{
    margin-left: 4px !important;
  }
  div#label-element {
    width: 98% !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    text-align: center !important;
    margin-bottom: 5px !important;

  }
</style>