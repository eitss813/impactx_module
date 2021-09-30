<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: heading-edit.tpl 9747 2012-07-26 02:08:08Z john $
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
    parent.onHeadingEdit(
      <?php echo Zend_Json::encode($this->field) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>

<?php endif; ?>
<script>
  showLimit1();
  function showLimit() {

    if( document.getElementById('label-limit'))
      document.getElementById('label-limit').remove();

    let lab = document.getElementById('label');
    console.log('-----------------------------',lab.value);
    var tag = document.createElement("p");
    var text = document.createTextNode(" ("+lab.value.length+" by 250)");
    tag.setAttribute('id','label-limit');
    tag.appendChild(text);
    var element = document.getElementById("label-label");
    element.appendChild(tag);
  }
  function showLimit1() {

    if( document.getElementById('label-limit'))
      document.getElementById('label-limit').remove();


    let lab = document.getElementById('label');
    console.log('-----------------------------',lab.value);
    var tag = document.createElement("p");
    var text = document.createTextNode(" ("+lab.value.length+" by 250)");
    tag.setAttribute('id','label-limit');
    tag.appendChild(text);
    var element = document.getElementById("label-label");
    element.appendChild(tag);
  }
</script>
<style>
  p#label-limit-err {
    color: #a94442;
    border-color: #ebccd1;
    margin-top: 0px;
    margin-bottom: 6px;

  }
  div#label-label {
    display: flex;
  }
  div#label-wrapper {
    margin-top:  7px !important;
    margin-left:  7px !important;
  }
  div#buttons-wrapper {
    margin-left: 4px;
  }
  div#label-element{
    margin-bottom: 7px;
    width: 98%;
    display: flex;
    flex-wrap: wrap-reverse;
  }
  div#max_value-label, div#min_value-label, div#default_value-label {
    margin-top: 15px;
    margin-bottom: 2px;
  }
</style>