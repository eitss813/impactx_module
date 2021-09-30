<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Settings.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Api_Shorturl extends Core_Api_Abstract
{

    public function generateShorturl(Core_Model_Item_Abstract $resource,$url)
    {


        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if($url) {
             $actual_link  = $url;
            // check if shorturl is already present before creating
            $isShorturlPresent = Engine_Api::_()->getDbTable('shorturls', 'core')->isShorturlPresent($resource);

        }else {
            // Get Current link
            $actual_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $resource->getHref();
            // check if shorturl is already present before creating
            $isShorturlPresent = Engine_Api::_()->getDbTable('shorturls', 'core')->isShorturlPresent($resource);

        }

        //get title
        $title = $resource->getTitle();

        // if not present create and send it
        if (empty($isShorturlPresent)) {

            // generate url using api.rebrandly.com
            $domain_data["fullName"] = "impx.org";
            $post_data["destination"] = $actual_link;
            $post_data["domain"] = $domain_data;
            $post_data["title"] = $title;
            $ch = curl_init("https://api.rebrandly.com/v1/links");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "apikey: 3443bf462bc244c8b1e1fdd53bcfd8d0",
                "Content-Type: application/json",
                "workspace: 7e539566f8bd49b39d8156de5583fa48"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            $result = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result, true);
            $generatedShortUrl = $response["shortUrl"];


            if($url) {
                $resourceType = 'sitepage_initiative';
                // once generated add them to db for future use
                $add_shorturl = Engine_Api::_()->getDbTable('shorturls', 'core')->addShorturl($resource,$generatedShortUrl);

            }else {
                // once generated add them to db for future use
                $add_shorturl = Engine_Api::_()->getDbTable('shorturls', 'core')->addShorturl($resource, $generatedShortUrl);

            }

            return $generatedShortUrl;
        } else {

            // if present pass that only
            $shorturl = Engine_Api::_()->getDbTable('shorturls', 'core')->getShorturl($resource);
            return $shorturl->link;

        }
    }

}