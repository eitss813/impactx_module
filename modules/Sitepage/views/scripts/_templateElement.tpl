
<?php
$innerHtml = '';
$defaultTemplates_rows = Engine_Api::_()->getDbtable('templates','sitepage')->getDefaultTemplates();
$defaultLayout_rows = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayouts();
foreach ($defaultLayout_rows as $value) {
	$layouts[$value['layout_id']] = $value;
}

foreach ($defaultTemplates_rows as $value) {
  $checked = ( (string) $value['layout'] === $this->value) ? 'checked="checked"' : '';
  $innerHtml .= '<li><input type="radio" name="layout" id="layout-'.$value['layout'].'" value="'.$value['layout'].'"  '.$checked.'><label for="layout-'.$value['layout'].'"> '.$value['template_name'].'</label> &nbsp;';
  if ($layouts[$value['layout']]['default'] == '1') {
  	$innerHtml .= '&nbsp; <a title="Preview - '.trim($value['template_name']).'" href="'.$this->layout()->staticBaseUrl . 'application/modules/sitepage/externals/images/desc_layout_'.trim($value['layout']).'.png" target="_blank" class="seaocore_icon_view" > </a>';
  }
  $innerHtml .= ' </li>';
}

echo '
<div id="layout-wrapper" class="form-wrapper"><div id="layout-label" class="form-label"><label for="layout" class="optional">Select Base Template</label></div>
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
