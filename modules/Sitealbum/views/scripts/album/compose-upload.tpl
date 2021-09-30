<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id$
 * @author     Sami
 */
?>

<script type="text/javascript">
  $try(function(){
    parent.en4.album.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars()) ?>);
  });
  $try(function() {
    parent._composePhotoResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
  });
</script>