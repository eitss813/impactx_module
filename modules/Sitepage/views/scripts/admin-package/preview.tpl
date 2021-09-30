
<div class="admin_form">
	<?php 
	if ($this->formError == 1)
		echo "<div class='tip'><span>".$this->translate("Error occured while writing file.Check file permissions of ". APPLICATION_PATH . "/public folder.") ."</span></div>";
	else 
		include_once APPLICATION_PATH."/application/modules/Sitepage/views/scripts/layouts/_plansTemplate_".$this->layout_id.".tpl"; 
	?>
</div>

<script type="text/javascript">
	function submitForm(id) {
		console.warn('Submit disabled. Function overriding.')
	}
</script>
<style type="text/css">
	html,body{
		overflow-x: hidden ;
	}
	.container{
		width: 1200px !important;
	}
	#global_page_sitesubscription-admin-layout-preview
	{
		text-align: center !important;
	}
</style>

<script type="text/javascript">

	_imgs ={
		layout_id: <?php echo $this->layout_id; ?>,
		document_obj: window.parent.document,
		def_tick: 'application/modules/Sitepage/externals/images/tick_image.png',
		def_cross: 'application/modules/Sitepage/externals/images/cross_image.png',
		setPreviewImages: function() {

			if (_imgs.document_obj.getElementsByName('tick_image')[0] == null || _imgs.document_obj.getElementsByName('cross_image')[0] == null) 
			return;

			_imgs.getDataUrl('tick_image');
			_imgs.getDataUrl('cross_image');
		},
		getDataUrl: function(inputName) {
			if (_imgs.document_obj.getElementsByName(inputName)[0].files[0] == null) {
				var src = ( inputName == 'tick_image' ) ? _imgs.def_tick : _imgs.def_cross;
				$$('.'+inputName).set('src',src);
				return null;
			}

			var reader = new FileReader;
			reader.readAsDataURL(_imgs.document_obj.getElementsByName(inputName)[0].files[0]);
			reader.onload = function() {
				$$('.'+inputName).set('src',reader.result);
			}
		}
	}

	if (_imgs.layout_id == '5') {
		_imgs.setPreviewImages();
	}
</script>