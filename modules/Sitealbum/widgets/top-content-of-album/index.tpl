<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css'); ?>

<script type="text/javascript">
  var CommentLikesTooltips;
  var el_sitealbum;
  en4.core.runonce.add(function() {
    // Add hover event to get tool-tip
    var show_tool_tip = false;
    var counter_req_pendding = 0;
    $$('.seao_common_add_tooltip_link').addEvent('mouseover', function(event) {
      var el = $(event.target);
      el_sitealbum = el;
      ItemTooltips.options.offset.y = el.offsetHeight;
      ItemTooltips.options.showDelay = 0;
      if (!el.hasAttribute("rel")) {
        el = el.parentNode;
      }
      show_tool_tip = true;
      if (!el.retrieve('tip-loaded', false)) {
        counter_req_pendding++;
        var resource = '';
        if (el.hasAttribute("rel"))
          resource = el.rel;
        if (resource == '')
          return;

        el.store('tip-loaded', true);
        el.store('tip:title', '<div class="" style="">' +
                ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">' +
                '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">' +
                '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>' +
                '</div></div></div></div>'
                );
        el.store('tip:text', '');
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'show-tooltip-info'), 'default', true) ?>';
        el.addEvent('mouseleave', function() {
          show_tool_tip = false;
        });

        var req = new Request.HTML({
          url: url,
          data: {
            format: 'html',
            'resource': resource
          },
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            el.store('tip:title', '');
            el.store('tip:text', responseHTML);
            ItemTooltips.options.showDelay = 0;
            ItemTooltips.elementEnter(event, el); // Force it to update the text 
            counter_req_pendding--;
            if (!show_tool_tip || counter_req_pendding > 0) {
              //ItemTooltips.hide(el);
              ItemTooltips.elementLeave(event, el);
            }
            var tipEl = ItemTooltips.toElement();
            tipEl.addEvents({
              'mouseenter': function() {
                ItemTooltips.options.canHide = false;
                ItemTooltips.show(el);
              },
              'mouseleave': function() {
                ItemTooltips.options.canHide = true;
                ItemTooltips.hide(el);
              }
            });
            Smoothbox.bind($$(".seao_common_add_tooltip_link_tips"));
          }
        });
        req.send();
      }
    });
    // Add tooltips
    var window_size = window.getSize()
    var ItemTooltips = new SEATips($$('.seao_common_add_tooltip_link'), {
      fixed: true,
      title: '',
      className: 'seao_common_add_tooltip_link_tips',
      hideDelay: 200,
      offset: {'x': 0, 'y': 0},
      windowPadding: {'x': 370, 'y': (window_size.y / 2)}
    });
  });
</script>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

