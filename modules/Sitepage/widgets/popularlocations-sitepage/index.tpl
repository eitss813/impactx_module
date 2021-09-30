<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var locationAction =function(cityValue)
  {
    if($("tag"))
      $("tag").value='';
    var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_location')){
				form=$('filter_form_location');
			}
    form.elements['sitepage_location'].value = cityValue;
    
		form.submit();
  }

</script>

<ul class="seaocore_browse_category">
  <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
    <input type="hidden" id="sitepage_location" name="sitepage_location"  value=""/>
  </form>
  <?php foreach ($this->sitepageLocation as $sitepageLocation): ?>
    <?php if (!empty($sitepageLocation->city) ||  !empty($sitepageLocation->state)): ?>
      <li>
        <div class="cat"  <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $sitepageLocation->city ||  $this->searchLocation == $sitepageLocation->state ) ): ?>style="font-weight: bold;" <?php endif; ?> >
          <a href="javascript:void(0);" onclick="locationAction('<?php if(!empty($sitepageLocation->city))echo $sitepageLocation->city; else echo $sitepageLocation->state;  ?>')" ><?php echo ucfirst($sitepageLocation->city) ?><?php $state=null;if(!empty($sitepageLocation->city)&& !empty($sitepageLocation->state))$state.=" [";$state.=ucfirst($sitepageLocation->state);if(!empty($sitepageLocation->city)&& !empty($sitepageLocation->state))$state.="] ";echo $state;?></a>
          <?php if(!empty($sitepageLocation->city)): echo "(" . $sitepageLocation->count_location . ")"; else: echo "(" . $sitepageLocation->count_location_state . ")"; endif;?>
        </div>	
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>