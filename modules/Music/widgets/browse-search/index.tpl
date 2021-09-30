<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <john@socialengine.com>
 */
?>

<script type="text/javascript">
  //<![CDATA[
    en4.core.runonce.add(function() {
      $$('#sort, #show').addEvent('change', function(){
        $(this).getParent('form').submit();
      });
    });
  //]]>
</script>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>
