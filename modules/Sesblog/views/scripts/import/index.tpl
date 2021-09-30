<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php ?>
<div class="layout_middle">
<?php echo $this->content()->renderWidget('sesblog.browse-menu',array()); ?>
</div>
<div class="sesbasic_bxs">
  <?php echo $this->form->render($this);?>
</div>


<script type="text/javascript">

  document.getElementById('file_data-wrapper').style.display = 'none';
  document.getElementById('user_name-wrapper').style.display = 'none';
  document.getElementById('submit-wrapper').style.display = 'none';

  var e = document.getElementById("import_type");
  var importType = e.options[e.selectedIndex].value;
  if(importType == '3') {
    document.getElementById('user_name-wrapper').style.display = 'block';
    document.getElementById('submit-wrapper').style.display = 'block';
  }
  else if(importType == '1' || importType == '2')
  document.getElementById('file_data-wrapper').style.display = 'block';
  

  function showImportOption(value) {
    if(value == '3' || value == '4') {
      document.getElementById('file_data-wrapper').style.display = 'none';
      document.getElementById('user_name-wrapper').style.display = 'block';
      document.getElementById('submit-wrapper').style.display = 'block';
    }
    else {
      document.getElementById('file_data-wrapper').style.display = 'block';
      document.getElementById('user_name-wrapper').style.display = 'none  ';
      document.getElementById('submit-wrapper').style.display = 'block';
    }
  }
</script>
