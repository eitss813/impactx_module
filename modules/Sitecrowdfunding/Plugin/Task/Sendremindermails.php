<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sendremindermails.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Sitecrowdfunding_Plugin_Task_Sendremindermails extends Core_Plugin_Task_Abstract
{
  public function execute()
  { 
  	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
    $projectTableName = $projectTable->info('name'); 
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
    date_default_timezone_set($timezone);
    $host = $_SERVER['HTTP_HOST'];

    $duration = $settings->getSetting('sitecrowdfunding.reminder.duration.options', '1');
 
    if($settings->getSetting('sitecrowdfunding.reminder.before.project.completion', 0)) {
        //AUTOMATIC EMAIL REMINDER BEFORE THE PROJECT COMPLETION 
        $currentDate = date('Y-m-d H:i:s');
        $newDate =  date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s', strtotime("+$duration days"))));
        $select = $projectTable->select()->from($projectTableName, '*')
                    ->where('funding_approved=?', 1)
                    ->where('funding_status = ?', 'active')
                    ->where('funding_state=?', 'published')
                    ->where("funding_end_date BETWEEN '$currentDate' AND '$newDate'");
        $projects = $projectTable->fetchAll($select);  

        foreach ($projects as $project) {
            //send mail to all project favourities
            $project_link = $view->htmlLink($host . $project->getHref(), $project->title);

            if(in_array('favourites', $settings->getSetting('sitecrowdfunding.reminder.project.completion.options', 'project_owners'))) { 
                $favouritesPaginator = Engine_Api::_()->getDbtable( 'favourites' , 'seaocore' )->getFavouritePaginator($project); 
                foreach ($favouritesPaginator as $value) {
                    $favouritesID[] = $value->poster_id;
                } 
                //SEND MAIL NOW TO ALL FAVOURITES 
                foreach ($favouritesID as $id) {
                    $user = Engine_Api::_()->user()->getUser($id);
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, "SITECROWDFUNDING_REMINDER_PROJECT_COMPLETION_MEMBERS", array(
                        'project_name' => $project->title,
                        'member_name' => $user->getTitle(), 
                        'project_link' => $project_link,
                        'project_end_date' => $project->funding_end_date
                    ));
                }
            } 

            if(in_array('project_owners', $settings->getSetting('sitecrowdfunding.reminder.project.completion.options', 'project_owners'))) {    
                //SEND MAIL TO PROJECT OWNERS
                $owner = $project->getOwner(); 
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'SITECROWDFUNDING_REMINDER_PROJECT_COMPLETION_OWNER', array(
                        'member_name' => $owner->getTitle(),
                        'project_name' => $project->title,
                        'project_link' => $project_link, 
                ));
            }

            if(in_array('project_owners_and_admins', $settings->getSetting('sitecrowdfunding.reminder.project.completion.options', 'project_owners'))) {
                //SEND MAIL TO PROJECT OWNERS AND PROJECT ADMINS
                /***
                 *
                 * send notification and email to all project admins
                 *
                 ***/
                $list = $project->getLeaderList();
                $list_id = $list['list_id'];

                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                $selectLeaders[] = $project->owner_id;

                $selectUsers = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $adminMembers = $userTable->fetchAll($selectUsers);

                foreach($adminMembers as $adminMember){
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, 'SITECROWDFUNDING_REMINDER_PROJECT_COMPLETION_OWNER', array(
                        'member_name' => $adminMember->getTitle(),
                        'project_name' => $project->title,
                        'project_link' => $project_link,
                    ));
                }
            }
        }
    }

    if($settings->getSetting('sitecrowdfunding.reminder.for.payment.gateway.configuration', 0)) {
        //PROJECTS THEIR PAYMENT DONE BUT GATEWAYS ARE NOT CONFIGURE
        $projectGatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
        $projectGatewayTableName = $projectGatewayTable->info('name');

        $select = $projectGatewayTable->select()->from($projectGatewayTableName, '*')
                    ->where('enabled=?', 1);
        $projectGs = $projectGatewayTable->fetchAll($select); 
        foreach ($projectGs as $projectG) {
            $projectIds[]=$projectG->project_id;
        } 

        $select = $projectTable->select()->from($projectTableName, '*')
                    ->where('approved=?', 1)
                    ->where('status = ?', 'active')
                    ->where('state=?', 'published');
        $projects = $projectTable->fetchAll($select); 

        foreach ($projects as $project) {
            $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
            if(!in_array($project->project_id, $projectIds)) {

                /***
                 *
                 * send notification and email to all project admins
                 *
                 ***/
                $list = $project->getLeaderList();
                $list_id = $list['list_id'];

                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                $selectLeaders[] = $project->owner_id;

                $selectUsers = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $adminMembers = $userTable->fetchAll($selectUsers);

                foreach($adminMembers as $adminMember){
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, 'SITECROWDFUNDING_REMINDER_PROJECT_GATEWAY_CONFIGURATION', array(
                        'member_name' => $adminMember->getTitle(),
                        'project_name' => $project->title,
                        'project_link' => $project_link,
                    ));
                }

            }
        }
    }


    if($settings->getSetting('sitecrowdfunding.reminder.for.project.payment', 1)) {
        //MAIL TO PROJECT OWNERS AFTER PROJECT CREATION TO DO THE PAYMENT
        /*$select = $projectTable->select()->from($projectTableName, '*')
                    ->where('approved=?', 0)
                    ->where('status != ?', 'active')
                    ->where('state != ?', 'published');
        $projects = $projectTable->fetchAll($select);

        foreach ($projects as $project) { 
            $owner = $project->getOwner();
            $project_link = $view->htmlLink($host . $project->getHref(), $project->title); 
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'SITECROWDFUNDING_REMINDER_PACKAGE_PAYMENT', array(
                    'member_name' => $owner->getTitle(),
                    'project_name' => $project->title,
                    'project_link' => $project_link, 
            ));
        }*/
    } 

  }
}