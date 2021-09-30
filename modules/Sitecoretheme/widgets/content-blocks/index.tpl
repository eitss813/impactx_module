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
 ?>
<div class="sitecoretheme_content-blocks_wrapper content-blocks_<?php echo $this->itemType ?> sitecoretheme_content-blocks_<?php echo $this->identity; ?>">
  <?php if( $this->title ): ?>
    <div class="_header">
      <h3><?php echo $this->translate($this->title); ?></h3>
      <div class="widgets_title_border">
        <span></span>
        <i></i>
        <span></span>
      </div>
      <?php if( $this->description ): ?>
        <div class="widgets_title_description">
          <?php echo $this->translate($this->description); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if( $this->viewType == 1 ): ?>
    <div class="_main _main_style_1">
      <ul class="">
        <?php foreach( $this->results as $row ) : ?>
          <li class="wow zoomIn animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
            <div class="_item">
              <div class="_item_img">
                <?php echo $this->htmlLink($row->getHref(), $this->itemBackgroundPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
              </div>
              <div class="_item_info">
                <div class="_item_title">
                  <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                </div>
                <div>
                  <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
                  <?php if( $this->categoryTable && !empty($row->category_id) ): ?>
                    <div class="_item_category">
                      <?php
                      echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                        'table' => $this->categoryTable,
                        'item' => $row
                      ));
                      ?>
                    </div>
                <?php endif; ?>
                </div>
                <div class="_item_body">
                  <?php echo $row->getDescription(); ?>
                </div>
              </div>
            </div>
            <?php if( $this->readMoreText ): ?>
              <div class="_readmore">
                <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif( $this->viewType == 4 ): ?>
    <div class="_main _main_style_4">
      <ul class="">
        <?php foreach( $this->results as $row ) : ?>
          <li class="wow zoomIn animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
            <div class="_item">
              <div class="_item_img">
                <?php echo $this->htmlLink($row->getHref(), $this->itemBackgroundPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
              </div>
              <div class="_item_info">
                <div class="_item_title">
                  <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                </div>
                <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
                <div class="_item_body">
                  <?php echo $row->getDescription(); ?>
                </div>
                <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                  <div class="_item_category">
                  <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                      'table' => $this->categoryTable,
                      'item' => $row
                  ));?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <?php if( $this->readMoreText ): ?>
              <div class="_readmore">
                <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif( $this->viewType == 6 ): ?>
    <div class="_main _main_style_6">
      <ul class="">
        <?php foreach( $this->results as $row ) : ?>
          <li class="wow zoomIn animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
            <div class="_item">
              <div class="_item_img">
               <?php if (isset($row->member_count)): ?> 
                  <div class="_counter">                
                    <div>
                      <small><?php echo $this->translate('Members') ?></small>                
                      <span><?php echo $row->member_count?></span>
                    </div>
                  </div>
                <?php endif; ?>
                <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
              </div>
              <div class="_item_info">
                <div class="_item_title">
                  <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                </div>                
                <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                  <div class="_item_category">
                  <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                      'table' => $this->categoryTable,
                      'item' => $row
                  ));?>
                  </div>
                <?php endif; ?>
                <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
                <div class="_item_body">
                  <?php echo $row->getDescription(); ?>
                </div>
                <?php if( $this->readMoreText ): ?>
                  <div class="_readmore">
                    <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
	  <?php elseif( $this->viewType == 8 || $this->viewType == 9 ): ?>
    <div class="sitecoretheme_content-blocks_wrapper_inner">
      <div class="_main _main_style_two_blocks_listing _main_style_<?php echo $this->viewType ?>">
  			<?php if($this->viewType ==8): ?>
  			<div class="_primary_list">
  				<?php foreach( $this->results as $row ) : ?>
  				<div class="wow slideInUp animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
  					  <div class="_item">
                <div class="_item_img">
                 <?php if (isset($row->member_count)): ?> 
                    <div class="_counter">                
                      <div>
                        <small><?php echo $this->translate('Members') ?></small>                
                        <span><?php echo $row->member_count?></span>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
                </div>
                <div class="_item_info">
                  <div class="_item_title">
                    <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                  </div>                
                  <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                    <div class="_item_category">
                    <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                        'table' => $this->categoryTable,
                        'item' => $row
                    ));?>
                    </div>
                  <?php endif; ?>
                  <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
                  <div class="_item_body">
                    <?php echo $row->getDescription(); ?>
                  </div>
                  <?php if( $this->readMoreText ): ?>
                    <div class="_readmore">
                      <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
  				</div>
  				<?php break;?>
  				<?php	endforeach; ?>
  			</div>
  			<?php endif; ?>
  			<div class="_sub_list">
        <ul class="">
  				<?php ?>
          <?php foreach( $this->results as $row ) : ?>
            <li class="wow slideInUp animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
              <div class="_item">
                <div class="_item_img">
                 <?php if (isset($row->member_count)): ?> 
                    <div class="_counter">                
                      <div>
                        <small><?php echo $this->translate('Members') ?></small>                
                        <span><?php echo $row->member_count?></span>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
                </div>
                <div class="_item_info">
                  <div class="_item_title">
                    <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                  </div>                
                  <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                           <div class="_item_category">
             <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                        'table' => $this->categoryTable,
                        'item' => $row
                    ));?>
                    </div>
                  <?php endif; ?>
                  <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
              <!--    <div class="_item_body">
                    <?php echo $row->getDescription(); ?>
                  </div> -->
                <!--  <?php if( $this->readMoreText ): ?>
                    <div class="_readmore">
                      <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
                    </div>
                  <?php endif; ?>-->
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
  			</div>
  			<?php if($this->viewType ==9): ?>
  			<div class="_primary_list">
  				<?php foreach( $this->results as $row ) : ?>
  				<div class="wow slideInUp animated" <?php if( !$this->readMoreText ): ?> style="padding-bottom:0" <?php endif; ?>>
  					  <div class="_item">
                <div class="_item_img">
                 <?php if (isset($row->member_count)): ?> 
                    <div class="_counter">                
                      <div>
                        <small><?php echo $this->translate('Members') ?></small>                
                        <span><?php echo $row->member_count?></span>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
                </div>
                <div class="_item_info">
                  <div class="_item_title">
                    <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
                  </div>                
                  <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                    <div class="_item_category">
                    <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                        'table' => $this->categoryTable,
                        'item' => $row
                    ));?>
                    </div>
                  <?php endif; ?>
                  <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
                  <div class="_item_body">
                    <?php echo $row->getDescription(); ?>
                  </div>
                  <?php if( $this->readMoreText ): ?>
                    <div class="_readmore">
                      <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
  				</div>
  				<?php break;?>
  				<?php	endforeach; ?>
  			</div>
  			<?php endif; ?>
      </div>
    </div>
  <?php elseif( $this->viewType == 2 ): ?>
    <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/owl.carousel/owl.carousel.min.css'); ?>
    <?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');

    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/owl.carousel.min.js');
    ?>

    <div class="_main _main_style_2">
      <div class="_items owl-carousel owl-carousel_<?php echo $this->identity; ?>">
        <?php foreach( $this->results as $row ) : ?>
          <div class="_item_list">
            <a href="<?php echo $row->getHref() ?>">
              <div class="_item_img">
                <?php echo str_replace('src=', 'data-src=', $this->itemPhoto($row, 'thumb.main', $row->getTitle(), array('class' => 'owl-lazy', 'nolazy' => true))) ?>
              </div>
              <div class="_item_info">
                <span class="_item_title">
                  <?php echo $row->getTitle() ?>
                </span>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="owl-nav _navigation"></div>
    </div>
    <style type="text/css">
      .generic_layout_container.layout_sitecoretheme_content_blocks.owl-carousel_blocks_<?php echo $this->identity; ?> {
        padding-left: 0;
        padding-right: 0;
      }      
    </style>
    <script type="text/javascript">
      $$('.owl-carousel_<?php echo $this->identity; ?>')
        .getParent('.layout_sitecoretheme_content_blocks')
        .addClass('owl-carousel_blocks_<?php echo $this->identity; ?>');
      jQuery(document).ready(function () {
        jQuery('.owl-carousel_<?php echo $this->identity; ?>').owlCarousel({
          items: 3,
          loop: true,
          nav: true,
          center: true,
          //        margin: 10,
          responsiveClass: true,
          lazyLoad: true,
          dots: false,
          navContainer: jQuery('.owl-carousel_blocks_<?php echo $this->identity; ?>').find('._navigation'),
          navText: [
            '',
            ''
          ],
          navContainerClass: 'owl-nav _navigation',
          navClass: [
            'owl-prev _prev',
            'owl-next _next'
          ],
          responsive: {
            // breakpoint from 0 up
            0: {
              items: 1,
              center: false,
              nav: false
            },
            // breakpoint from 480 up
            480: {
              items: 2,
              center: false
            },
            // breakpoint from 768 up
            768: {
              items: 3,
              center: true,
              nav: true,
            }
          }
        });
      });
    </script>
  <?php elseif( $this->viewType == 5 ): ?>
    <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/owl.carousel/owl.carousel.min.css'); ?>
    <?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');

    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/owl.carousel.min.js');
    ?>

    <div class="_main _main_style_5">
      <div class="_items owl-carousel owl-carousel_<?php echo $this->identity; ?>">
        <?php foreach( $this->results as $row ) : ?>
          <div class="_item_list">
            <a href="<?php echo $row->getHref() ?>">
              <div class="_item_img">
                <?php echo str_replace('src=', 'data-src=', $this->itemPhoto($row, 'thumb.main', $row->getTitle(), array('class' => 'owl-lazy', 'nolazy' => true))) ?>
              </div>
              <div class="_item_info">                
                <span class="_item_title">
                  <?php echo $row->getTitle() ?>
                </span>
                <span class="_item_body">
                  <?php echo $row->getDescription(); ?>
                </span>                
              </div>
              <div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="owl-nav _navigation"></div>
    </div>
    <style type="text/css">
      .generic_layout_container.layout_sitecoretheme_content_blocks.owl-carousel_blocks_<?php echo $this->identity; ?> {
        padding-left: 0;
        padding-right: 0;
      }      
    </style>
    <script type="text/javascript">
      $$('.owl-carousel_<?php echo $this->identity; ?>')
        .getParent('.layout_sitecoretheme_content_blocks')
        .addClass('owl-carousel_blocks_<?php echo $this->identity; ?>');
      jQuery(document).ready(function () {
        jQuery('.owl-carousel_<?php echo $this->identity; ?>').owlCarousel({
          items: 2,
          loop: false,
          nav: true,
          center: false,
          margin: 30,
          autoWidth: false,
          responsiveClass: true,
          lazyLoad: true,
          dots: false,
          navContainer: jQuery('.owl-carousel_blocks_<?php echo $this->identity; ?>').find('._navigation'),
          navText: [
            '',
            ''
          ],
          navContainerClass: 'owl-nav _navigation',
          navClass: [
            'owl-prev _prev',
            'owl-next _next'
          ],
          responsive: {
            // breakpoint from 0 up
            0: {
              items: 1,
              center: false,
              nav: false
            },
            // breakpoint from 480 up
            768: {
              items: 2,
              center: false
            },
          }
        });
      });
    </script>
  <?php elseif( $this->viewType == 3 ): ?>
    <div class="_main _main_style_3">
      <ul class="_items">
        <?php foreach( $this->results as $row ) : ?>
          <li class="_item wow zoomIn animated">
            <div class="_item_img">
              <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
            </div>
            <div class="_item_info">
              <div class="_item_title">
                <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
              </div>
            </div>
            <?php if( $this->readMoreText ): ?>
              <div class="_readmore">
                <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif( $this->viewType == 7 ): ?>
    <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/owl.carousel/owl.carousel.min.css'); ?>
    <?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');

    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/owl.carousel.min.js');
    ?>
    <div class="_main _main_style_7">
      <div class="_items owl-carousel owl-carousel_<?php echo $this->identity; ?>">
        <?php foreach( $this->results as $row ) : ?>
          <div class="_item wow zoomIn animated">
            <div class="_item_img">
              <?php echo $this->htmlLink($row->getHref(), $this->itemPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
            </div>
            <div class="_item_info">
              <div class="_item_title">
                <?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
              </div>
              <div class="_item_body">
                <?php echo $row->getDescription(); ?>
              </div>
            </div>
            <?php if( $this->categoryTable && !empty($row->category_id)): ?>
                  <div class="_item_category">
                  <?php echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
                      'table' => $this->categoryTable,
                      'item' => $row
                  ));?>
                  </div>
            <?php endif; ?>
            
            <?php if( $this->readMoreText ): ?>
              <div class="_readmore">
                <?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
       <div class="owl-nav _navigation"></div>
    </div>
    <style type="text/css">
      .generic_layout_container.layout_sitecoretheme_content_blocks.owl-carousel_blocks_<?php echo $this->identity; ?> {
        padding-left: 0;
        padding-right: 0;
      }      
    </style>
    <script type="text/javascript">
      $$('.owl-carousel_<?php echo $this->identity; ?>')
        .getParent('.layout_sitecoretheme_content_blocks')
        .addClass('owl-carousel_blocks_<?php echo $this->identity; ?>');
      jQuery(document).ready(function () {
        jQuery('.owl-carousel_<?php echo $this->identity; ?>').owlCarousel({
          loop: true,
          nav: true,
          center: false,
          margin: 0,
          autoWidth: false,
          responsiveClass: true,
          lazyLoad: true,
          dots: false,
//          autoplay:true,
//          autoplayTimeout:1000,
//          autoplayHoverPause:true,
          navContainer: jQuery('.owl-carousel_blocks_<?php echo $this->identity; ?>').find('._navigation'),
          navText: [
            '',
            ''
          ],
          navContainerClass: 'owl-nav _navigation',
          navClass: [
            'owl-prev _prev',
            'owl-next _next'
          ],
          responsive: {
            // breakpoint from 0 up
            0: {
              items: 1,
              center: false
            },
            480: {
              items: 2,
              slideBy:2
            },
            // breakpoint from 480 up
            768: {
              items: 3,
              slideBy: 3
            },
          }
        });
      });
    </script>
  <?php elseif( $this->viewType == 10 ): ?>
    <div class="_main _main_style_10">
      <ul class="round_listings sitecoretheme_container">
        <?php foreach( $this->results as $row ) : ?>
					<?php $iconCount = (int)isset($row->like_count)+(int)isset($row->comment_count)+(int)isset($row->view_count)+(int)isset($row->member_count); ?>
				<li class="listing_item wow zoomIn animated _icon_style_<?php echo $iconCount ?>">
					<article>
						<a href="<?php echo $row->getHref() ?>" class="listing_item_thumb">
							<div class="listing_item_thumb_img">
								<?php echo $this->itemBackgroundPhoto($row, 'thumb.main') ?>
							</div>
							<div class="listing_item_thumb_stats">
								<?php if (isset($row->like_count)): ?>
									<p class="list_likes">
										<i class="fa fa-thumbs-up"></i>
										<span><?php echo $row->like_count; ?></span>
									</p>
								<?php endif; ?>
								<?php if (isset($row->comment_count)): ?>
									<p class="list_comments">
										<i class="fa fa-comments"></i>
										<span><?php echo $row->comment_count; ?></span>
									</p>
								<?php endif; ?>
								<?php if (isset($row->view_count)): ?>
									<p class="list_views">
										<i class="fa fa-eye"></i>
										<span><?php echo $row->view_count; ?></span>
									</p>
								<?php endif; ?>
								<?php if (isset($row->member_count)): ?>
									<p class="list_members">
										<i class="fa fa-user"></i>
										<span><?php echo $row->member_count; ?></span>
									</p>
								<?php endif; ?>
							</div>
							<div class="listing_item_thumb_title">
								<span><?php echo $row->getTitle(); ?></span>
							</div>    </a>
						<!--            <?php if ($this->readMoreText): ?>
														<div class="_readmore">
							<?php echo $this->htmlLink($row->getHref(), $this->translate($this->readMoreText)) ?>
														</div>
						<?php endif; ?>-->

						<div class="listing_item_info">
							<p><?php echo $row->getDescription(); ?> </p>
						</div>

					</article>
				</li>
        <?php endforeach; ?>
      </ul>
    </div>
	<?php endif; ?>
</div>
<script type="text/javascript">
  $$('.sitecoretheme_content-blocks_<?php echo $this->identity; ?>')
    .getParent('.layout_sitecoretheme_content_blocks')
    .addClass('layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?> layout_sitecoretheme_content_blocks_view_type_<?php echo $this->viewType; ?>');
</script>

	<style type="text/css">
		<?php if($this->backgroundImage): ?>	
		.layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?> {
			background-image:url(<?php echo $this->layout()->staticBaseUrl.$this->backgroundImage ?>);
		}
		<?php endif; ?>
		<?php if($this->headingColor): ?>	
		.layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?> h3,
		.layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?> ._header h3,
		.layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?> ._header .widgets_title_description {
			color: <?php echo $this->headingColor ; ?>
		}
		<?php endif; ?>
		<?php if($this->backgroundOverlayColor): ?>	
		.layout_sitecoretheme_content_blocks_<?php echo $this->identity; ?>::before {
			background-color: <?php echo $this->backgroundOverlayColor ?>;
			opacity: <?php echo $this->backgroundOverlayOpacity/100 ?>;
		}
		<?php endif; ?>
	</style>