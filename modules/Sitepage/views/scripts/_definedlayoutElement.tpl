
<div class="_select_layout_container">
<?php
$pageList = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayouts();
                foreach( $pageList as $pageRow ) :
                    if($pageRow->status == 1) :
?>
<div class="_select_layout">
<input type="radio" name="layout_id" value = "<?php echo $pageRow->getIdentity();?>" <?php echo ($this->layout == $pageRow->getIdentity()) ?  "checked" : "" ;  ?>>
<img src="<?php echo $pageRow->getPhotoUrl('thumb.icon')?>">
<div class="_view_sitepage_layout"><a title="Preview" href="<?php echo $pageRow->getPhotoUrl();?>" target="_blank" class="seaocore_icon_view" ></a></div>
</div>
<?php endif;?>
<?php endforeach;?>
</div>