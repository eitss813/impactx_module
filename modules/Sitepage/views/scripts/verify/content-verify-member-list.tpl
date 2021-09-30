<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content-verify-member-list.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepageverify.css') ?>
                    
<script type="text/javascript">
  var current_page = '<?php echo $this->current_page; ?>';
  var resource_id = '<?php echo $this->resource_id; ?>';
  var paginateUserVerify = function(page) {
    var url = en4.core.baseUrl + 'siteverify/index/content-verify-member-list/resource_id/' + resource_id;
    en4.core.request.send(new Request.HTML({
      'url': url,
      'data': {
        'format': 'html',
        'page': page
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        en4.core.runonce.trigger();
      }
    }), {
      'element': document.getElementById('sitepage_page_anchor').getParent()
    });
  }
</script>
<a id="sitepage_page_anchor" style="position:absolute;"></a>
<div class="seaocore_pages_popup" id="verify_members_popup">
  <div class='sitepage_users_block_links'>
    <div class="top">
      <h3>
        <?php echo $this->translate("%s has been verified by:", $this->resource_title);
        ?>
      </h3>
    </div>
    <div class="seaocore_pages_popup_content" id="verify_popup_content" >
      <?php if ($this->verify_count > 1): ?>
        <?php if ($this->current_page > 1): ?>
          <div class="seaocore_pages_popup_paging">
            <div id="user_group_pages_previous" class="paginator_previous">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => 'paginateUserVerify(parseInt(current_page) - parseInt(1))'
              ));
              ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
      <?php
      if (COUNT($this->paginator)) :
        foreach ($this->paginator as $item):
          $user = Engine_Api::_()->getItem('user', $item->poster_id);
          ?>
          <div class="item_member_list">
            <div class="item_member_thumb">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('target' => '_blank'));
              ?>
            </div>
            <div class="item_member_details">
              <div class="item_member_name">
                <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank')); ?>
              </div>
              <span class='span_comment'> <?php echo $item->comments; ?> </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if ($this->verify_count > 1): ?>
      <?php if ($this->current_total_verify < $this->verify_count): ?>
        <div class="seaocore_members_popup_paging">
          <div id="user_group_members_next" class="paginator_next">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => 'paginateUserVerify(parseInt( current_page) + parseInt(1))'
            ));
            ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="verify_members_popup_btns">
    <button  onclick='javascript:parent.Smoothbox.close();'>
      <?php echo $this->translate("Cancel") ?></button>
  </div>

  <?php
else:
  echo $this->translate('No results were found.');
endif;
?>
