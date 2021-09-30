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
<?php 
$this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbumshow.navigation.tabs', 7);
$headding = "Albums";
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>


<div class="headline">
  <h2>
<?php echo $this->translate($headding); ?>
  </h2>
    <?php if (count($this->navigation)) { ?>
    <div class='tabs'>
    <?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
      <ul class='navigation'>
      <?php $key = 0; ?>
          
        <?php foreach ($this->navigation as $nav): ?>
          <?php $data_smoothboxValue ='';?>
          <?php if ($key < $this->max): ?>
            <li <?php if ($nav->active): echo "class='active'";endif; ?>>
              <?php if ($nav->action):?>
                <?php if(isset($nav->data_SmoothboxSEAOClass)):?>
                    <?php $data_smoothboxValue = $nav->data_SmoothboxSEAOClass;?>
                <?php endif;?>
                
                <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                <a class="<?php echo $nav->class ?>" <?php if(isset($nav->data_SmoothboxSEAOClass)):?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue;?>" <?php endif;?> href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                <?php else:?>
                
                <?php 
                $smoothboxClass = @explode(' ', $nav->class);
                
                if(in_array('seao_smoothbox', $smoothboxClass)) {
                   unset($smoothboxClass[0]);
                   $nav->class = implode(' ' , $smoothboxClass);
                }
                ?>
                
                <a class="<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                <?php endif;?>
              <?php else : ?>
                <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                <a class= "<?php echo $nav->class ?>" <?php if(isset($nav->data_SmoothboxSEAOClass)):?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue;?>" <?php endif;?> href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                <?php else:?>
                
                <?php 
                $smoothboxClass = @explode(' ', $nav->class);
                
                if(in_array('seao_smoothbox', $smoothboxClass)) {
                   unset($smoothboxClass[0]);
                   $nav->class = implode(' ' , $smoothboxClass);
                }
                ?>
                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                <?php endif;?>
              <?php endif; ?>
            </li>
          <?php else: ?>
            <?php break; ?>
          <?php endif; ?>
          <?php $key++ ?>
        <?php endforeach; ?>

        <?php if (count($this->navigation) > $this->max): ?>
          <li class="tab_closed more_tab" onclick="moreTabSwitchSitealbum($(this));">
            <div class="tab_pulldown_contents_wrapper">
              <div class="tab_pulldown_contents">          
                <ul>
                  <?php $key = 0; ?>
                  <?php foreach ($this->navigation as $nav): ?>
                    <?php if ($key >= $this->max): ?>
                      <li <?php if ($nav->active): echo "class='active'";
              endif; ?> >
                        <?php if ($nav->action): ?>
                          
                           <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                          <a <?php if(isset($nav->data_SmoothboxSEAOClass)):?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue;?>" <?php endif;?> class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                          <?php else:?>
                          
                          <?php 
                $smoothboxClass = @explode(' ', $nav->class);
                
                if(in_array('seao_smoothbox', $smoothboxClass)) {
                   unset($smoothboxClass[0]);
                   $nav->class = implode(' ' , $smoothboxClass);
                }
                ?>
                           <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                          <?php endif;?>
                        <?php else : ?>
                           
                            <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                          <a <?php if(isset($nav->data_SmoothboxSEAOClass)):?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue;?>" <?php endif;?> class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                          <?php else:?>
                          
                          <?php 
                $smoothboxClass = @explode(' ', $nav->class);
                
                if(in_array('seao_smoothbox', $smoothboxClass)) {
                   unset($smoothboxClass[0]);
                   $nav->class = implode(' ' , $smoothboxClass);
                }
                ?>
                          <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                          
                          <?php endif; ?>
                        <?php endif; ?>
                      </li>
                    <?php endif; ?>
                    <?php $key++ ?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  <?php } ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() {
   
    var moreTabSwitchSitealbum = window.moreTabSwitchSitealbum = function(el) {
      el.toggleClass('seaocore_tab_open active');
      el.toggleClass('tab_closed');
    }
  });
</script>

<script type="text/javascript">
        $$('.core_main_album').getParent().addClass('active');
    </script>