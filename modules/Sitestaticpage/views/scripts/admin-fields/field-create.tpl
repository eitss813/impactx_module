<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: field-create.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script>
	function toggle_field(id, id2) {
		var state = document.getElementById(id).style.display;
		if (state == 'block') {
			document.getElementById(id).style.display = 'none';
			document.getElementById(id2).style.display = 'block';
			document.getElementById("map-field-button").setAttribute("class", "admin_button_disabled");
			document.getElementById("create-field-button").setAttribute("class", "");
		} 
		else {
			document.getElementById(id).style.display = 'block';
			document.getElementById(id2).style.display = 'none';
			document.getElementById("create-field-button").setAttribute("class", "admin_button_disabled");
			document.getElementById("map-field-button").setAttribute("class", "");
		}
  }
</script>

<?php if( $this->form ): ?>
  <?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js');
  ?>
  <div id="create-field">
    <?php echo $this->form->render($this) ?>
  </div>

  <?php if( !empty($this->formAlt) ): ?>
    <div id="map-field" style="display: none;">
      <?php echo $this->formAlt->render($this) ?>
    </div>
  <?php endif; ?>
<?php else: ?>
  <div class="global_form_popup_message">
    <?php echo "Your changes have been saved."; ?>
  </div>
  <script type="text/javascript">
    parent.onFieldCreate(
      <?php echo Zend_Json::encode($this->field) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>
<?php endif; ?>