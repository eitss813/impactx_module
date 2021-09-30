<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_IndexController extends Core_Controller_Action_Standard {

    public function indexAction() {  
        
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION TO CATEGORY HOME 
    public function categoriesAction() {
        //RENDER PAGE
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
    }

    public function categoryHomeAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);
        // SET META TITLE META DESCRIPTION AND META KEYWORDS OF THE CATEGORY 
        $params = array();
        $project_type_title = '';
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');

        if (!empty($category_id)) {
            if ($project_type_title)
                $params['project_type_title'] = $title = $project_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }
            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);
            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }

                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->sitecrowdfunding()->setMetaTitles($params);
        //SET META DESCRIPTION
        Engine_Api::_()->sitecrowdfunding()->setMetaDescriptionsBrowse($params);
        //GET PROJECT CATEGORIES TITLE
        $params['project_type_title'] = $this->view->translate('Projects');
        //SET META KEYWORDS
        Engine_Api::_()->sitecrowdfunding()->setMetaKeywords($params);

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitecrowdfunding_index_categories-home_category_$category_id");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
                ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION TO GET SUB-CATEGORY
    public function subCategoryAction() {

        //GET CATEGORY ID
        $category_id_temp = $this->_getParam('category_id_temp');

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id_temp)->getCategorySlug();
        }

        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories($category_id_temp, array('category_id', 'category_name'));

        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $data[] = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubCategoryAction() {

        //GET SUB-CATEGORY ID
        $subcategory_id_temp = $this->_getParam('subcategory_id_temp');

        //INTIALIZE ARRAY
        $this->view->subsubcats = $data = array();

        //RETURN IF SUB-CATEGORY ID IS EMPTY
        if (empty($subcategory_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');

        //GET SUB-CATEGORY
        $subCategory = $tableCategory->getCategory($subcategory_id_temp);
        if (!empty($subCategory->category_name)) {
            $subCategoryName = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id_temp)->getCategorySlug();
        }

        //GET 3RD LEVEL CATEGORIES
        $subCategories = $tableCategory->getSubCategories($subcategory_id_temp, array('category_id', 'category_name'));
        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $subCategoryName;
            $data[] = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    public function backersFaqAction() {
        $coreSetting = Engine_Api::_()->getApi('settings', 'core');
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
        $this->view->body = $coreSetting->getSetting('sitecrowdfunding.backersfaq.body', $body);
        $this->view->title = $coreSetting->getSetting('sitecrowdfunding.backersfaq.title', 'FAQs for Backers');
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.backersfaq.enabled', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
    }

    public function projectOwnerFaqAction() {
        $coreSetting = Engine_Api::_()->getApi('settings', 'core');
        $body = <<<EOD
                <div style="text-align: justify; width: 90%; line-height: 25px; padding: 20px;">
<ol>
<li style="font-weight: 400;"><strong>What are the things I should do before starting my project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You should follow below steps before starting your project:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Make a detailed budget of your costs required to bring your idea to life. Use this to set your project&rsquo;s funding goal.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) Think thoroughly about what rewards to offer to your project&rsquo;s backers</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) Have a plan to market your project so that it can reach to maximum people.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">d) Be active before launching your project till it reaches it&rsquo;s funding goal amount.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What are the various steps to create a project on this website?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Click on &lsquo;Create a Project&rsquo; button or link.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) Select the package with the feature set you need for your project.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) Fill the required details in the project creation form which you have gathered before starting this process.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">d) Go to dashboard of your project to compile overview, to configure payment gateways, to create various rewards, to add videos / photos, to set main photo / video for your project etc.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span></li>
<li style="font-weight: 400;"><strong>What should I consider while setting up funding goal for my project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Your funding goal should be the minimum amount of funds you need to complete your project along with make and ship of rewards.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Make a list of all the materials, resources, and expenses you'll need to complete your project, and the estimated costs for each. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Share a breakdown of this budget in your project description to show backers you've thought things through.<br><br></span></li>
<li style="font-weight: 400;"><strong>What are the different payment gateways which I can enable for my project&rsquo;s backers?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can configure below payment gateways if they are enabled by the site admin:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Stripe</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) PayPal</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">d) Mangopay</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What information should I share about my project on its profile page?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">After visiting your project page backers should have a clear sense of:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) What is your project as all about.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) How you will bring your project to life.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) How the funds collected will be used. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">d) The identities of the people on your team (if you have one).</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Also, y</span><span style="font-weight: 400;">our project page should tell your story and include an eye-catching project image or video, and some attractive rewards.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">The more information you share, the more you will earn your backers&rsquo; trust.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>How do I include images or other media in my project overview?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can include photos, links, videos etc. in your project overview via TinyMCE editor. </span><span style="font-weight: 400;">TinyMCE offers HTML formatting tools, like bold, italic, underline, ordered and unordered lists, different types of alignments, in-line placement of images and videos, etc. It allows users to edit HTML documents online. The different options can be configured at the time of integration with a project, which improves the flexibility of a project.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span></li>
<li style="font-weight: 400;"><strong>What are image specifications for project pages?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Your project image size should be 680x1400 pixels. Recommended file types are: JPG, JPEG, PNG, or GIF.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What does estimated delivery date mean?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">The estimated delivery date for a reward is the date you expect to deliver that reward to backers. If you're offering more than one thing in a single reward tier, set your estimated delivery date to when you expect everything in the reward tier to be delivered. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">If you're not sure what the estimated delivery date is for a reward, take some time out to create a timeline for your project so that you have a good sense of when you'll complete it. Choose a delivery date that you feel confident about and will be working towards.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What can be offered as a reward?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Rewards are generally items produced by the project itself &mdash; a copy of the album, a print from the show, a limited edition of the comic, naming characters after backers, personal phone calls etc.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Is there a way to limit the quantity of a reward?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, there is a way to limit the quantity of a reward. You can do so while creating the reward, select the &lsquo;Limit Quantity&rsquo; checkbox and enter the limit for backers who can choose this reward while backing your project.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>How do I charge shipping on my rewards?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can charge shipping cost for rewards selected by backers of certain places. To do so choose the location where you want to ship your reward and add the shipping charges in the textbox appearing along with it. This shipping cost will be added to the amount set for that reward when a backer selects the reward to fund your project.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>I am unable to edit my Project. What might be the reason behind it?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You are unable to edit your project because of possible below reasons: </span>
<ul>
<li style="font-weight: 400;"><span style="font-weight: 400;">1. Published Project with at least 1 Backer: When a project is in draft mode, it is not finalized so all the details related to that project are editable. But, once a project is published and is backed by at least one backer then few fields are non editable like: Project Duration and Funding Amount.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">If Project Owner still wants to edit the published project then he can contact to the site admin. Site admin can take proper action in such scenario and do the needful changes.</span></li>
</ul>
</li>
<li style="font-weight: 400;"><strong>I am unable to delete my Project. What might be the reason behind it?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You are unable to delete your project because of possible below reasons: </span>
<ul>
<li style="font-weight: 400;"><span style="font-weight: 400;">1. Project Backer&rsquo;s: When the project is funded even by a single backer.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">2. Member Level Settings: &lsquo;Allow Deletion of Projects?&rsquo; is disabled for the member belonging to the particular member level.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">If Project Owner still wants to delete the project then he can contact to the site admin. Site admin can take proper action in such scenario like: to refund the backed amount to the respective backer and delete the project.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">[Note: In case, no one has backed the project or the project is in draft mode, then Project Owner can delete that project.]</span></li>
</ul>
</li>
<li style="font-weight: 400;"><strong>Is it possible for a project to be funded more than the set goal amount?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, it is possible for a project to be funded more than the set goal amount or more than 100%.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Is it possible to run a Project without creating any rewards in it? I am unable to find the link to create rewards in my Project, from where I can do the same?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, it is possible to run a Project without creating any rewards in it. There is always one option to back the project with any desired amount and that is without selecting any rewards. </span>
<ul>
<li style="font-weight: 400;"><span style="font-weight: 400;">To create rewards in a project, follow below steps:</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">1. Open the profile page of the project.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">2. Now, go to the dashboard of this project.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">3. Click on &ldquo;Rewards&rdquo; from the options displaying on left side of the dashboard page.</span></li>
<li style="font-weight: 400;"><span style="font-weight: 400;">4. Create / edit / delete various rewards from here.</span><span style="font-weight: 400;"><br></span></li>
</ul>
</li>
<li style="font-weight: 400;"><strong>I am unable to edit / delete my reward of my Project. What might be the reason behind it?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You are unable to edit / delete reward of your project because of possible below reasons: </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">1. Reward Selected: Once a reward is selected by even a single backer then few fields become non editable like: Backed Amount, Estimated Delivery, Shipping Details and Reward Quantity.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">2. Project Completed: Once a project has reached its goal in defined set of time, rewards of these projects cannot be edited or deleted whether it has been selected any backer or not.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">[Note: If Project Owner still wants to edit / delete the selected reward then he can contact to the site admin. Site admin can do the needful changes.]</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>If I choose a subcategory, will my project also show up in the main category?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes. For example, if you have started an art based project and you put it in the Art subcategory i.e. Design, your project will appear in the both Art and Design category / sub-category.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Can I run more than one project at once?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, you can run more than one project at once. But, we recommend you to focus on one project at a time as it requires lots of your effort, time and patience.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Will my project go live automatically once it's approved?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, your project will be live automatically once it is approved by site admin.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>When and how should I start planning my promotion strategy?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You should start planning as soon as you decide you want to run a project. Start by thinking through who your existing fans and contacts are and organizing their information into an actionable contact list.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Choose different social media&rsquo;s and ways to promote your project. This way it will reach out to maximum people. You can also ask your friends, family members, team members etc. to spread the word about your project.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>My funding has stalled after a few days, what should I do?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) You can change your promotion strategy.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) Ask friends and family to share your project with their networks. Getting your project beyond your immediate supporters can only help. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) Share your project via blogs, newsletters etc.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Where can I find my project ?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can find your created projects, backed / liked / favorited projects at one place, i.e. at &lsquo;My Projects&rsquo; page.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Where can I track my project&rsquo;s progress?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can track your project&rsquo;s progress from &lsquo;My Projects&rsquo; page or from the project&rsquo;s profile page.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What is my responsibility for answering questions from backers and non-backers?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Backers: You can contact your backers from &lsquo;Backers Report&rsquo; section of the dashboard of your project. You can compose message for specific backer or all backers at once.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Non-backers: The members who are interested in your project and wants to contact you before backing your project, can do so via &lsquo;Contact me&rsquo; button placed on the project profile page.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>Can I run my project again if funding is unsuccessful?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Yes, of course! You can always try again and relaunch with a new goal, whenever you're ready. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Before relaunching, we recommend taking some time to review your project to see what might be improved the next time around. </span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What do I do if I miss my Estimated Delivery Date?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">The Estimated Delivery Date is intended to set expectations for backers on when they will receive rewards. Setbacks are possible with any project &mdash; creative ones especially. When the unforeseen occurs, creators are expected to post a project update explaining the situation. Sharing the story, speed bumps and all.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Creators who are honest and transparent will find backers to be far more forgiving. We&rsquo;ve all felt the urge to avoid things when we feel bad about them, but leaving backers in the dark makes them assume the worst. It not only reflects badly on the project, it&rsquo;s disrespectful to the support that community has given. Regular communication is a must.</span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>What should I consider when I'm planning to relaunch my project?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">Each launched project is a learning experience, so if you're planning to re-launch a project that wasn't successful in reaching it's goal, just make sure you've taken stock of what worked and what didn't. Here are some common points that creators usually reexamine:</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">a) Your project's goal and budget. </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">b) Your supporters. Did you let your supporters know about your project? </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">c) Your promotion plan. </span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>How do I communicate with backers?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">To communicate with your backers, you can post announcements in your project&rsquo;s profile page. You can also start a discussion if want any opinion of your backers.</span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;"><br></span></li>
<li style="font-weight: 400;"><strong>What information can I see about my backers?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">You can see backers name, amount funded, payment method and mode used while backing your project. </span><span style="font-weight: 400;"><br><br></span></li>
<li style="font-weight: 400;"><strong>How can I use the backer export?</strong><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">The backer export (available from your Backer Report) lets you export all your backer data into a spreadsheet where you can organize and sort the information to meet </span><span style="font-weight: 400;"><br></span><span style="font-weight: 400;">almost any need. </span><span style="font-weight: 400;"><br></span></li>
</ol>
</div>
EOD;
        $this->view->body = $coreSetting->getSetting('sitecrowdfunding.projectownerfaq.body', $body);
        $this->view->title = $coreSetting->getSetting('sitecrowdfunding.projectownerfaq.title', 'FAQs for Project Owner');
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.projectownerfaq.enabled', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
    } 

    //ACTION FOR GETTING Project Options
    function getProjectSuggestionsAction() {

        $params = $this->_getAllParams(); 
        //GET PROJECTS TABLE
        $sitecrowdfundingTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $sitecrowdfundingTableName = $sitecrowdfundingTable->info('name');
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $select = $sitecrowdfundingTable->getProjectSelect(array(
            'search' => $this->_getParam('text'),
            'selectLimit' => 40,
            'orderby' => 'title',
            'owner_id' => $params['owner_id'], 
        )); 

        //FETCH RESULTS
        $usersiteprojects = $sitecrowdfundingTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

}
