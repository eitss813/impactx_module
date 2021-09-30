<div id = "<?php echo $this->name.'-wrapper'?>" class="form-element" >
<?php echo $this->value[$this->name.start]?>
	<span>Start</span><input type="time" name="<?php echo $this->name.start;?>" id="<?php echo $this->name.start;?>" step="1" value="<?php echo $this->value[start]?>" onblur="filterTime(this.value,<?php echo $this->name.end;?> )" >
	<span>End</span><input type="time" name="<?php echo $this->name.end;?>" id="<?php echo $this->name.end;?>" step="1" value="<?php echo $this->value[end]?>">
</div>