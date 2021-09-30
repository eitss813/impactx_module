<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: review-parameter.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<div class='clear'>
  <div class='settings global_form_popup sesblog_review_parameter_popup'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="application/javascript">
  var alreadyaddedParameter = scriptJquery('.sesblog_review_added_parameter');
  if(alreadyaddedParameter.length > 0){
    for(var i=0;i<alreadyaddedParameter.length;i++){
      var id = scriptJquery(alreadyaddedParameter[i]).attr('id').replace('sesblog_review_','');
      scriptJquery(alreadyaddedParameter[i]).parent().append('<a href="javascript:;" data-url="'+id+'" class="removeAlreadyAddedElem fa fa-trash">Remove</a>');
    }
  }
  scriptJquery(document).on('click','.removeAlreadyAddedElem',function(e){
    var id = scriptJquery(this).attr('data-url');
    var val = scriptJquery('#deletedIds').val();
    if(val)
      var oldVal = val+',';
    else
      var oldVal = '';
    scriptJquery('#deletedIds').val(oldVal+id);
    scriptJquery(this).parent().parent().remove();
  });
  scriptJquery(document).on('click','#addmoreelem',function(e){
    scriptJquery('<div><input type="text" name="parameters[]" value="" class="reviewparameter"><a href="javascript:;" class="removeAddedElem fa fa-trash">Remove</a></div>').insertBefore(this);
  });
  scriptJquery(document).on('click','.removeAddedElem',function(e){
    scriptJquery(this).parent().remove();
  });
</script>
