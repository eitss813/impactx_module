
<?php
echo '
<div id="'.$this->element_name.'-wrapper" class="form-wrapper">
  <div id="'.$this->element_name.'-label" class="form-label">
    <label for="'.$this->element_name.'" class="optional">
      ' . $this->translate($this->label) . '
    </label>
  </div>
  <div id="'.$this->element_name.'-element" class="form-element">
    <p class="description">' . $this->translate($this->description) . '</p>
    <input name="'.$this->element_name.'" id="inputbox'.$this->order.'" value="#' .$this->value. '" type="text">
    <input name="myRainbow'.$this->order.'" id="myRainbow'.$this->order.'" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image" title="Choose Color">
    <input class = "colorPickerElement" type="text" value="'.$this->element_name.'" id="'.$this->order.'" name="colorPickerElement[]" style="display:none;">
  </div>
</div>
'
?>