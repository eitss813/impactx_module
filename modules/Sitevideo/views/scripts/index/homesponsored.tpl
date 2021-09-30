<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php if ($this->direction == 1) { ?>
    <?php $j = 0; ?>
    <?php foreach ($this->sitevideos as $sitevideo): ?>
        <?php
        echo $this->partial(
                'list_carousel.tpl', 'sitevideo', array(
            'sitevideo' => $sitevideo,
            'vertical' => $this->vertical,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'videoOption' => $this->videoOption,
        ));
        ?>	 
    <?php endforeach; ?>
    <?php if ($j < ($this->sponserdSitevideosCount)): ?>
        <?php for ($j; $j < ($this->sponserdSitevideosCount); $j++): ?>
            <li class="sitevideo_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
            </li>
        <?php endfor; ?>
    <?php endif; ?>
<?php } else { ?>

    <?php for ($i = $this->sponserdSitevideosCount; $i < Count($this->sitevideos); $i++): ?>
        <?php $sitevideo = $this->sitevideos[$i]; ?>
        <?php
        echo $this->partial(
            'list_carousel.tpl', 'sitevideo', array(
            'sitevideo' => $sitevideo,
            'vertical' => $this->vertical,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'videoOption' => $this->videoOption,
        ));
        ?>	
    <?php endfor; ?>

    <?php for ($i = 0; $i < $this->sponserdSitevideosCount; $i++): ?>
        <?php $sitevideo = $this->sitevideos[$i]; ?>
        <?php
        echo $this->partial(
                'list_carousel.tpl', 'sitevideo', array(
            'sitevideo' => $sitevideo,
            'vertical' => $this->vertical,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'videoOption' => $this->videoOption,
        ));
        ?>	
    <?php endfor; ?>
<?php } ?>

