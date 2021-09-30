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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitepage/externals/scripts/core.js'); ?>
<?php if($this->loaded_by_ajax && !$this->isajax):?>
<script>

  var browsetagparams = {
            requestParams:<?php echo json_encode($this->allParams) ?>,
            responseContainer: $$('.layout_sitepage_tagcloud_sitepage'),
            requestUrl: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            loading: true
        };

  en4.core.runonce.add(function() {  
    browsetagparams.responseContainer.each(function(element) {   
     new Element('div', {
        'class': 'sitepage_profile_loading_image'
      }).inject(element);
    });
  en4.sitepage.ajaxTab.sendReq(browsetagparams);

  });

 </script>           
<?php endif;?>

<?php if($this->showcontent):?>
    <script type="text/javascript">

      var tagAction = function(tag){
        if($('filter_form')) {
           form=document.getElementById('filter_form');
          }else if($('filter_form_tag')){
            form=$('filter_form_tag');
        }   
        form.elements['tag'].value = tag;
        if( $('filter_form'))
        $('filter_form').submit();
        else
        $('filter_form_tag').submit();
      }
    </script>

    <form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag"  value=""/>
    </form>

    <h3><?php echo $this->translate('Popular Page Tags'); ?> (<?php echo $this->count_only ?>)</h3>
    <ul class="sitepage_sidebar_list">
      <li>
        <?php foreach ($this->tag_array as $key => $frequency): ?>
          <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
          <?php ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>);' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
        <?php endforeach; ?>
        <br/>
        <b class="explore_tag_link"><?php echo $this->htmlLink(array('route' => 'sitepage_tags', 'action' => 'tagscloud','category_id' => $this->category_id), $this->translate('Explore Tags &raquo;')) ?></b>
      </li>
    </ul>
<?php endif;?>