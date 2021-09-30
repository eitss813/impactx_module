<?php

$db = Engine_Db_Table::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitestaticpage_admin_main_manage", "sitestaticpage", "Static Pages & HTML Blocks", "", \'{"route":"admin_default","module":"sitestaticpage","controller":"manage"}\', "sitestaticpage_admin_main", "", 2),
("sitestaticpage_admin_main_questions", "sitestaticpage", "Manage Forms", "", \'{"route":"admin_default","module":"sitestaticpage","controller":"fields"}\', "sitestaticpage_admin_main", "", 3);');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITESTATICPAGE_PROFILEQUESTIONS_EMAIL", "sitestaticpage", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]");');

$staticPageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');

$body = '<p><span style="font-size: 14pt;"><strong>Terms of Service</strong></span></p>
<p><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="text-align: justify;"><span style="font-size: 10pt;">These Terms of Service ("Terms") govern your access to and use of our website, products, and services ("Products"). Please read these Terms carefully, and contact us if you have any questions. By accessing or using our Products, you agree to be bound by these Terms and by our Privacy Policy.</span></p>
<p><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p><strong><span style="font-size: 12pt;">1. Using This Website</span></strong></p>
<p><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;"><strong>a. Who can use this Website</strong></span></p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">You may use our Products only if you can form a binding contract with us, and only in compliance with these Terms and all applicable laws. When you create your account, you must provide us with accurate and complete information. Any use or access by anyone under the age of 13 is prohibited. Some of our Products may be software that is downloaded to your computer, phone, tablet, or other device. You agree that we may automatically upgrade those Products, and these Terms will apply to such upgrades.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><strong><span style="font-size: 12pt;">2. Your Content</span></strong></p>
<p><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;"><strong>a. Posting content</strong></span></p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We allow you to post content, including photos, comments, links, and other materials. Anything that you post or otherwise make available on our Products is referred to as "User Content." You retain all rights in, and are solely responsible for, the User Content you post.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">&nbsp;</span></p>
<p style="padding-left: 30px; text-align: justify;"><strong><span style="font-size: 10pt;">b. How other users can use your content</span></strong></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">You grant other users a non-exclusive, royalty-free, transferable, sublicensable, worldwide license to use, store, display, reproduce, share, modify, create derivative works, perform, and distribute your User Content on this site solely for the purposes of operating, developing, providing, and using the Products. Nothing in these Terms shall restrict other legal rights we may have to User Content, for example under other licenses. We reserve the right to remove or modify User Content for any reason, including User Content that we believe violates these Terms or our policies.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><strong><span style="font-size: 10pt;">c. How long we keep your content</span></strong></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">Following termination or deactivation of your account, or if you remove any User Content from this site, we may retain your User Content for a commercially reasonable period of time for backup, archival, or audit purposes. Furthermore, our users may retain and continue to use, store, display, reproduce, re-share, modify, create derivative works, perform, and distribute any of your User Content that other users have stored or shared through this site.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">&nbsp;</span></p>
<p style="padding-left: 30px; text-align: justify;"><strong><span style="font-size: 10pt;">d. Feedback you provide</span></strong></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">We value hearing from our users, and are always interested in learning about ways we can make this website more awesome. If you choose to submit comments, ideas or feedback, you agree that we are free to use them without any restriction or compensation to you.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>3. Security</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">We care about the security of our users. While we work to protect the security of your content and account, we cannot guarantee that unauthorized third parties will not be able to defeat our security measures. Please notify us immediately of any compromise or unauthorized use of your account.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>4. Termination</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">We may terminate or suspend this license at any time, with or without cause or notice to you. Upon termination, you continue to be bound by Sections 2 and 6-12 of these Terms.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>5. Disclaimers</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">The Products and all included content are provided on an "as is" basis without warranty of any kind, whether express or implied.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">THIS WEBSITE SPECIFICALLY DISCLAIMS ANY AND ALL WARRANTIES AND CONDITIONS OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT, AND ANY WARRANTIES ARISING OUT OF COURSE OF DEALING OR USAGE OF TRADE.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">This Website takes no responsibility and assumes no liability for any User Content that you or any other user or third party posts or transmits using our Products. You understand and agree that you may be exposed to User Content that is inaccurate, objectionable, inappropriate for children, or otherwise unsuited to your purpose.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>6. Limitation of Liability</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">TO THE MAXIMUM EXTENT PERMITTED BY LAW, THIS WEBSITE SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOOD-WILL, OR OTHER INTANGIBLE LOSSES, RESULTING FROM (A) YOUR ACCESS TO OR USE OF OR INABILITY TO ACCESS OR USE THE PRODUCTS; (B) ANY CONDUCT OR CONTENT OF ANY THIRD PARTY ON THE PRODUCTS, INCLUDING WITHOUT LIMITATION, ANY DEFAMATORY, OFFENSIVE OR ILLEGAL CONDUCT OF OTHER USERS OR THIRD PARTIES; OR (C) UNAUTHORIZED ACCESS, USE OR ALTERATION OF YOUR TRANSMISSIONS OR CONTENT. IN NO EVENT SHALL OUR\'S AGGREGATE LIABILITY FOR ALL CLAIMS RELATING TO THE PRODUCTS EXCEED ONE HUNDRED U.S. DOLLARS (U.S. $100.00).</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>7. Arbitration</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">For any dispute you have with us, you agree to first contact us and attempt to resolve the dispute with us informally. If we have not been able to resolve the dispute with you informally, we each agree to resolve any claim, dispute, or controversy (excluding claims for injunctive or other equitable relief) arising out of or in connection with or relating to these Terms by binding arbitration by the American Arbitration Association ("AAA") under the Commercial Arbitration Rules and Supplementary Procedures for Consumer Related Disputes then in effect for the AAA, except as provided herein. Unless you and we agree otherwise, the arbitration will be conducted in the country where you reside. Each party will be responsible for paying any AAA filing, administrative and arbitrator fees in accordance with AAA rules, except that we will pay for your reasonable filing, administrative, and arbitrator fees if your claim for damages does not exceed $75,000 and is non-frivolous (as measured by the standards set forth in Federal Rule of Civil Procedure 11(b)). The award rendered by the arbitrator shall include costs of arbitration, reasonable attorneys\' fees and reasonable costs for expert and other witnesses, and any judgment on the award rendered by the arbitrator may be entered in any court of competent jurisdiction. Nothing in this Section shall prevent either party from seeking injunctive or other equitable relief from the courts for matters related to data security, intellectual property or unauthorized access to the Service. ALL CLAIMS MUST BE BROUGHT IN THE PARTIES\' INDIVIDUAL CAPACITY, AND NOT AS A PLAINTIFF OR CLASS MEMBER IN ANY PURPORTED CLASS OR REPRESENTATIVE PROCEEDING, AND, UNLESS WE AGREE OTHERWISE, THE ARBITRATOR MAY NOT CONSOLIDATE MORE THAN ONE PERSON\'S CLAIMS. YOU AGREE THAT, BY ENTERING INTO THESE TERMS, YOU AND WE ARE EACH WAIVING THE RIGHT TO A TRIAL BY JURY OR TO PARTICIPATE IN A CLASS ACTION.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>8. Governing Law and Jurisdiction</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">These Terms shall be governed by the laws of the State of California, without respect to its conflict of laws principles. We each agree to submit to the personal jurisdiction of a state court located in San Francisco County, California or the United States District Court for the Northern District of California, for any actions not subject to Section 10 (Arbitration).</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">Our Products are controlled and operated from the United States, and we make no representations that they are appropriate or available for use in other locations.</span></p>
<p><span style="font-size: 10pt;">&nbsp;</span></p>
<p><span style="font-size: 12pt;"><strong>9. General Terms</strong></span></p>
<p>&nbsp;</p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">Notification Procedures and changes to these Terms. We reserve the right to determine the form and means of providing notifications to you, and you agree to receive legal notices electronically if we so choose. We may revise these Terms from time to time and the most current version will always be posted on our website. If a revision, in our sole discretion, is material we will notify you. By continuing to access or use the Products after revisions become effective, you agree to be bound by the revised Terms. If you do not agree to the new terms, please stop using the Products.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">&nbsp;</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">Assignment. These Terms, and any rights and licenses granted hereunder, may not be transferred or assigned by you, but may be assigned by us without restriction. Any attempted transfer or assignment in violation hereof shall be null and void.</span></p>';

