<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$showFooterBackgroundImage = "'" . $this->showFooterBackgroundImage . "'"; ?>
<style>
<?php if($this->selectFooterBackground == 2): ?>
  .layout_page_footer { background-image: url(<?php echo $showFooterBackgroundImage; ?>);}
<?php endif; ?>
</style>

<?php
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/styles/dating_footer.css');
  $this->headLink()->appendStylesheet("https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
  $this->headLink()->appendStylesheet("https://fonts.googleapis.com/css?family=Roboto:400,500,700");
?>

<?php if( $this->showFooterTip ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please configure footer menu from admin panel.'); ?>
    </span>
  </div>
<?php  endif; ?>


<div class="versatile_main_footer_section sitecore_main_footer_section_<?php echo $this->templates; ?>">
 
  <div class="versatile_footer_section">

      <?php if($this->templates == 4): ?>
      <div class="main_container">
          <ul class="versatile_check_assign">
              <li>
                  <div class="versatile_contact_info">

                      <div class="Address_bar_section">

                          <!--<div class="address_bar_section_inner">
                              <?php if( !empty($this->mobile) ): ?>
                              <p><i class="fa fa-phone"></i><?php echo $this->mobile; ?></p>
                              <?php endif; ?>
                          </div> -->

                          <div class="address_bar_section_inner">
                              <?php if( !empty($this->mail) ): ?>
                              <p><i class="fa fa-envelope-o"></i><?php echo $this->mail; ?></p>
                              <?php endif; ?>
                          </div>

                          <div class="address_bar_section_inner">
                              <?php if( !empty($this->website) ): ?>
                              <a href="<?php echo $this->website; ?>" target="_blank">  <i class="fa fa-globe"></i><?php echo $this->website; ?></a>
                              <?php endif; ?>
                          </div>
                      </div>
                  </div>
              </li>
              <li>
                  <div class="versatile_contact_info image-desc-info">
                      <?php
              if( $this->showFooterLogo ) {
                      if( $this->selectFooterLogo ) {
                            if($this->viewer()->getIdentity()){
                                echo '<a href="' . $this->url(array('action' => 'home'), "user_general", true) . '"><img src="' . $this->selectFooterLogo . '" class="image-style"></img></a>';
                            }else{
                                echo '<a href="' . $this->layout()->staticBaseUrl . "pages/activities" . '"><img src="' . $this->selectFooterLogo . '" class="image-style"></img></a>';
                            }
                      } else {
                      $title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
                      $route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
                      echo '<h2>' . $this->htmlLink($route, $title) . '</h2>';
                      }
                      }
                      ?>
                      <div class="address_bar_section_inner">
                          <?php
                  if( $this->verticalfooterLendingBlockValue ):
                          echo '<p>' . $this->verticalfooterLendingBlockValue . '</p>';
                          else:
                          echo '<p>' . $this->translate('Be part of our social community, share your experiences with others and make the community an amazing place with your presence.') . '</p>';
                          endif;
                          ?>
                      </div>
                  </div>
          </ul>
      </div>
      <?php endif; ?>

		<?php if($this->templates == 2): ?>
    <div class="main_container">
      <ul class="versatile_check_assign">
        <li>
          <div class="versatile_contact_info">

            <?php
              if( $this->showFooterLogo ) {
                if( $this->selectFooterLogo ) {
                  echo '<a href="' . $this->url(array('action' => 'home'), "user_general", true) . '"><img src="' . $this->selectFooterLogo . '"></img></a>';
                } else {
                  $title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
                  $route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
                  echo '<h2>' . $this->htmlLink($route, $title) . '</h2>';
                }
              }
            ?>

            <div class="Address_bar_section">
              <div class="address_bar_section_inner">
                              <?php
                  if( $this->verticalfooterLendingBlockValue ):
                    echo '<p>' . $this->verticalfooterLendingBlockValue . '</p>';
                  else:
                    echo '<p>' . $this->translate('Be part of our social community, share your experiences with others and make the community an amazing place with your presence.') . '</p>';
                  endif; 
              ?>
              </div>

              <div class="address_bar_section_inner">
                     <?php if( !empty($this->mobile) ): ?>
                    <p><i class="fa fa-phone"></i><?php echo $this->mobile; ?></p>
                <?php endif; ?>
              </div>

              <div class="address_bar_section_inner">
                              <?php if( !empty($this->mail) ): ?>
                    <p><i class="fa fa-envelope-o"></i><?php echo $this->mail; ?></p>
                <?php endif; ?>
              </div>

              <div class="address_bar_section_inner">
                             <?php if( !empty($this->website) ): ?>
                    <a href="<?php echo $this->website; ?>" target="_blank">  <i class="fa fa-globe"></i><?php echo $this->website; ?></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </li>
        <?php $count = 0;
        foreach( $this->navigation as $navigation ) : ?>
          <?php if( $navigation->uri == 'javascript:void(0)' ) : ?>
            <?php if( !empty($navigation->icon) ) : ?>
              <li class="versatile_contact_info" id="sitecoretheme_footer_block_<?php echo $count ?>" style="background-image:url(\<?php echo $navigation->icon ?>\); background-repeat:no-repeat;">
                <ul>
            <?php else: ?>
            <?php if($count>0): ?>
              </li>
              </ul>
            <?php endif; ?>
              <li class="versatile_contact_info" id="sitecoretheme_footer_block_<?php echo $count ?>"><ul><li class="sitecoretheme_footer_block_head"><?php echo $this->translate($navigation->getLabel()) ?></li>
            <?php endif; ?>
            <?php $count++; ?>
          <?php else: ?>
            <?php
            if( isset($navigation->target) ) : ?>
              <li><a href="<?php echo $navigation->getHref() ?>" target="<?php echo $navigation->target ?>"><?php echo $this->translate($navigation->getLabel()); ?></a></li>
            <?php else: ?>
              <li><a href="<?php echo $navigation->getHref() ?>"><?php echo $this->translate($navigation->getLabel()); ?></a></li>
            <?php endif; ?>
          <?php endif; ?> 
        <?php endforeach; ?>
					</ul>
        </li>
				<?php if($this->verticalTwitterFeed && $this->twitterCode ) : ?>
        <li class="versatile_contact_info_twitter">
          <div>
              <?php echo $this->twitterCode ?>
          </div>
        </li>
				<?php elseif( $this->settings('sitecoretheme.fotter.content.item', 'user')): ?>					
					<?php								
					$footerContent = $this->content()->renderWidget("sitecoretheme.landing-page-listing", array('itemType' => $this->settings('sitecoretheme.fotter.content.item', 'user'), 'sortBy' => $this->settings('sitecoretheme.fotter.content.sort', 'creation_date'), 'limit' => $this->settings('sitecoretheme.fotter.content.limit', 9), 'crousalView' => false, 'widget_id' => 'footer_section_content_listing', 'showInfo' => $this->settings('sitecoretheme.fotter.content.viewType', 'grid')=='list', 'viewType' => $this->settings('sitecoretheme.fotter.content.viewType', 'grid'))); ?>
						<?php if (strlen($footerContent)> 50): ?>
							<li class="sitecoretheme_footer_block_head">
								<?php if ($this->settings('sitecoretheme.fotter.content.heading', 'Latest Registered Members')): ?>
									<h2><?php echo $this->translate($this->settings('sitecoretheme.fotter.content.heading', 'Latest Registered Members')); ?></h2>
								<?php endif; ?>
								<?php echo $footerContent; ?>
							</li>
						<?php endif; ?>
				<?php endif; ?>				
      </ul>
	  <?php if ($this->settings('sitecoretheme.fotter.subscribeus', 1)): ?>

			<div class="newsletter_subscribe">
				<div class="sitecoretheme_footer_bottom_block_inner">      
					<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitenewsletter')): ?>
						<?php $content = $this->content()->renderWidget("sitenewsletter.newsletter-subscribe", array('title' => "Subscribe Us")); ?>
						<?php if (trim(strip_tags($content))): ?>
							<h5><?php echo $this->translate("Subscribe Us") ?></h5>
							<?php echo $content; ?>
						<?php endif; ?>
					<?php else: ?>
						<h5><?php echo $this->translate("Subscribe Us") ?></h5>
						<form>
							<input type="text" placeholder="<?php echo $this->translate("Your Email") ?>" id="subscriber_email" value="<?php echo $this->viewerEmail; ?>">
								<button id="subscribe_button"><?php echo $this->translate("Subscribe") ?></button>
								<div class="subscribe_msg" id="subscribe_msg"></div>
						</form>
					<?php endif; ?>
				</div>
			</div> 
		
		<?php endif; ?>
    </div>
		<?php endif; ?>
		<?php if($this->templates == 3): ?>
    <div class="main_container">
      <ul class="versatile_check_assign">
        <li>
          <div class="versatile_contact_info">

            <?php
              if( $this->showFooterLogo ) {
                if( $this->selectFooterLogo ) {
                  echo '<a href="' . $this->url(array('action' => 'home'), "user_general", true) . '"><img src="' . $this->selectFooterLogo . '"></img></a>';
                } else {
                  $title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
                  $route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
                  echo '<h2>' . $this->htmlLink($route, $title) . '</h2>';
                }
              }
            ?>
	<?php if ($this->settings('sitecoretheme.fotter.subscribeus', 1)): ?>

			<div class="newsletter_subscribe">
				<div class="sitecoretheme_footer_bottom_block_inner">      
					<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitenewsletter')): ?>
						<?php $content = $this->content()->renderWidget("sitenewsletter.newsletter-subscribe", array('title' => "Subscribe Us")); ?>
						<?php if (trim(strip_tags($content))): ?>
							<h5><?php echo $this->translate("Subscribe Us") ?></h5>
							<?php echo $content; ?>
						<?php endif; ?>
					<?php else: ?>
						<h5><?php echo $this->translate("Subscribe Us") ?></h5>
						<form>
							<input type="text" placeholder="<?php echo $this->translate("Your Email") ?>" id="subscriber_email" value="<?php echo $this->viewerEmail; ?>">
								<button id="subscribe_button"><?php echo $this->translate("Subscribe") ?></button>
								<div class="subscribe_msg" id="subscribe_msg"></div>
						</form>
					<?php endif; ?>
				</div>
			</div> 
		
		<?php endif; ?>
        </li>
					<?php $count = 0; $maxAllow = 6;
					foreach ($this->navigation as $navigation) :
						if($navigation->uri == 'javascript:void(0)' && strpos($navigation->class, 'sitecoretheme_footer_') !==false&&strpos($navigation->class, '_column') !==false){
							continue;
						}
						if($count >= $maxAllow):
							break;
						endif;
						?>
						<?php $count++?>
						<?php if (isset($navigation->target)) : ?>
	
							<!--<li><a href="<?php echo $navigation->getHref() ?>" target="<?php echo $navigation->target ?>"><?php echo $this->translate($navigation->getLabel()); ?></a></li>-->
		  <li><a href="<?php echo $navigation->getHref() ?>" target="<?php echo $navigation->target ?>"><i class="fa fa-flag"></i></a><span><?php echo $this->translate($navigation->getLabel()); ?></span></li>
						<?php else: ?>
							<li><a href="<?php echo $navigation->getHref() ?>"><?php echo $this->translate($navigation->getLabel()); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
      </ul>
    </div>
		<?php endif; ?>
	<?php if($this->templates == 1): ?>
		<?php if ($this->settings('sitecoretheme.fotter.subscribeus', 1)): ?>
<div class="main_container">
			<div class="newsletter_subscribe">
				<div class="sitecoretheme_footer_bottom_block_inner">      
					<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitenewsletter')): ?>
						<?php $content = $this->content()->renderWidget("sitenewsletter.newsletter-subscribe", array('title' => "Subscribe Us")); ?>
						<?php if (trim(strip_tags($content))): ?>
							<h5><?php echo $this->translate("Subscribe Us") ?></h5>
							<?php echo $content; ?>
						<?php endif; ?>
					<?php else: ?>
						<h5><?php echo $this->translate("Subscribe Us") ?></h5>
						<form>
							<input type="text" placeholder="<?php echo $this->translate("Your Email") ?>" id="subscriber_email" value="<?php echo $this->viewerEmail; ?>">
								<button id="subscribe_button"><?php echo $this->translate("Subscribe") ?></button>
								<div class="subscribe_msg" id="subscribe_msg"></div>
						</form>
					<?php endif; ?>
				</div>
			</div>
	</div>
		<?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<div class="vertical_bottom_footer">
      <div class="main_container">
        <div class="vertical_bottom_footer_content">
          <?php if( !empty($this->social_links_array[0]) && ( !empty($this->facebook_url) || !empty($this->twitter_url) ||  !empty($this->linkedin_url) || !empty($this->youtube_url) || !empty($this->pinterest_url) )): ?>

            <ul class="social_bottom_icons">
              <?php if( !empty($this->facebook_url) ) : ?>
                      <li><a href="<?php echo $this->facebook_url ?>" target="_blank" title="<?php echo $this->facebook_title ?>"><i class="fa fa-facebook"></i></a></li>
                  <?php endif; ?>
              <?php if( !empty($this->twitter_url) ) : ?>
                      <li><a href="<?php echo $this->twitter_url ?>" target="_blank" title="<?php echo $this->twitter_title ?>">
                        <i class="fa fa-twitter"></i>
                      </a></li>
                  <?php endif; ?>
              <?php if( !empty($this->linkedin_url) ) : ?>
                      <li><a href="<?php echo $this->linkedin_url ?>" target="_blank" title="<?php echo $this->linkedin_title ?>" >
                        <i class="fa fa-linkedin"></i>
                      </a></li>
                  <?php endif; ?>
              <?php if( !empty($this->youtube_url) ) : ?>
                      <li><a href="<?php echo $this->youtube_url ?>" target="_blank" title="<?php echo $this->youtube_title ?>"><i class="fa fa-youtube"></i></a></li>
                  <?php endif; ?>
              <?php if( !empty($this->pinterest_url) ) : ?>
                      <li><a href="<?php echo $this->pinterest_url ?>" target="_blank" title="<?php echo $this->pinterest_title ?>"><i class="fa fa-pinterest"></i></a></li>
                  <?php endif; ?>
            </ul>
          <?php endif; ?>

          <div class="copy_right_text"><?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
          <?php foreach( $this->navigation_menus as $item ):
            $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
              'reset_params', 'route', 'module', 'controller', 'action', 'type',
              'visible', 'label', 'href'
            )));
            ?>
            &nbsp;-&nbsp; <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
          <?php endforeach; ?>

          <?php if(  count($this->languageNameList) > 1): ?>
              &nbsp;-&nbsp;
              <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
                <?php $selectedLanguage = $this->translate()->getLocale() ?>
                <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
                <?php echo $this->formHidden('return', $this->url()) ?>
              </form>
          <?php endif; ?>
          <?php if( !empty($this->affiliateCode) ): ?>
            <div class="affiliate_banner">
              <?php 
                echo $this->translate('Powered by %1$s', 
                  $this->htmlLink('http://www.socialengine.com/?source=v4&aff=' . urlencode($this->affiliateCode), 
                  $this->translate('SocialEngine Community Software'),
                  array('target' => '_blank')))
              ?>
            </div>
          <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
<?php if($this->settings('sitecoretheme.fotter.subscribeus', 1) && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitenewsletter')): ?>
<script type="text/javascript">
  $('subscribe_button').addEvent('click', function (event) {
    event.preventDefault();
    $('subscribe_msg').innerHTML = '';
    var email = $('subscriber_email').get('value');
    var reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if( !reg.test(String(email).toLowerCase()) ) {
      $('subscribe_msg').setStyle('color', '#ff0000');
      $('subscribe_msg').innerHTML = 'Please enter valid email';
      return;
    }
    en4.core.request.send(new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'index', 'controller' => 'subscription', 'module' => 'sitecoretheme'), 'default');?>',
      data : {
        format : 'json',
        email : $('subscriber_email').get('value')
      },
      onSuccess : function(data) {
        if( data['resp'] ) {
          $('subscribe_msg').setStyle('color', '#179617');
        } else {
          $('subscribe_msg').setStyle('color', '#ff0000');
        }

        $('subscribe_msg').innerHTML = data['msg'];
      }
    }));
    });
</script>
<?php endif; ?>
<style>
    .address_bar_section_inner > a {
        color: unset !important;
    }
    .copy_right_text>a {
        color: #ccc !important;
    }
</style>