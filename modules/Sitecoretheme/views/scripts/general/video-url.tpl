<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: video-url.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js' ?>"></script>
<video controls preload="auto" autoplay="true"> 
  <source src="<?php echo $this->url ?>" type=video/mp4>
</video>

<style type="text/css">
  video{
    max-height: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0;
    width: 100%;
    box-sizing: border-box;
  }
  body {
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 0;
    box-sizing: border-box;
  }
</style>