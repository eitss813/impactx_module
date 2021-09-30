<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberInfo.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_View_Helper_MemberInfo extends Zend_View_Helper_Abstract {

    public function memberInfo($sitemember, $memberInfo, $params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
        ?>  
        <?php if (!empty($memberInfo) && in_array('ratingStar', $memberInfo) && $sitemember->rating_avg) : ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_rating" title="<?php echo $view->translate("Rating") ?>"></i>
                <div class="o_hidden f_small">
                    <span title="<?php echo $view->translate('Overall Rating: %s', $sitemember->rating_avg); ?>">
                        <?php for ($x = 1; $x <= $sitemember->rating_avg; $x++) { ?>
                            <span class="seao_rating_star_generic rating_star_y" title="<?php echo $view->translate('Overall Rating: %s', $sitemember->rating_avg); ?>"></span>
                            <?php
                        }
                        $roundrating = round($sitemember->rating_avg);
                        if (($roundrating - $sitemember->rating_avg) > 0) {
                            ?>
                            <span class="seao_rating_star_generic rating_star_half_y" title="<?php echo $view->translate('Overall Rating: %s', $sitemember->rating_avg); ?>"></span>
                            <?php
                        }
                        $roundrating++;
                        for ($x = $roundrating; $x <= 5; $x++) {
                            ?>
                            <span class="seao_rating_star_generic seao_rating_star_disabled" title="<?php echo $view->translate('Overall Rating: %s', $sitemember->rating_avg); ?>"></span>
                        <?php } ?>
                    </span>
                </div>
            </div> 
        <?php endif; ?>   

        <?php
        if (!empty($memberInfo) && in_array('profileField', $memberInfo)) {
            $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

            $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitemember);
            $userProfileFields = $view->userFieldValueLoop($sitemember, $fieldStructure, array('customParams' => $params['customParams'], 'custom_field_title' => $params['custom_field_title'], 'custom_field_heading' => $params['custom_field_heading']));
            if (!empty($userProfileFields)) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Profile Fields") . '" class="seao_icon_strip seao_icon seao_icon_host"></i><div class="o_hidden f_small seaocore_txt_light">' . $userProfileFields . '</div></div>';
            }
        }

        
        if (!empty($memberInfo) && in_array('age', $memberInfo)) {
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($sitemember);
            if (!empty($fieldsByAlias['birthdate'])) {
                $optionId = $fieldsByAlias['birthdate']->getValue($sitemember);
                if ($optionId) {
                    $age = floor((time() - strtotime($optionId->value)) / 31556926);
                    echo '<div class="seao_listings_stats"><i title="' . $view->translate("Age") . '" class="seao_icon_strip seao_icon seao_icon_age"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate(array('%s year old', '%s years old', $age), $view->locale()->toNumber($age)) . '</div></div>';
                }
            }
        }

        if (!empty($memberInfo) && in_array('membertype', $memberInfo)) {
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($sitemember);
            if (!empty($fieldsByAlias['profile_type'])) {
                $optionId = $fieldsByAlias['profile_type']->getValue($sitemember);
                if ($optionId) {
                    $optionObj = Engine_Api::_()->fields()
                            ->getFieldsOptions($sitemember)
                            ->getRowMatching('option_id', $optionId->value);
                    if ($optionObj) {
                        echo '<div class="seao_listings_stats"><i title="' . $view->translate("Member Type") . '" class="seao_icon_strip seao_icon seao_icon_member"></i><div class="o_hidden f_small seaocore_txt_light">' . $optionObj->label . '</div></div>';
                    }
                }
            }
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1)) {
            if (!empty($sitemember->location) && in_array('location', $memberInfo)) {
                echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_location" title="' . $view->translate("Location") . '"></i><div class="o_hidden f_small seaocore_txt_light">';

                if (!in_array('directionLink', $memberInfo)) {
                    echo '<span title="' . $sitemember->location . '">' .
                    $sitemember->location . '</span>';
                } else if (in_array('directionLink', $memberInfo)) {
                    echo $view->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitemember->seao_locationid, 'resouce_type' => 'seaocore'), $sitemember->location, array('onclick' => 'openSmoothbox(this);return false', 'title' => $sitemember->location));
                }

                if (in_array('distance', $memberInfo) && isset($sitemember->distance)) {
                    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer')) {
                        echo "<br>";
                        echo $view->translate("approximately %s miles", round($sitemember->distance, 2));
                    } else {
                        $distance = (1 / 0.621371192) * $sitemember->distance;
                        echo "<br>";
                        echo $view->translate("approximately %s kilometers", round($distance, 2));
                    }
                }
                echo '</div>
     </div>';
            }
        }

        $statistics = '';
        if (!empty($memberInfo) && in_array('viewCount', $memberInfo)) {
            $statistics .= $view->translate(array('%s view', '%s views', $sitemember->view_count), $view->locale()->toNumber($sitemember->view_count)) . ', ';
        }

        if (!empty($memberInfo) && in_array('likeCount', $memberInfo)) {
            $likeCount = Engine_Api::_()->getApi('like', 'seaocore')->likeCount('user', $sitemember->user_id);
            $statistics .= $view->translate(array('%s like', '%s likes', $likeCount), $view->locale()->toNumber($likeCount)) . ', ';
        }

        //$this->view->allow_verify = $allowVerify = Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify')) {
            $verifyCount = Engine_Api::_()->getDbTable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id, 'user');
            if (!empty($memberInfo) && in_array('verifyCount', $memberInfo)) {
                $statistics .= $view->translate(array('%s verified', '%s verified', $verifyCount), $view->locale()->toNumber($verifyCount)) . ', ';
            }
        }

        if (!empty($memberInfo) && in_array('memberCount', $memberInfo)) {
            $statistics .= $view->translate(array('%s friend', '%s friends', $sitemember->member_count), $view->locale()->toNumber($sitemember->member_count)) . ', ';
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') != 3) {
            if (!empty($memberInfo) && in_array('reviewCount', $memberInfo) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')) {
                $statistics .= $view->translate(array('%s review', '%s reviews', $sitemember->review_count), $view->locale()->toNumber($sitemember->review_count)) . ', ';
            } else if (!empty($memberInfo) && in_array('reviewCount', $memberInfo) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0)) {
                $statistics .= $view->translate(array('%s vote', '%s votes', $sitemember->review_count), $view->locale()->toNumber($sitemember->review_count)) . ', ';
            }
        }

        if (!empty($memberInfo) && in_array('recommendCount', $memberInfo) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0)) {
            $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember');
            $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'resource_id' => $sitemember->user_id));
            $totalRecommend = $recommendpaginator->getTotalItemCount();
            $statistics .= $view->translate(array('%s recommendation', '%s recommendations', $totalRecommend), $view->locale()->toNumber($totalRecommend)) . ', ';
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        if (!empty($statistics)) {
            echo '<div class="seao_listings_stats"><i title="' . $view->translate("Statistics") . '" class="seao_icon_strip seao_icon seao_icon_stats"></i><div class="o_hidden f_small seaocore_txt_light">' . $statistics . '</div></div>';
        }

        if (!empty($memberInfo) && in_array('joined', $memberInfo)) {
            echo '<div class="seao_listings_stats"><i title="' . $view->translate("Creation Date") . '" class="seao_icon_strip seao_icon seao_icon_time"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Joined: %s", $view->timestamp($sitemember->creation_date)) . '</div></div>';
        }

        if (!empty($memberInfo) && in_array('lastupdate', $memberInfo)) {
            if ($sitemember->modified_date != "0000-00-00 00:00:00") {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Last Update") . '" class="seao_icon_strip seao_icon seao_icon_date"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Last Update: %s", $view->timestamp($sitemember->modified_date)) . '</div></div>';
            } else {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Last Update") . '" class="seao_icon_strip seao_icon seao_icon_date"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Last Update: %s", $view->timestamp($sitemember->creation_date)) . '</div></div>';
            }
        }


        if (!empty($memberInfo) && in_array('networks', $memberInfo)) {
            $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($sitemember)->where('hide = ?', 0);
            $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
            if (count($networks) > 0) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Network") . '" class="seao_icon_strip seao_icon seao_icon_location"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->fluentList($networks) . '</div></div>';
            }
        }

        if (!empty($memberInfo) && in_array('mutualFriend', $memberInfo)) {
            $mutualfriendCount = Engine_Api::_()->seaocore()->getMutualFriend($sitemember->user_id)->getTotalItemCount();
            if (!empty($mutualfriendCount) && ($sitemember->user_id != Engine_Api::_()->user()->getViewer()->getIdentity())) {
                ?>
                <div class="seao_listings_stats"><i title='<?php
                if ($mutualfriendCount == 1): echo $view->translate("Mutual Friend");
                else: echo $view->translate("Mutual Friends");
                endif;
                ?>' class="seao_icon_strip seao_icon seao_icon_mutual_friend"></i><div class="o_hidden f_small"><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $view->escape($view->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'more-mutual-friend', 'id' => $sitemember->user_id, 'format' => 'smoothbox'), 'default', true)); ?>');
                                  return false;" > <?php echo $view->translate(array('%s mutual friend', '%s mutual friends', $mutualfriendCount), $mutualfriendCount) ?> </a></div></div> 
                                                    <?php
                                                }
                                            }
                                            if (!empty($memberInfo) && in_array('memberStatus', $memberInfo)) {
                                                $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                                                if (!empty($online_status)) {
                                                    ?>
                <div class="seao_listings_stats f_small seaocore_txt_light"><img title="Online" src='<?php echo $view->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                    <?php echo $view->translate("Online"); ?></div> 
                    <?php
            }
        }
    }

}
?>
