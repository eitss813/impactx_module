<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<script type="text/javascript">
  //<![CDATA[
  window.addEvent('domready', function() {
    if ($('type'))
      addReviewTypeOptions($('type').value);
    $('order').addEvent('change', function() {
      $(this).getParent('form').submit();
    });
  });
  //]]>
  var addReviewTypeOptions = function(value) {
    if (!$('recommend-wrapper'))
      return;
    if (value == 'user') {
      $('recommend-wrapper').style.display = 'block';
    } else {
      $('recommend-wrapper').style.display = 'none';
    }
  }
</script>

<div class="seaocore_searchform_criteria">
<?php echo $this->searchForm->render($this) ?>
</div>