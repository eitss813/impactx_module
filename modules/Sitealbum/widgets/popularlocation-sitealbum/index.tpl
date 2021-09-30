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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<script type="text/javascript">
    var locationAction = function(cityValue)
    {
        if ($("tag"))
            $("tag").value = '';
        var form;
        if ($('filter_form')) {
            form = document.getElementById('filter_form');
        } else if ($('filter_form_location')) {
            form = $('filter_form_location');
        }
        form.elements['location'].value = cityValue;

        form.submit();
    }
</script>

<ul class="sitealbum_popular_locations">
    <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), "sitealbum_general", true) ?>' style='display: none;'>
        <input type="hidden" id="location" name="location"  value=""/>
    </form>
    <?php foreach ($this->sitealbumLocation as $sitealbumLocation): ?>
        <?php if (!empty($sitealbumLocation->city) || !empty($sitealbumLocation->state)): ?>
            <li <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $sitealbumLocation->city || $this->searchLocation == $sitealbumLocation->state )): ?> style="font-weight: bold;" <?php endif; ?>>
                <span class="fright mleft5">
                    <?php
                    if (!empty($sitealbumLocation->city)): echo "" . $sitealbumLocation->count_location . "";
                    else: echo "" . $sitealbumLocation->count_location_state . "";
                    endif;
                    ?>
                </span>
                <span class="o_hidden">
                    <a href="javascript:void(0);" onclick="locationAction('<?php if (!empty($sitealbumLocation->city))
                        echo $sitealbumLocation->city;
                    else
                        echo $sitealbumLocation->state;
                    ?>')" ><?php echo ucfirst($sitealbumLocation->city) ?><?php
                    $state = null;
                    if (!empty($sitealbumLocation->city) && !empty($sitealbumLocation->state))
                        $state.=" [";$state.=ucfirst($sitealbumLocation->state);
                    if (!empty($sitealbumLocation->city) && !empty($sitealbumLocation->state))
                        $state.="] ";echo $state;
                    ?></a>
                </span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>