$this->headScript()->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
?>
<?php if (!empty($this->showInformationOptions)): ?>
  <div class="mbot10 o_hidden">
    <?php if ($this->canEdit) : ?>
      <?php if (in_array('likeButton', $this->showInformationOptions) && $this->canComment): ?>
        <div class="seaocore_like_button sitealbum_like_button">
          <a id="<?php echo $this->subject()->getType() ?>unlike_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> >	<i class="seaocore_like_thumbdown_icon"></i>
            <span><?php echo $this->translate('Unlike') ?></span>
          </a>
          <a id="<?php echo $this->subject()->getType() ?>like_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?>  >	<i class="seaocore_like_thumbup_icon"></i>
            <span><?php echo $this->translate('Like') ?></span>
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>    
    <?php if (in_array('editmenus', $this->showInformationOptions) && count($this->navigation) > 0): ?>
      <div id="sitealbum_topcontent_optblock" class="sitealbum_topcontent_optblock">
        <span>
          <?php foreach ($this->navigation as $link): ?>
            <?php if ($link->params['action'] == 'upload' || $link->params['action'] == 'editphotos' || $link->params['action'] == 'edit'): ?>
            
                <?php if($link->params['action'] != 'upload'):?>
                  <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array('target' => $link->get('target'))) ?>
                <?php else:?>
            
            <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                  <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array('class' => 'seao_smoothbox','target' => $link->get('target'), 'data-SmoothboxSEAOClass' => "seao_add_photo_lightbox")) ?>
            
            <?php else:?>
             <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array('target' => $link->get('target'))) ?>
            
            <?php endif;?>
                <?php endif;?>
            <?php endif; ?>
          <?php endforeach; ?>
        </span>
        <?php if ($this->canEdit): ?>
          <span class="mleft5">
            <a href="javascript:void(0);" onclick="PulDownOptions();" id="polldown_options">
              <i class="icon_cog"></i>
              <i class="icon_down"></i>
            </a>
            <ul id="options_pulldown" style="display:none;">
              <?php foreach ($this->navigation as $link): ?>
                <?php if ($link->params['action'] == 'delete' || $link->params['action'] == 'create' || $link->params['action'] == 'add-album-of-day' || $link->params['action'] == 'get-link' || $link->params['action'] == 'edit-location' || $link->params['action'] == 'switch-popup'): ?>
                  <li>
                    <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array('class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ), 'style' => 'background-image: url(' . $link->get('icon') . ');', 'target' => $link->get('target'))) ?>
                  </li>
                <?php endif; ?>

              <?php endforeach; ?>
            </ul>
          </span>
        <?php else: ?>
          <?php if (in_array('likeButton', $this->showInformationOptions) && $this->canComment): ?>
            <div class="seaocore_like_button sitealbum_like_button">
              <a id="<?php echo $this->subject()->getType() ?>unlike_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> >	<i class="seaocore_like_thumbdown_icon"></i>
                <?php echo $this->translate('Unlike') ?>
              </a>
              <a id="<?php echo $this->subject()->getType() ?>like_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?>  >	<i class="seaocore_like_thumbup_icon"></i>
                <?php echo $this->translate('Like') ?>
              </a>
            </div>
          <?php endif; ?>
          <?php foreach ($this->navigation as $link): ?>
            <?php if ($link->params['action'] == 'switch-popup'): ?>
              <span><?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array('target' => $link->get('target'))) ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if (in_array('title', $this->showInformationOptions)): ?>
    <div class="clr <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoTitle">
      <?php
      echo $this->translate('%1$s', ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
      );
      ?>
    </div>
  <?php endif; ?>

  <?php if (in_array('owner', $this->showInformationOptions)): ?>
    <div class="o_hidden <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoAction">

      <?php echo $this->translate("By "); ?><?php echo $this->htmlLink($this->album->getOwner()->getHref(), $this->album->getOwner(), array('title' => $this->album->getOwner(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel' => 'user' . ' ' . $this->subject()->getIdentity())); ?>
    </div>
  <?php endif; ?>

  <?php if (in_array('updateddate', $this->showInformationOptions) || in_array('location', $this->showInformationOptions)): ?>
    <div class="o_hidden <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoAction">
      <?php if (in_array('updateddate', $this->showInformationOptions)): ?>
        <?php echo $this->translate('Updated about %1$s', $this->timestamp($this->album->modified_date)) ?>
      <?php endif ?>
    </div>
    <div class="o_hidden <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoAction">
      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) && ($this->album->location) && in_array('location', $this->showInformationOptions)): ?>
        <?php echo $this->translate('Taken at %1$s', $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->album->seao_locationid, 'resouce_type' => 'seaocore'), $this->album->location, array('class' => 'smoothbox', 'title' => $this->album->location))); ?>
      <?php endif ?>
    </div>
  <?php endif; ?>

  <?php if ((in_array('description', $this->showInformationOptions) && ('' != trim($this->album->getDescription()))) || (in_array('categoryLink', $this->showInformationOptions) && $this->album->category_id) || (in_array('tags', $this->showInformationOptions) && count($this->sitealbumTags) > 0)): ?> 
    <div class="sitealbum_headerDescription <?php if ($this->showLayout == 'center'): ?>txt_center<?php else: ?>fleft<?php endif; ?>">
      <?php if (in_array('description', $this->showInformationOptions) && ('' != trim($this->album->getDescription()))): ?> 
        <?php echo $this->album->getDescription() ?>
      <?php endif ?>

      <?php
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && in_array('categoryLink', $this->showInformationOptions) && $this->album->category_id) :
        $categoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($this->album->category_id);
        ?>
        <div class="o_hidden <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoAction">
          <?php echo $this->translate("Category:"); ?>
          <a href="<?php echo $this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug()), 'sitealbum_general_category', true) ?>">
            <span><?php echo $this->translate("$categoryName"); ?></span>
          </a> 
        </div>
      <?php endif; ?>

      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0) && in_array('tags', $this->showInformationOptions) && count($this->sitealbumTags) > 0): $tagCount = 0; ?>
        <div class="o_hidden <?php if ($this->showLayout == 'center'): ?>txt_center<?php endif; ?> sitealbum_photoAction">
          <?php echo $this->translate("Tags: "); ?>
          <?php foreach ($this->sitealbumTags as $tag): ?>
            <?php if (!empty($tag->getTag()->text)): ?>
              <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
              <?php if (empty($tagCount)): ?>
                <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'><?php echo $tag->getTag()->text ?></a><?php if (count($this->sitealbumTags) != $tagCount): echo ','; ?><?php endif; ?>
                <?php
                $tagCount++;
              else:
                ?>
                <a href='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'><?php echo $tag->getTag()->text ?></a><?php $tagCount++;
            if (count($this->sitealbumTags) != $tagCount): echo ','; ?><?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
