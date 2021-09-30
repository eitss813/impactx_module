<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  
  window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('location'));
  });
</script>

<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
   var searchSitepages = function() {

		var formElements = $('filter_form').getElements('li');
		formElements.each( function(el) {
			var field_style = el.style.display;
			if(field_style == 'none') {
				el.destroy();
			}
		});

    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {
      $('filter_form').submit();
    }
  }
  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride':'min','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride':'max','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });

  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    ))
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

	<div class='layout_right'> 
		<?php $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);?>
		<?php $pageilike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mylike.show', 1);?>
		<?php $claimEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.claimlink', 1);?>
	<?php if((!empty($manageAdminEnabled) && !empty($this->manageadmin_count)) || (!empty($claimEnabled) && !empty($this->showClaimLink)) || (!empty($pageilike)) ): ?>
			<div class="quicklinks sitepage_manage_admin_link">
				<ul>
					<?php if(!empty($manageAdminEnabled) && !empty($this->manageadmin_count)): ?>
					<li>
						<a href='<?php echo $this->url(array('action' => 'my-pages'), 'sitepage_manageadmins', true) ?>' class="icon_sitepages_page-owner buttonlink"> <?php echo $this->translate("Pages I Admin") ?></a>
					</li>
					<?php endif; ?>
					<?php if(!empty($claimEnabled) && !empty($this->showClaimLink)): ?>			
						<li>
							<a href='<?php echo $this->url(array('action' => 'my-pages'), 'sitepage_claimpages', true) ?>' class="icon_sitepages_claim buttonlink"> <?php echo $this->translate("Pages I've Claimed") ?></a>
						</li>				
					<?php endif; ?>
					<?php if(!empty($pageilike)): ?>
					<li>
						<a href='<?php echo $this->url(array('action' => 'mylikes'), 'sitepage_like', true) ?>' class="icon_sitepages_like buttonlink"> <?php echo $this->translate("Pages I Like") ?></a>
					</li>
				<?php endif; ?>
				<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
					<li>
						<a href='<?php echo $this->url(array('action' => 'my-joined'), 'sitepage_like', true) ?>' class="icon_sitepage_join buttonlink"> <?php echo $this->translate("Pages I've Joined") ?></a>
					</li>
				<?php endif; ?>
				</ul>		
			</div>	
		<?php endif; ?>
		<div class="seaocore_searchform_criteria">
			<?php echo $this->form->render($this) ?>
		</div>	
	  <?php if( count($this->quickNavigation) > 0 ): ?>
			<div class="quicklinks">
	      <?php
	        // Render the menu
	        echo $this->navigation()
	          ->menu()
	          ->setContainer($this->quickNavigation)
	          ->render();
	      ?>
	    </div>
	  <?php endif; ?>
	</div>
	<div class='layout_middle'>
		<?php  
			$renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.renew.email', 2))));?>
	  
	  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
		  <ul class="seaocore_browse_list">
 		    <?php foreach ($this->paginator as $item): ?>
			    <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?><?php if($item->featured):?>class="lists_highlight"<?php endif;?><?php endif;?>>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
             <?php if($item->featured):?>
               <span title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></span>
            <?php endif;?>
          <?php endif;?>
			      <div class='seaocore_browse_list_photo'>
			        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align'=>'left'))) ?> 
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
            <?php if (!empty($item->sponsored)): ?>
              <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
              if (!empty($sponsored)) { ?>
                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
              <?php } ?>
            <?php endif; ?>
          <?php endif; ?>  
			      </div>
	
			      <div class='seaocore_browse_list_options'>
							<?php if ($this->can_edit): ?>
	                <?php if(empty ($item->declined)): ?>
	                <a href='<?php echo $this->url(array('page_id' => $item->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard'); ?></a>
	
	                <?php if($item->draft == 0) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $item->page_id), $this->translate('Publish Page'), array('class'=>'buttonlink smoothbox icon_sitepage_publish')) ?>
                      
                    <?php endif; ?>

	              <?php endif; ?>
								<?php if ($this->can_delete): ?>
									<a href='<?php echo $this->url(array('page_id' => $item->page_id), 'sitepage_delete', true) ?>' class='buttonlink icon_sitepages_delete'><?php echo $this->translate('Delete Page'); ?></a>
								<?php endif; ?>
								<?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($item->page_id)): ?>
								<div class="tip">
									<span>
										<a href='javascript:void(0);' onclick="submitSession(<?php echo $item->page_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
									</span>
								</div>
							<?php endif; ?>
	
	            <?php if (Engine_Api::_()->sitepage()->canShowRenewLink($item->page_id)): ?>
	              <div class="tip">
									<span>
										<a href='javascript:void(0);' onclick="submitSession(<?php echo $item->page_id ?>)"><?php echo $this->translate('Renew Page'); ?></a>
									</span>
								</div>
	            <?php endif; ?>
	          </div>
	
              <?php  $this->partial()->setObjectKey('sitepage');
				         echo $this->partial('partial_views.tpl', $item); ?>
              <?php echo $this->viewMore($item->body,200,5000) ?>
			        </div>
			      </div>
			    </li>
		    <?php endforeach; ?>
		  </ul>
	  <?php elseif ($this->search): ?>
			<div class="tip"> <span> <?php echo $this->translate('You do not have any page which matches your search criteria.');?> </span> </div>
	  <?php else: ?>
			<div class="tip">
				<span> <?php echo $this->translate('You do not have any pages yet.');?>
					<?php if ($this->can_create): ?>
						<?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
						$createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
						else:
						$createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
						endif; ?>
						<?php echo $this->translate('Get started by %1$screating%2$s a new page.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepage")); ?>

	
	<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
			<input type="hidden" name="page_id_session" id="page_id_session" />
	</form>
</div>

<script type="text/javascript">
  function submitSession(id){
    
    document.getElementById("page_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>

<?php $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitepage', 'category_id');
if(!empty($row->display)):
?>
<script type="text/javascript">
  var form;

  var subcategoryies = function(category_id, sub, subcatname, subsubcat)
  {
	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form=$('filter_form_category');
		}

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id?>';
    }
    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id?>';
    }
    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id?>';
    }
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }
    if(category_id != 0) {
      if(sub == '')
      subsubcat = 0;
      changesubcategory(sub, subsubcat);
    }

  	var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitepage_general', true);?>';
    en4.core.request.send(new Request.JSON({
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
      	clear('subcategory_id');
        var  subcatss = responseJSON.subcats;
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = sub;
          form.elements['subcategory'].value = $('subcategory_id').value;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = $('subcategory_id').value;
          if(form.elements['subsubcategory'])
          form.elements['subsubcategory'].value = subsubcat;
          if(form.elements['subsubcategory_id'])
          form.elements['subsubcategory_id'].value = subsubcat;
        }

        if(subcatss.length == 0) {
	      	form.elements['categoryname'].value = 0;
        }

        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));
  };
      var changesubcategory = function(subcatid, subsubcat)
		{
			var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitepage_general', true);?>';
   		var request = new Request.JSON({
		    url : url,
		    data : {
		      format : 'json',
		      subcategory_id_temp : subcatid
				},
		    onSuccess : function(responseJSON) {
		    	clear('subsubcategory_id');
		    	var  subsubcatss = responseJSON.subsubcats;
		      addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
            $('subsubcategory_id').value = subsubcat;
            if(form.elements[' subsubcategory_id'])
            form.elements[' subsubcategory_id'].value = $('subsubcategory_id').value;
            if(form.elements[' subsubcategory'])
            form.elements['subsubcategory'].value = $('subsubcategory_id').value;
            if($('subsubcategory_id')) {
              $('subsubcategory_id').value = subsubcat;
            }
          }

          if(subcatid == 0) {
            clear('subsubcategory_id');
            if($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
          }
		    }
			});
      request.send();
		};
        function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }
  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }
  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;			
    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'block';
      $('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } 
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }	
  

  var cat = '<?php echo $this->category_id ?>';

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';
    subcategoryies(cat, sub, subcatname,subsubcat);
  }
  
  function show_subcat(cat_id) 
  {		

    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif';
      }		
    }
  }
  
</script>

<?php endif;?>