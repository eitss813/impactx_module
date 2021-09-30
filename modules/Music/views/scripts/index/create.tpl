<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
  var playlist_id = <?php echo $this->playlist_id ?>;
  function updateTextFields() {
    if ($('playlist_id').selectedIndex > 0) {
      $('title-wrapper').hide();
      $('description-wrapper').hide();
      $('search-wrapper').hide();
    } else {
      $('title-wrapper').show();
      $('description-wrapper').show();
      $('search-wrapper').show();
    }
  }

  en4.core.runonce.add(function () {
    new Uploader('upload_file', {
      uploadLinkClass : 'buttonlink icon_music_new',
      uploadLinkTitle : '<?php echo $this->translate("Add Music");?>',
      uploadLinkDesc : '<?php echo $this->translate("_MUSIC_UPLOAD_DESCRIPTION");?>'
    });
  });
  // populate field if playlist_id is specified
  if (playlist_id > 0) {
    $$('#playlist_id option').each(function(el, index) {
      if (el.value == playlist_id)
        $('playlist_id').selectedIndex = index;
    });
    updateTextFields();
  }
</script>


<script type="text/javascript">
  $$('.core_main_music').getParent().addClass('active');
</script>