<!--FACEBOOK LIKE BUTTON START HERE-->
<?php
if (!empty($this->showInformationOptions) && in_array('facebooklikebutton', $this->showInformationOptions)):
  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
  if (!empty($fbmodule)) :
    $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
    if (!empty($enable_facebookse) && !empty($fbmodule->version)) :
      $fbversion = $fbmodule->version;
      if (!empty($fbversion) && ($fbversion >= '4.1.7')) :
        ?>

        <div >
          <script type="text/javascript">
          var fblike_moduletype = 'album';
          var fblike_moduletype_id = '<?php echo $this->subject()->getIdentity(); ?>'
          </script>
          <?php echo Engine_Api::_()->facebookse()->isValidFbLike('album') . '<br />'; ?>
        </div>

      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<div class="sitealbum_album_options">
  <?php if ($this->canMakeFeatured && !$this->allowView): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("SITEALBUM_VIEW_PRIVACY_MESSAGE"); ?>
      </span>
    </div>
  <?php endif; ?>
</div>
<script type="text/javascript">
  function PulDownOptions() {
    var parent = $('options_pulldown').getParent('.sitealbum_topcontent_optblock');
    if (parent) {
      var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
      $('options_pulldown').inject(parent);
      $('options_pulldown').setStyles({
        'position': 'absolute',
        'top': parent.getCoordinates().bottom,
        'right': rightPostion
      });
    }

    if ($('options_pulldown').style.display == 'none') {
      $('options_pulldown').style.display = "block";
    } else {
      $('options_pulldown').style.display = "none";
    }

    document.body.removeEvents('click').addEvent('click', function(event) {
      if ($('options_pulldown').style.display == 'block' && event.target != '' && event.target.id != 'polldown_options' && event.target.className != 'icon_down' && event.target.className != 'icon_cog') {
        $('options_pulldown').style.display = 'none';
      }
    });
  }
</script>
<?php if ($this->showLayout == 'center'): ?>
  <style type="text/css">
    .layout_sitealbum_top_content_of_album #contentlike-fb{
      text-align:center;
    }
  </style>
<?php endif; ?>