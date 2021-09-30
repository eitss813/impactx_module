<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _profield.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
 $session = new Zend_Session_Namespace('profileFields');
   $profileFields=array();
    $profileIds=array();
    if(isset ($session->profileFields)){
      $profileFields=$session->profileFields;    
      unset($session->profileFields);

      foreach ($profileFields as $value):
        $profile_fields_explode=explode("_",$value);
        $profileIds[] = $profile_fields_explode['1'];
      endforeach;      
     $profileIds = array_unique($profileIds);
    }
?>
<style type="text/css">
  .form-options-wrapper li{
    padding:0px;
  }
</style> 
<div id="profilefields" class="form-wrapper" style="border-top:none;padding:0px;">
  <div class="form-label" >
    <label>
      &nbsp;
    </label>
  </div>
  <div class="form-element">
    <ul class="form-options-wrapper">
      <li style="clear:both; margin-bottom:10px;">
        <?php
        $options = Engine_Api::_()->getDBTable('options', 'sitepage')->getAllProfileTypes();
        foreach ($options->toarray() as $opt) :
          $selectOption = Engine_Api::_()->getDBTable('metas', 'sitepage')->getFields($opt['option_id']);
        
         ?>
          <div class="form-label"  id="profile_<?php echo $opt['option_id'] ?>" >
            <label style="font-weight: bold;">
              <a href="javascript:void(0);" onclick="profile_show('<?php echo $opt['option_id'] ?>')" >  <span id="image_<?php echo $opt['option_id'] ?>" > <?php echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/plus16.gif', '', array('title'=> $this->translate('Show Profile Fields.'))) ?></span> </a>
                  <a href="javascript:void(0);" onclick="profile_show('<?php echo $opt['option_id'] ?>')" > <?php echo $opt['label']; ?></a>
            </label>
         </div>

         <?php  // ELEMENTS OF PROFILE TYPE SPECIFY    ?>
          <div id="profile_fields_<?php echo $opt['option_id'] ?>"  style="display: none;">
            <?php if(!empty ($selectOption)):
                  foreach ($selectOption as $key => $value): ?>
                    <div style="clear:both; margin-left: 10%;">
                      <input type='checkbox' value=<?php echo $key ?>  name="<?php echo $opt['option_id'] . '_profilecheck_' . $key ?>"  <?php if(in_array('1_'. $opt['option_id'].'_'.$key,  $profileFields)):?>  checked <?php endif; ?>/> <?php echo $value['lable']." (".ucfirst($value['type']).")" ?>
                    </div>
                <?php
                  endforeach;
               else: ?>
                 <div  class="tip" style="clear:both; margin-left: 10%;">
                   <span>
                    <?php   echo $this->translate("No Profile Fields."); ?>
                   </span>
                 </div>
             <?php endif;?>
          </div>
        <?php
        endforeach;?>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">

  window.addEvent('domready', function() {

	if($("profile-2")){
     var value=document.getElementById("profile-2").checked;
     if (value)
      {
        $('profilefields').style.display='block';
      }else{
       $('profilefields').style.display='none';
      }
  }

  <?php foreach($profileIds as $profile_id): ?>

      profile_show(<?php echo $profile_id; ?>);
   <?php endforeach;?>

});
  function showprofileOption(optionsrootfiles) {
    if($('profilefields')) {
      if(optionsrootfiles == 2) {
        $('profilefields').style.display='block';
      }
      else {
        $('profilefields').style.display='none';
      }
    }
  }

</script>


<script type="text/javascript">
  function profile_show(id) {

    var profile_fields_id="profile_fields_"+id;
    var image_id="image_"+id;
    if($(profile_fields_id).style.display == 'block') {
      $(profile_fields_id).style.display = 'none';
      $(image_id).innerHTML='<?php echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/plus16.gif', '', array('title'=> $this->translate('Show Profile Fields.'))) ?>';
    } else {
      $(profile_fields_id).style.display = 'block';
      $(image_id).innerHTML='<?php echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/minus16.gif', '', array('title'=> $this->translate('Hide Profile Fields.'))) ?>';

    }
  }
</script>