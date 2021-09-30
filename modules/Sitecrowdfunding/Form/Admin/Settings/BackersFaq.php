<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_BackersFaq extends Engine_Form {

    public function init() {

        $this->setTitle('FAQs for Backers')
                ->setDescription('Compose the list of FAQs for your members who are backing projects on your site. This will help your site members to understand how they can back a project, what they will get in return, how they can keep track of updates in their backed projects etc. These FAQs will be visible listed in the Project navigation tab.')
                ->setName('faq_backers');
   
                
        $body = <<<EOD
<div style="text-align: justify; width: 90%; line-height: 25px; padding: 20px;">
                <ol>
                <li style="font-weight: 400;"><strong>How can I found interesting projects as per my preference?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">To find interesting projects as per your preference follow below steps:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Go to &lsquo;Browse Projects&rsquo; page.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) Enter the criteria in the search form as per your preference.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) You can go through the projects as per the searched criteria.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>How can I back a project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can back a project in two ways:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Back Button: Click on back button and you will be re-directed to the page with all the list of rewards and an option to back any amount to the project.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) Reward Selection: choose the reward listed on the project profile page and you will be redirected to the page where that reward is pre-selected.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Next step is to fill your delivery address and pay for the back amount using available payment options.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>Can I back a project more than once?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, you can back a project more than once.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span></li>
                <li style="font-weight: 400;"><strong>How can I contact Project Owner for any queries related to his project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can click on &lsquo;Contact Me&rsquo; button placed on project profile page to contact the Project Owner for any queries related to his project.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>Is it possible to get refund of the amount I have backed for a project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Amount backed can be refunded back or not is entirely dependent on the project owner and site admin. So, in case of any refund, please contact project owner or site admin.<br><br></span></li>
                <li style="font-weight: 400;"><strong>Do I get notified if a project I have backed succeeds or not?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, you will be notified about the success and failure of the project which you have backed.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>Is my pledge amount publicly displayed?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">This depends entirely on you. You can make your contribution anonymous while backing the project.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>How can I know in detail about the project owner?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can see the full biography of the project owner by clicking on the &lsquo;Full Bio&rsquo; button present on the project profile page. Here, you can also the link of other social media profile of the project owner like: Facebook, Twitter, LinkedIn, Google Plus etc.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>Where can I keep track of my backed details related to various projects?</strong><strong><br></strong><span style="font-weight: 400;">You can keep track of your backed details related to various projects from &lsquo;My Projects&rsquo; section. You can also print invoice of the backing details from here.</span><strong><br><br></strong></li>
                <li style="font-weight: 400;"><strong>Will I receive the invoice for my backed amount?</strong><strong><br></strong><strong>Yes, you will receive the invoice for you backed amount on your registered email address. You</strong><span style="font-weight: 400;"> can also print invoice of the backing details from &lsquo;My Projects&rsquo; section.</span><strong><br><br></strong></li>
                <li style="font-weight: 400;"><strong>How do I know when rewards for a project will be delivered?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Projects have an Estimated Delivery Date under each reward on the project page. You can view the Estimated Delivery Date either on the project profile page. This date is entered by project owners as their best guess for delivery to backers.</span><span style="font-weight: 400;"><br><br></span></li>
                <li style="font-weight: 400;"><strong>I haven't gotten my reward yet. What do I do?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">The first step is checking the Estimated Delivery Date on the project page. Backing a project is a lot different than simply ordering a product online, and sometimes projects are in very early stages when they are funded.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">If the Estimated Delivery Date has passed, check for project updates that may explain what happened. Sometimes project owners hit unexpected roadblocks, or simply underestimate how much work it takes to complete a project. PRoject owners are expected to communicate these setbacks when they happen.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">If the project owner hasn&rsquo;t posted any update, send them a direct message to request more information about their progress, or post a public comment on their project asking for a status update.<br><br></span></li>
                </ol>
                </div>
EOD;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Text', "sitecrowdfunding_backersfaq_title", array(
            'label' => 'Title',
            'description' => 'Please enter the title below which you want to be visible to users.',
            'value' => $coreSettings->getSetting('sitecrowdfunding.backersfaq.title', 'FAQs for Backers'),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StringTrim',
            ),
        ));
        $filter = new Engine_Filter_Html();
        $this->addElement('TinyMce', 'sitecrowdfunding_backersfaq_body', array(
            'label' => 'Description',
            'description' => 'Click on the Fullscreen icon in the Editor to get the editor in fullscreen mode, thus enabling you to create your content better.',
            'value' => $coreSettings->getSetting('sitecrowdfunding.backersfaq.body', $body),
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
            'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
            'filters' => array(new Engine_Filter_Censor(), $filter),
        ));
        $this->addElement('Checkbox', 'sitecrowdfunding_backersfaq_enabled', array(
            'label' => "Enable Backers FAQ page",
            'value' => 1
        ));
        // Element: Button Submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
