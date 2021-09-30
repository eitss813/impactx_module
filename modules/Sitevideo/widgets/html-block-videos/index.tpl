<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

if ($this->sitevideoLendingBlockValue):
    echo '<div id="show_help_content">' . $this->sitevideoLendingBlockValue . '</div>';
else:
    ?>
    <?php

    echo '<div style="width: 75%;margin: 0 auto;"><p style="text-align: center;line-height: 55px;"><span style="font-size: 30pt;"><strong>Upload, watch and share videos on your site</strong></span></p><p style="text-align: center;"><span style="font-size: 16pt;line-height: 22pt;">Post and share videos with your community members, friends, or with anyone, on computers, phones and tablets. <a href="videos/browse"><strong>See all our videos &raquo;</strong></a></span></p></div>';
endif;
?>