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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php if (count($this->quickNavigation)) { ?>
    <div class='quicklinks'>
    <?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
        <ul class='navigation'>
            <?php foreach ($this->quickNavigation as $nav): ?>
                <?php $data_smoothboxValue = ''; ?>
                <li <?php if ($nav->active): echo "class='active'";
                endif; ?>>
                    <?php if ($nav->action): ?>
                        <?php if (isset($nav->data_SmoothboxSEAOClass)): ?>
                            <?php $data_smoothboxValue = $nav->data_SmoothboxSEAOClass; ?>
                        <?php endif; ?>

                        <?php if (Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()): ?>
                            <a class="<?php echo $nav->class ?>" <?php if (isset($nav->data_SmoothboxSEAOClass)): ?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue; ?>" <?php endif; ?> href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php else: ?>

                            <?php
                            $smoothboxClass = @explode(' ', $nav->class);

                            if (in_array('seao_smoothbox', $smoothboxClass)) {
                                unset($smoothboxClass[0]);
                                $nav->class = implode(' ', $smoothboxClass);
                            }
                            ?>

                            <a class="<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if (Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()): ?>
                            <a class= "<?php echo $nav->class ?>" <?php if (isset($nav->data_SmoothboxSEAOClass)): ?> data-SmoothboxSEAOClass="<?php echo $data_smoothboxValue; ?>" <?php endif; ?> href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php else: ?>

                            <?php
                            $smoothboxClass = @explode(' ', $nav->class);

                            if (in_array('seao_smoothbox', $smoothboxClass)) {
                                unset($smoothboxClass[0]);
                                $nav->class = implode(' ', $smoothboxClass);
                            }
                            ?>
                            <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php } ?>