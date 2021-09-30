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
<?php if(!empty($this->button)) :?>
	<button class="add" id="add_button" onclick='forwardAction("<?php echo $this->isValue; ?>")'><?php echo $this->button->label;?></button>
<?php else :?>
	<button class="add" id="add_button" onclick='forwardAction()'><?php echo "Add a Button"?></button>
<?php endif;?>
	<div class="_add_button_smoothbox">
		<?php
		if($this->buttonValue == true ) :
			echo $this->htmlLink(array('route' => 'sitepage_button','action' => 'edit','page_id' => $this->page_id), $this->translate('Edit'), array('class' => 'smoothbox'));?>
			<span> | </span>
			<?php
			echo $this->htmlLink(array('route' => 'sitepage_button','action' => 'delete','page_id' => $this->page_id), $this->translate('Delete'), array('class' => 'smoothbox'));
		
		endif;?>
	</div>
<script type="text/javascript">
	function forwardAction(value) {
		if(value) {
			var url = "<?php echo $this->button->url;?>"
			window.open(url,"_blank");
		} else {
			url = "<?php echo $this->url(array('action' => 'add','page_id' => $this->page_id, 'format' => 'smoothbox'),'sitepage_button',true); ?>";
			Smoothbox.open(url);
		}
	}
</script>