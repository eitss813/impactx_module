<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<?php
$this->tagstring = "";
if (count($this->userTags)) {
  $count = 0;
  foreach ($this->userTags as $tag) {
    if (!empty($tag->text)) {
      if (empty($count)) {
        $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a>";
        $count++;
      } else {
        $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a>";
      }
    }
  }
}
?>

<?php if ($this->tagstring): ?>
  <h3><?php echo $this->translate('%1$s\'s Tags', $this->htmlLink($this->sitepage->getParent(), $this->sitepage->getParent()->getTitle())) ?></h3>
  <ul class="sitepage_sidebar_list">
    <li> 
      <?php echo $this->tagstring; ?>
    </li>	
  </ul>
<?php endif; ?>