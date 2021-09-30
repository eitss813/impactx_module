<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_MainProjectInformationController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        $this->view->current_link = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->projectOption = $this->_getParam('projectOption', array("title", "description", "owner", "location", "fundingRatio", "fundedAmount", "daysLeft", "backerCount", "backButton", "category", "dashboardButton", "shareOptions", "optionsButton"));
        $this->view->columnHeight = 470;//$this->_getParam('columnHeight', 400);
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 20);
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 50);
        $this->view->showPhoto = true;
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');

        //0:Video,1:Photo
        $profile_cover = $tableOtherinfo->getColumnValue($project->getIdentity(), 'profile_cover');
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $sitecrowdfundingMainProjectInfo = Zend_Registry::isRegistered('sitecrowdfundingMainProjectInfo') ? Zend_Registry::get('sitecrowdfundingMainProjectInfo') : null;
        if ($profile_cover == 0 && !empty($project->video_id) && $sitevideoEnabled && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'))) {
            $this->view->video = $video = Engine_Api::_()->getItem('sitevideo_video', $project->video_id);
            if ($video) {
                $this->view->showPhoto = false;
            }
        }
        if (empty($sitecrowdfundingMainProjectInfo))
            return $this->setNoRender();
        // Suggest to Friend link show work
        $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        if (!empty($is_suggestion_enabled)) {
            $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitecrowdfunding', $project->project_id, null, null);
            if (!empty($modContentObj)) {
                $contentCreatePopup = @COUNT($modContentObj);
            }
            Engine_Api::_()->sitecrowdfunding()->deleteSuggestion(Engine_Api::_()->user()->getViewer()->getIdentity(), 'sitecrowdfunding', $project->project_id, 'sitecrowdfunding_project', 'sitecrowdfunding_suggestion');
            if (!empty($contentCreatePopup)) {
                $this->view->projectSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitecrowdfunding', 'link');
            }
        } else {
            $this->view->projectSuggLink = 0;
        }

        // get following count for project
        $this->view->resource_id = $resource_id = $project->getIdentity();
        $this->view->resource_type = $resource_type = $project->getType();
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($resource_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($resource_id);
        }
        $this->view->parentOrganization = $parentOrganization;

        $this->view->noOfFollowingCount = $noOfFollowingCount = Engine_Api::_()->getApi('favourite', 'seaocore')->favouriteCount($resource_type, $resource_id);
        $this->view->noOfMembersCount = $noOfMembersCount = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->getMembersCount($resource_id);

        $fundingDatas = Engine_Api::_()->getDbTable('externalfundings', 'sitecrowdfunding')->getExternalFundingAmount($project->getIdentity());
        $this->view->totalFundingAmount = $fundingDatas['totalFundingAmount'];
        $this->view->memberCount = $fundingDatas['memberCount'];
        $this->view->orgCount = $fundingDatas['orgCount'];
        $this->view->total_backer_count = $fundingDatas['memberCount'] + $fundingDatas['orgCount'];
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        /*
         *
            - Organisation => apply to project by default
		        - Initiatives => if this is filled take initiatives donate label instead of organisation donate label
			        - Project => if this is filled take project donate label instead of organisation/initiatives donate label
         */

        $donateLabel = null;
        $isTaxEnabled = null;
        $taxLabel = null;

        if ($parentOrganization['page_id']) {

            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);

            if ($project->initiative_id) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id);

                /** payment_is_tax_deductible **/
                // if all present, then use project
                if ($initiative->payment_is_tax_deductible !== null && $project->payment_is_tax_deductible !== null && $sitepage->payment_is_tax_deductible !== null) {
                    $isTaxEnabled = $project->payment_is_tax_deductible;
                } // if initiative not present and project present, then use project label
                elseif ($initiative->payment_is_tax_deductible === null && $project->payment_is_tax_deductible !== null && ($sitepage->payment_is_tax_deductible !== null || $sitepage->payment_is_tax_deductible === null)) {
                    $isTaxEnabled = $project->payment_is_tax_deductible;
                } // if initiative present and project not present, then update initiative into project
                elseif ($initiative->payment_is_tax_deductible !== null && $project->payment_is_tax_deductible === null && ($sitepage->payment_is_tax_deductible !== null || $sitepage->payment_is_tax_deductible === null)) {
                    $isTaxEnabled = $initiative->payment_is_tax_deductible;
                } // if both initiative and project not present, then use sitepage label
                elseif ($initiative->payment_is_tax_deductible === null && $project->payment_is_tax_deductible === null && ($sitepage->payment_is_tax_deductible !== null || $sitepage->payment_is_tax_deductible === null)) {
                    $isTaxEnabled = $sitepage->payment_is_tax_deductible;
                } // use sitepage
                else {
                    $isTaxEnabled = $sitepage->payment_is_tax_deductible;
                }

                /** payment_tax_deductible_label **/
                // if all present, then use project
                if ($initiative->payment_tax_deductible_label && $project->payment_tax_deductible_label && $sitepage->payment_tax_deductible_label) {
                    $taxLabel = $project->payment_tax_deductible_label;
                } // if initiative not present and project present, then use project label
                elseif (!$initiative->payment_tax_deductible_label && $project->payment_tax_deductible_label && ($sitepage->payment_tax_deductible_label || !$sitepage->payment_tax_deductible_label)) {
                    $taxLabel = $project->payment_tax_deductible_label;
                } // if initiative present and project not present, then update initiative into project
                elseif ($initiative->payment_tax_deductible_label && !$project->payment_tax_deductible_label && ($sitepage->payment_tax_deductible_label || !$sitepage->payment_tax_deductible_label)) {
                    $taxLabel = $initiative->payment_tax_deductible_label;
                } // if both initiative and project not present, then use sitepage label
                elseif (!$initiative->payment_tax_deductible_label && !$project->payment_tax_deductible_label && ($sitepage->payment_tax_deductible_label || !$sitepage->payment_tax_deductible_label)) {
                    $taxLabel = $sitepage->payment_tax_deductible_label;
                } // use sitepage
                else {
                    $taxLabel = $sitepage->payment_tax_deductible_label;
                }

                /** payment_action_label **/
                // if all present, then use project
                if ($initiative->payment_action_label && $project->payment_action_label && $sitepage->payment_action_label) {
                    $donateLabel = $project->payment_action_label;
                } // if initiative not present and project present, then use project label
                elseif (!$initiative->payment_action_label && $project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $project->payment_action_label;
                } // if initiative present and project not present, then update initiative into project
                elseif ($initiative->payment_action_label && !$project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $initiative->payment_action_label;
                } // if both initiative and project not present, then use sitepage label
                elseif (!$initiative->payment_action_label && !$project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $sitepage->payment_action_label;
                } // use sitepage
                else {
                    $donateLabel = $sitepage->payment_action_label;
                }

            } else {

                /** payment_is_tax_deductible **/
                // if sitepage present and project present, then use project label
                if ($sitepage->payment_is_tax_deductible !== null && $project->payment_is_tax_deductible !== null) {
                    // print_r(166);
                    $isTaxEnabled = $project->payment_is_tax_deductible;
                } // if sitepage not present and project present, then use project label
                elseif ($sitepage->payment_is_tax_deductible === null && $project->payment_is_tax_deductible !== null) {
                    // print_r(171);
                    $isTaxEnabled = $project->payment_is_tax_deductible;
                } // if sitepage present and project not present, then update sitepage into project
                elseif ($sitepage->payment_is_tax_deductible !== null && $project->payment_is_tax_deductible === null) {
                    // print_r(176);
                    $isTaxEnabled = $sitepage->payment_is_tax_deductible;
                } // use sitepage
                else {
                    // print_r(180);
                    $isTaxEnabled = $sitepage->payment_is_tax_deductible;
                }

                /** payment_tax_deductible_label **/
                // if sitepage present and project present, then use project label
                if ($sitepage->payment_tax_deductible_label && $project->payment_tax_deductible_label) {
                    // print_r(187);
                    $taxLabel = $project->payment_tax_deductible_label;
                } // if sitepage not present and project present, then use project label
                elseif (!$sitepage->payment_tax_deductible_label && $project->payment_tax_deductible_label) {
                    // print_r(192);
                    $taxLabel = $project->payment_tax_deductible_label;
                } // if sitepage present and project not present, then update sitepage into project
                elseif ($sitepage->payment_tax_deductible_label && !$project->payment_tax_deductible_label) {
                    // print_r(197);
                    $taxLabel = $sitepage->payment_tax_deductible_label;
                } // use sitepage
                else {
                    // print_r(202);
                    $taxLabel = $sitepage->payment_tax_deductible_label;
                }

                /** payment_action_label **/
                // if sitepage present and project present, then use project label
                if ($sitepage->payment_action_label && $project->payment_action_label) {
                    // print_r(208);
                    $donateLabel = $project->payment_action_label;
                } // if initiative not present and project present, then use project label
                elseif (!$sitepage->payment_action_label && $project->payment_action_label) {
                    // print_r(213);
                    $donateLabel = $project->payment_action_label;
                } // if sitepage present and project not present, then update sitepage into project
                elseif ($sitepage->payment_action_label && !$project->payment_action_label) {
                    // print_r(218);
                    $donateLabel = $sitepage->payment_action_label;
                } // use sitepage
                else {
                    // print_r(223);
                    $donateLabel = $sitepage->payment_action_label;
                }
            }
        } else {
            $isTaxEnabled = $project->payment_is_tax_deductible;
            $taxLabel = $project->payment_tax_deductible_label;
            $donateLabel = $project->payment_action_label;
        }

        // update in table if not same
        if ($project->payment_is_tax_deductible != $isTaxEnabled) {
            $this->view->payment_is_tax_deductible = $isTaxEnabled;
        } else {
            $this->view->payment_is_tax_deductible = $project->payment_is_tax_deductible;
        }

        if ($project->payment_tax_deductible_label != $taxLabel) {
            $this->view->payment_tax_deductible_label = $taxLabel;
        } else {
            $this->view->payment_tax_deductible_label = $project->payment_tax_deductible_label;
        }

        if ($project->payment_action_label != $donateLabel) {
            $this->view->payment_action_label = $donateLabel;
        } else {
            $this->view->payment_action_label = $project->payment_action_label;
        }

    }

}
