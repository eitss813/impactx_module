

<?php
$innerHtml = '';
$defaultTemplates_rows = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayouts();

foreach ($defaultTemplates_rows as $value) {
  $checked = ((string)$value['layout_id'] == $this->value) ? 'checked="checked"' : '';
  $innerHtml .= '<li><input type="radio" name="layout" id="layout-'.$value['layout_id'].'" value="'.$value['layout_id'].'"  '.$checked.'><label for="layout-'.$value['layout_id'].'">'.$value['layout_name'].'</label>';
  if ($value['default'] == '1') {
  	$innerHtml .= '&nbsp; <a title="Preview - '.trim($value['layout_name']).'" href="'.$this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/layout_'.trim($value['layout_id']).'.png" target="_blank" class="seaocore_icon_view" > </a>';
  }
  $innerHtml .= ' </li>';
}

echo '
<div id="layout-wrapper" class="form-wrapper"><div id="layout-label" class="form-label"><label for="layout" class="optional">Select Base Layout</label></div>
<div id="layout-element" class="form-element">
'.$innerHtml.'
<ul class="form-options-wrapper">
</ul>
</div></div>
'
?>

<style type="text/css">
li
{
  list-style: none;
}
</style>
