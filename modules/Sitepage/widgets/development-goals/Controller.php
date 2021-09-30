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
class Sitepage_Widget_DevelopmentGoalsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {

        $this->view->page_layout = $page_layout = $this->_getParam('page_layout', null);
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);

        if (!empty($page_layout) && !empty($initiative_id) && $page_layout == 'initiative_landing_page') {

            $this->view->page_id = $page_id = $this->_getParam('page_id');
            $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id');

            $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

            if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
                Engine_Api::_()->core()->setSubject($sitepage);
            }

            $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
            $this->view->page_id = $page_id = $sitepage->page_id;

            $request = Zend_Controller_Front::getInstance()->getRequest();
            $params = $request->getParams();
            $params['page_id'] = $page_id;
            $params['initiative'] = $initiative_id;
            $projectsIds = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectIds($params);

        } else {
            $this->view->initiative_id = null;
            $this->view->initiative = null;
            if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
                $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
                $this->view->page_id = $page_id = $sitepage->page_id;
            } else {
                $page_id = $this->_getParam('page_id', null);
                if ($page_id) {
                    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
                    $this->view->page_id = $page_id = $sitepage->page_id;
                }
            }

            // get page projects
            if ($page_id) {
                $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageIdOrganization($page_id);
            }
        }


        $goals = array(
            array(
                "Project_Primary_key" => 1,
                "backgroundColor" => "rgb(229, 36, 59)",
                "style" => 'transform: rotate(0deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/1_No_Poverty.png",
                "goal" => 'End poverty in all its forms everywhere',
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 2,
                "backgroundColor" => "rgb(221, 166, 58)",
                "style" => 'transform: rotate(21.2deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/2_Zero_Hunger.png",
                "goal" => "End hunger, achieve food security and improved nutrition and promote sustainable agriculture",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 3,
                "backgroundColor" => "rgb(76, 159, 56)",
                "style" => 'transform: rotate(42.4deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/3_Good_Health_And_Well_Being.png",
                "goal" => "Ensure healthy lives and promote well-being for all at all ages",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 4,
                "backgroundColor" => "rgb(197, 25, 45)",
                "style" => 'transform: rotate(63.6deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/4_Quality_Education.png",
                "goal" => "Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 5,
                "backgroundColor" => "rgb(255, 58, 33)",
                "style" => 'transform: rotate(84.8deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/5_Gender_Equality.png",
                "goal" => "Achieve gender equality and empower all women and girls",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 6,
                "backgroundColor" => "rgb(38, 189, 226)",
                "style" => 'transform: rotate(106deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/6_Clean_Water_And_Sanitation.png",
                "goal" => "Ensure availability and sustainable management of water and sanitation for all",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 7,
                "backgroundColor" => "rgb(252, 195, 11)",
                "style" => 'transform: rotate(127.2deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/7_Affordable_And_Clean_Energy.png",
                "goal" => "Ensure access to affordable, reliable, sustainable and modern energy for all",
                "Project_Name" => "Ensure  access  to  affordable,  reliable,  sustainable  and  modern  energy  for all",
                "Project_Icon" => "application/modules/Sitecrowdfunding/externals/images/yellow_icons/7.png",
                "Project_Insight" => "260",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 8,
                "backgroundColor" => "rgb(162, 25, 66)",
                "style" => 'transform: rotate(148.4deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/8_Decent_Work_And_Economic_Growth.png",
                "goal" => "Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all",
                "Project_Name" => "Promote  sustained,  inclusive  and  sustainable  economic  growth,  full  and  productive  employment  and  decent  work  for  all",
                "Project_Icon" => "application/modules/Sitecrowdfunding/externals/images/yellow_icons/8.png",
                "Project_Insight" => "290",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 9,
                "backgroundColor" => "rgb(253, 105, 37)",
                "style" => 'transform: rotate(169.6deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/9_Industry_Innovation_And_Infrastructure.png",
                "goal" => "Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 10,
                "backgroundColor" => "rgba(225, 20, 132, 0.99)",
                "style" => 'transform: rotate(190.8deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/10_Reduced_Inequalities.png",
                "goal" => "Reduce inequality within and among countries",
                "total_projects_count" => 0,
                "projects" => array(),


            ),
            array(
                "Project_Primary_key" => 11,
                "backgroundColor" => "rgba(249, 157, 38, 0.99)",
                "style" => 'transform: rotate(212deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/11_Sustainable_Cities_And_Communities.png",
                "goal" => "Make cities and human settlements inclusive, safe, resilient and sustainable",
                "Project_Name" => "Make  cities  and  human  settlements  inclusive,  resilient  and  sustainable for all",
                "Project_Icon" => "application/modules/Sitecrowdfunding/externals/images/yellow_icons/11.png",
                "Project_Insight" => "280",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 12,
                "backgroundColor" => "rgba(207, 141, 42, 0.99)",
                "style" => 'transform: rotate(233.2deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/12_Responsible_Consumption_And_Production.png",
                "goal" => "Ensure sustainable consumption and production patterns",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 13,
                "backgroundColor" => "rgba(72, 119, 62, 0.99)",
                "style" => 'transform: rotate(254.4deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/13_Climate_Action.png",
                "goal" => "Take urgent action to combat climate change and its impacts",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 14,
                "backgroundColor" => "rgb(0, 125, 188)",
                "style" => 'transform: rotate(275.6deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/14_Life_Below_Water.png",
                "goal" => "Conserve and sustainably use the oceans, seas and marine resources for sustainable development",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 15,
                "backgroundColor" => "rgba(62, 176, 73, 0.99)",
                "style" => 'transform: rotate(296.8deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/15_Life_On_Land.png",
                "goal" => "Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 16,
                "backgroundColor" => "rgba(2, 85, 139, 0.99)",
                "style" => 'transform: rotate(318deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/16_Peace_Justice_And_Strong_Institutions.png",
                "goal" => " Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions at all levels",
                "total_projects_count" => 0,
                "projects" => array(),

            ),
            array(
                "Project_Primary_key" => 17,
                "backgroundColor" => "rgba(24, 54, 104, 0.99)",
                "style" => 'transform: rotate(339.2deg) skew(72deg);',
                "imageSrc" => "application/modules/Sitecrowdfunding/externals/images/sustainable_development/17_Partnership_For_The_Goals.png",
                "goal" => "Strengthen the means of implementation and revitalize the global partnership for sustainable development",
                "total_projects_count" => 0,
                "projects" => array(),

            )
        );
        $this->view->projectsIds = array($projectsIds);
        $project_goals = Engine_Api::_()->getDbtable('goals', 'sitecrowdfunding')->getAllGoalsByProjectIds($projectsIds);

        foreach ($project_goals as $key => $item) {
            if (!empty($item['sdg_goal_id'])) {

                $existing_target = null;
                $existing_target_ids = null;

                if ($key == 0) {
                    $default_selected_goal = $item['sdg_goal_id'];
                }

                $temp = null;

                $temp = $goals[$item['sdg_goal_id'] - 1];

                // projects
                $existing_project_ids = $temp['projects'];
                $temp['projects'] = array_diff(array_merge($existing_project_ids, [$item['project_id']]), array_intersect($existing_project_ids, [$item['project_id']]));
                $temp['total_projects_count'] = count($temp['projects']);

                // targets
                $existing_target = $temp['target'];
                if (is_array($existing_target)) {
                    $temp['target'] = array_merge($existing_target, [$item['target']]);
                } else {
                    $temp['target'] = array_merge([$existing_target], [$item['target']]);
                }
                $temp['target'] = array_values(array_filter($temp['target']));
                $temp['target'] = array_unique($temp['target']);

                // target ids
                $existing_target = $temp['target_ids'];
                if (is_array($existing_target)) {
                    $temp['target_ids'] = array_merge($existing_target, [$item['sdg_target_id']]);
                } else {
                    $temp['target_ids'] = array_merge([$existing_target], [$item['sdg_target_id']]);
                }
                $temp['target_ids'] = array_values(array_filter($temp['target_ids']));
                $temp['target_ids'] = array_unique($temp['target_ids']);
                $temp['target_ids_str'] = implode(",",$temp['target_ids']);

                $temp['isSelected'] = true;

                $goals[$item['sdg_goal_id'] - 1] = $temp;
            }
        }

        $this->view->default_selected_goal = $default_selected_goal;
        $this->view->is_goal_set = $is_goal_set = count($project_goals) > 0 ? true : false;

        $this->view->goals = $goals;

        //intiative project count
        $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
          if(('sitepage_initiatives' == $routeName)) {
              $initiative_id = $this->_getParam('initiative_id', null);
              $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
              $projects = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsCountByPageIdAndInitiativesId($item['page_id'], $item['initiative_id']);
              $this->view->initiative_project_count = $projects;

          }
          else {
              $this->view->initiative_project_count = 0;
          }


    }

}
