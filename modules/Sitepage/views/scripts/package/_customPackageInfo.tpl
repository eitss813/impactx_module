<?php
	if( !empty($this->parent_id) ) {
	  $url = $this->url(array('action' => 'create', 'parent_id' => $this->parent_id),'sitepage_general');
	} elseif( !empty($this->business_id) ) {
	  $url = $this->url(array('action' => 'create', 'business_id' => $this->business_id),'sitepage_general');
	} elseif( !empty($this->group_id) ) {
	  $url = $this->url(array('action' => 'create', 'group_id' => $this->group_id),'sitepage_general');
	} elseif( !empty($this->store_id) ) {
	  $url = $this->url(array('action' => 'create', 'store_id' => $this->store_id),'sitepage_general');
	} else {
	  $url = $this->url(array('action' => 'create'),'sitepage_general');
	}
?>
<?php
  // Get template style
	$activeTemplate = Engine_Api::_()->getDbTable('templates','sitepage')->getActivatedTemplate();
  	$layout_id = $activeTemplate['layout'];
	include_once APPLICATION_PATH."/application/modules/Sitepage/views/scripts/layouts/_plansTemplate_".$layout_id.".tpl";
?>

<!-- Change button text for trial packages -->
<?php foreach ( $this->paginator as $packages) : ?>
	<?php if ($packages->duration && $packages->duration ): ?>
		<script type="text/javascript">
			var id = "#"+ "<?php echo $packages->package_id; ?>" ;
			($$(id).get('tag') == 'input') ? $$(id).set('value','Get Trial') : $$(id).set('html','Get Trial');
		</script>
	<?php endif; ?>
<?php endforeach; ?>