$static_page = $staticPageTable->createRow();
$static_page->owner_id = 1; 
$static_page->title = 'Terms of Services';
$static_page->short_url = 0;
$static_page->page_url = '';
$static_page->body = $body;
$static_page->params = 'a:0:{}';
$static_page->view_count = 0;
$static_page->creation_date = date();
$static_page->modified_date = date();
$static_page->menu = 3;
$static_page->level_id = '["0"]';
$static_page->networks = '["0"]';
$static_page->search =0;
$static_page->type = 1;
$static_page->meta_info = '';
$static_page->save();

$static_page = $staticPageTable->createRow();
$body = '<h3><span style="font-size: 14pt;"><strong>Privacy Policy</strong></span></h3>
<p style="text-align: justify;"><strong>&nbsp;</strong></p>
<p style="text-align: justify;"><span style="font-size: 10pt;">Thank you for using this site! We wrote this policy to help you understand what information we collect, how we use it, and what choices you have. Because we&rsquo;re an internet company, some of the concepts below are a little technical, but we&rsquo;ve tried our best to explain things in a simple and clear way. We welcome your questions and comments on this policy.</span></p>
<p style="text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="text-align: justify;"><span style="font-size: 12pt;"><strong>What information do we collect?</strong></span></p>
<p style="text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">We collect information in two ways:</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><strong><span style="font-size: 10pt;">1. When you give it to us or give us permission to obtain it.</span></strong></p>
<p style="text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">When you sign up for or use our products, you voluntarily give us certain information. This can include your name, profile photo, comments, likes, email address you used to sign up, and any other information you provide us. If you&rsquo;re using this site on your mobile device, you can also choose to provide us with location data.</span></p>
<p style="padding-left: 30px; text-align: justify;"><span style="font-size: 10pt;">You also may give us permission to access your information in other services. For example, you may link your Facebook or Twitter account, which allows us to obtain information from those accounts (e.g., your friends or contacts). The information we obtain from those services often depends on your settings or their privacy policies, so be sure to check what those are.</span></p>
<p style="padding-left: 30px; text-align: justify;">&nbsp;</p>
<h4 style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">2. We also get technical information when you use our products or use websites or apps that have features from this site.</span></h4>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">These days, whenever you use a website, mobile application, or other Internet service, there&rsquo;s certain information that almost always gets created and recorded automatically. The same is true when you use our products. Here are some of the types of information we collect:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.</strong> Log Data. When you use this site or go to a webpage or use an app that has features (like our &ldquo;Share&rdquo; button), our servers automatically record information (&ldquo;log data&rdquo;) including information that your browser sends whenever you visit a website or your mobile app sends when you&rsquo;re using it. This log data may include your Internet Protocol address, the address of the web pages you visited that had features from this site, browser type and settings, the date and time of your request, how you used this website, and cookie data.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> Cookie data. Depending on how you&rsquo;re accessing our products, we may use &ldquo;cookies&rdquo; (a small text file sent by your computer each time you visit our website, unique to your account or your browser), or similar technologies to record log data. When we use cookies, we may use &ldquo;session&rdquo; cookies (that last until you close your browser) or &ldquo;persistent&rdquo; cookies (that last until you or your browser delete them). For example, we may use cookies to store your language preferences or other settings so you don&lsquo;t have to set them up every time you visit this site. Some of the cookies we use are associated with your account (including personal information about you, such as the email address you gave us), and other cookies are not.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>c.</strong> Device Information. In addition to log data, we may also collect information about the device you&rsquo;re using this site on, including what type of device it is, what operating system you&rsquo;re using, device settings, unique device identifiers, and crash data. Whether we collect some or all of this information often depends on what type of device you&rsquo;re using and its settings. For example, different types of information are available depending on whether you&rsquo;re using a Mac or a PC, or an iPhone or an Android phone. To learn more about what information your device makes available to us, please also check the policies of your device manufacturer or software provider.</span></p>
<p>&nbsp;</p>
<p style="text-align: justify;"><strong><span style="font-size: 12pt;">How do we use the information we collect?</span></strong></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We use the information we collect to provide our products to you and make them better, develop new products, and protect our users. For example, we may log how often people use two different versions of a product, which can help us understand which version is better.</span></p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We also use the information we collect to offer you customized content, including:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.</strong> Suggesting content you might like. For example, if you&rsquo;ve indicated that you&rsquo;re interested in cooking or visited recipe websites that has features from this site, we may suggest food-related content, or people that we think you might like;</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> Showing you ads you might be interested in.</span></p>
<p style="text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We also use the information we collect to:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.&nbsp;</strong></span><span style="font-size: 10pt;">Send you updates (such as when certain activity, like shares or comments), newsletters, marketing materials and other information that may be of interest to you. For example, depending on your email notification settings, we may send you weekly updates that include posts you may like. You can decide to stop getting these updates by updating your account settings (or through other settings we may provide).</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> Help your friends and contacts find you on this site. For example, if you sign up using a Facebook account, we may help your Facebook friends find your account on this site when they first sign up. Or, we may allow people to search for your account using your email address.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>c.</strong> Respond to your questions or comments.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">The information we collect may be &ldquo;personally identifiable&rdquo; (meaning it can be used to specifically identify you as a unique person) or &ldquo;non-personally identifiable&rdquo; (meaning it can&rsquo;t be used to specifically identify you).</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify;"><strong><span style="font-size: 12pt;">What choices do you have about your information?</span></strong></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">Our goal is to give you simple and meaningful choices over your information. If you have an account, many of the choices you have on this are built directly into the product or your account settings. For example, you can:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.</strong> Access and change information in your profile page at any time.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> Link or unlink your account from an account on another service (e.g., Facebook or Twitter). For some services (like Facebook), you can also choose whether or not to publish your activity on to that service.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>c.</strong> Delete your account at any time.</span></p>
<p style="text-align: justify;"><span style="font-size: 10pt;"><strong>&nbsp;</strong></span></p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">You may have choices available to you through the device or software you use to access this site. For example:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.</strong> The browser you use may provide you with the ability to control cookies or other types of local data storage.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> Your mobile device may provide you with choices around how and whether location or other data is shared with us.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">To learn more about these choices, please see the information provided by the device or software provider.</span></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong><span style="font-size: 12pt;">How do we share the information we collect?</span></strong></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">People use this website to find their inspirations, make them a reality, and inspire others in the process. When you post public updates, anyone can view them. You may also provide us with profile page information that anyone can view. The other limited instances where we may share your personal information include:</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>a.</strong> When we have your consent. This includes sharing information with other services (like Facebook or Twitter) when you&rsquo;ve chosen to link to your account to those services or publish your activity to them. For example, you can choose to publish your posts to Facebook or Twitter.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>b.</strong> If we believe that disclosure is reasonably necessary to comply with a law, regulation or legal request; to protect the safety, rights, or property of the public, any person; or to detect, prevent, or otherwise address fraud, security or technical issues.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;"><strong>c.</strong> We may engage in a merger, acquisition, bankruptcy, dissolution, reorganization, or similar transaction or proceeding that involves the transfer of the information described in this Policy.</span></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We may also share aggregated or non-personally identifiable information with our partners or others.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify;"><strong><span style="font-size: 12pt;">Our Policy on Children&rsquo;s Information</span></strong></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We are not directed to children under 13. If you learn that your minor child has provided us with personal information without your consent, please contact us.</span></p>
<p style="text-align: justify; padding-left: 30px;">&nbsp;</p>
<p style="text-align: justify;"><strong><span style="font-size: 12pt;">How do we make changes to this policy?</span></strong></p>
<p>&nbsp;</p>
<p style="text-align: justify; padding-left: 30px;"><span style="font-size: 10pt;">We may change this policy from time to time, and if we do we&rsquo;ll post any changes on this page. If you continue to use this website after those changes are in effect, you agree to the revised policy. If the changes are significant, we may provide more prominent notice or obtain your consent as required by law.</span></p>';

$static_page->owner_id = 1; 
$static_page->title = 'Privacy Policy';
$static_page->short_url = 0;
$static_page->page_url = '';
$static_page->body = $body;
$static_page->params = 'a:0:{}';
$static_page->view_count = 0;
$static_page->creation_date = date();
$static_page->modified_date = date();
$static_page->menu = 3;
$static_page->level_id = '["0"]';
$static_page->networks = '["0"]';
$static_page->search =0;
$static_page->type = 1;
$static_page->meta_info = '';
$static_page->save();
?>