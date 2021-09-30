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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/image_rotate.css');
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
<script type="text/javascript">
    window.addEvent('domready', function () {
        durationOfRotateImage = <?php echo!empty($this->defaultDuration) ? $this->defaultDuration : 500; ?>;
        //image_rotate();
				 var slideshowDivObj = $('slide-images');
    var imagesObj = $('slide-images').getElements('.slideblok_image');
    var indexOfRotation = 0;

    imagesObj.each(function(img, i){
      if(i > 0) {
        img.set('opacity',0);
      }
    });    

    var show = function() {
			if(imagesObj.length > 0) {
      imagesObj[indexOfRotation].fade('out');
      indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation+1 : 0;
      imagesObj[indexOfRotation].fade('in');
			}
    };
    
    window.addEvent('load',function(){
      show.periodical(durationOfRotateImage);
    });
		
		
		<?php if($this->contentFullWidth):?>
			if ($$('.layout_sitealbum_featured_photos').length > 0) {
          var globalContentElement = en4.seaocore.getDomElements('content');
					var globalContentWidth = $(globalContentElement).getWidth();
						
						 $(globalContentElement).setStyles({
							'width': '100%',
							'margin-top': '-20px'
						 });
			}
		<?php endif;?>
    });
		
		
</script>

<style type="text/css">
	#slide-images{
			width: <?php echo!empty($this->slideWidth) ? $this->slideWidth . 'px;' : '100%'; ?>;
			height: <?php echo $this->slideHeight . 'px !important'; ?>;
			overflow: hidden;
	}
	.slideblok_image img{
			min-height: <?php echo $this->slideHeight . 'px !important'; ?>;
			height: auto;
	}
	.layout_sitealbum_featured_photos .featuredimage-text {
			height: <?php echo $this->slideHeight . 'px !important'; ?>;
	}
</style>

<div id="slide-images" class="slideblock">
    <?php
    foreach ($this->paginator as $paginator):
        ?>
        <div class="slideblok_image">
            <?php echo $this->itemPhoto($paginator, 'thumb.main', $paginator->getTitle(), array());
            ?> 
                        
            <?php $album = Engine_Api::_()->getItem('album', $paginator->album_id); ?>
            <a href='<?php echo $paginator->getHref(); ?>'>
                <?php if (!empty($paginator->title)): ?>
                    <?php echo $this->translate('%1$s by %2$s', $paginator->getTitle(), $album->getOwner()->getTitle()) ?>
                <?php else: ?>
                    <?php echo $this->translate('by %1$s', $album->getOwner()->getTitle()) ?>
                <?php endif; ?>
            </a>
        </div>
        <?php
    endforeach;
    ?>
    <section class="featuredimage-text">
        <div>
            <?php if ($this->featuredPhotosHtmlTitle): ?>
                <h1><?php echo $this->featuredPhotosHtmlTitle; ?></h1>
            <?php endif; ?>
            <?php if ($this->featuredPhotosHtmlDescription): ?>
                <article><?php echo $this->featuredPhotosHtmlDescription; ?></article>
            <?php endif; ?>
                <?php if ($this->featuredPhotosSearchBox): ?> 
                <div class="featuredimage_search">                     
                 <?php echo $this->content()->renderWidget("sitealbum.searchbox-sitealbum", array('formElements' => array("textElement"), 'textWidth' => 500));
                    ?>
                </div>
            <?php endif; ?>
          <div class="txt_center featuredimage-album-counts">
            <span><?php echo $this->translate(array('<i>%s</i> photo', '<i>%s</i> photos', $this->photos_count), $this->locale()->toNumber($this->photos_count)); ?></span>
            <span><?php echo $this->translate(array('<i>%s</i> album', '<i>%s</i> albums', $this->albums_count), $this->locale()->toNumber($this->albums_count)); ?></span>
          </div>
        </div>
    </section>  
</div>

<?php if ($this->featuredAlbums): ?>
    <div class="sitealbum_featured_photos_albums">
        <ul class="thumbs">
            <?php foreach ($this->albumpaginator as $item): ?>
                <li>
                    <?php if ($item->photo_id): ?>
                        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" >
                          <?php 
                          echo $this->itemBackgroundPhoto($item, 'thumb.main', $item->getTitle(), array());
                          ?>
                      
                        </a>
                    <?php else: ?>
                        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" >   
                          <?php 
                          echo $this->itemBackgroundPhoto($item,'thumb.normal', $item->getTitle(), array());
                          ?>
                        </a>
                    <?php endif; ?>
                    <div class="thumbs_info mtop5">
                        <?php //if (in_array('albumTitle', $this->albumInfo)): ?>
                        <span class="thumbs_title">
                        		<span>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 50))); ?>
                                                    <span class="mtop5">
                            <?php
                            echo $this->translate('By %1$s', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()));
                            ?> 
                            </span>
                        </span>
                        </span>

                    </div>  
                </li>
                
            <?php endforeach; ?>
                <?php 
                for($i=$this->albumpaginator->getTotalItemCount(); $i <3; $i++){
                    echo '<li></li>';
                }
                ?>
        </ul>
    </div>
<?php endif; ?>