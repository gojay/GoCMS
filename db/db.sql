-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 28, 2012 at 11:50 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `go_cms`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `go_contents`
-- 

CREATE TABLE `go_contents` (
  `content_id` bigint(20) unsigned NOT NULL auto_increment,
  `content_parent` tinyint(3) unsigned default '0',
  `content_type` varchar(255) NOT NULL,
  `content_name` varchar(255) NOT NULL,
  `content_slug` varchar(255) NOT NULL,
  `content_image` varchar(255) default NULL,
  `content_description` text NOT NULL,
  `content_order` tinyint(4) default '0',
  `content_date` datetime NOT NULL,
  `content_status` char(1) NOT NULL,
  PRIMARY KEY  (`content_id`),
  UNIQUE KEY `content_id` (`content_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- 
-- Dumping data for table `go_contents`
-- 

INSERT INTO `go_contents` (`content_id`, `content_parent`, `content_type`, `content_name`, `content_slug`, `content_image`, `content_description`, `content_order`, `content_date`, `content_status`) VALUES 
(1, 0, 'page', 'About Us', 'about-us', '', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 2, '2012-05-23 10:32:25', 'L'),
(2, 1, 'page', 'History', 'history', '', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 0, '2012-05-23 10:32:54', 'L'),
(3, 1, 'page', 'Phylosophy', 'phylosophy', '', '', 0, '2012-05-23 10:33:20', 'L'),
(4, 1, 'page', 'Mission', 'mission', '', '', 0, '2012-05-23 10:33:37', 'L'),
(5, 1, 'page', 'Values', 'values', '', '<h2>Teamwork</h2>\r\n<p>&quot;Coming together is beginning. keeping together is  progress. Working together is success.&quot; &ndash; Henry Ford In brand activation, there is no success without teamwork. Therefore  every division in our team holds high commitment towards group effort.<br />\r\nWe also work together with our clients as partners, which enabled us to  understand their products and services, their target market, their  expectation and their goal.</p>\r\n<p align="center"><img src="http://localhost/dgo/public/uploads/image/img.png" alt="" /></p>\r\n<h2>Efficient</h2>\r\n<p>&quot;The combination of hard work and smart work is efficient work&quot; &ndash; Robert  Half We always strive to understand what our client''s needs to provide the  best alternative solution for Their communication strategy in the most  cost efficient way, without reducing the impact we deliver to your  potential audience.</p>\r\n<h2>Target Driven</h2>\r\n<p>&quot;Being Target Driven can create a higher motivation in works.&quot; &ndash; unknown Every project has its own goals to achieve. We always aim to make those  gools a really by creating marketing strategy that is practical and  achievable. Each member of our team has the experience, professionalism,  energy and drive to exceed targets.</p>\r\n<h2>Agility</h2>\r\n<p>&quot;Agility &ndash; is theme that runs through almost everything we''re taking  about these day. We can''t know what future''s going to look like. We have  to be ogle enough to be able to respond to that uncertainty.&quot; &ndash; Linton  Wells II<br />\r\nWe execute our work from a meticulous planning. But when encountered  with opportunities and challenges we reach our potential by being  flexible. That way we can respond to changes quickly to deliver what our  client needs.</p>', 0, '2012-05-23 10:33:45', 'L'),
(6, 1, 'page', 'Our People', 'our-people', NULL, '<p>Our people make all the differences. We are passionate about what we do, well versed in all aspect of our business, and thrive on the success of our clients.</p>', 0, '2012-05-23 10:34:02', 'L'),
(7, 1, 'page', 'Strategic Partner', 'strategic-partner', '', '', 0, '2012-05-23 10:34:16', 'L'),
(8, 0, 'page', 'Program', 'program', NULL, '', 0, '2012-05-23 10:34:41', 'D'),
(9, 0, 'page', 'Services', 'services', NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 3, '2012-05-23 10:34:52', 'L'),
(10, 9, 'page', 'Email Marketing', 'email-marketing', NULL, '', 0, '2012-05-23 10:36:54', 'L'),
(11, 0, 'post', 'News 1', 'news-1', 'news-1.png', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 0, '2012-05-23 11:41:06', 'L'),
(12, 0, 'post', 'News 2', 'news-2', NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<span class="page-break"><!--more--></span></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br />\r\n<br />\r\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 0, '2012-05-23 11:59:10', 'L'),
(13, 0, 'page', 'Portfolio', 'portfolio', '', '', 4, '2012-05-24 10:34:37', 'L'),
(16, 0, 'portfolio', 'Portfolio 1', 'portfolio-1', 'portfolio-1.png', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie.</p>\r\n<p>Duis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.</p>', 0, '2012-05-24 11:29:00', 'L'),
(17, 0, 'portfolio', 'Portfolio 2', 'portfolio-2', 'portfolio-2.png', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie.<br />\r\n<br />\r\nDuis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.</p>', 0, '2012-05-24 11:30:06', 'L'),
(18, 0, 'page', 'Home', 'home', NULL, '', 1, '2012-05-25 19:08:34', 'L'),
(19, 0, 'page', 'News', 'news', NULL, '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie.</p>\r\n<p>Duis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.</p>', 5, '2012-05-25 19:27:49', 'L'),
(20, 0, 'page', 'Contact', 'contact', NULL, '', 6, '2012-05-25 19:28:32', 'L'),
(21, 0, 'portfolio', 'Portfolio 3', 'portfolio-3', 'portfolio-3.png', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie.</p>\r\n<p>Duis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.</p>', 0, '2012-05-25 21:25:28', 'L'),
(22, 9, 'page', 'Direct to Client', 'direct-to-client', NULL, '', 0, '2012-05-25 22:16:53', 'L'),
(23, 9, 'page', 'In-Store Marketing', 'in-store-marketing', NULL, '', 0, '2012-05-25 22:17:15', 'L'),
(24, 9, 'page', 'Sub Contractor to BTL', 'sub-contractor-to-btl', NULL, '', 0, '2012-05-25 22:17:37', 'L'),
(26, 6, 'people', 'Irke Silvany Riasanty', 'irke-silvany-riasanty', NULL, '<p><img width="95" height="110" align="left" src="http://localhost/dgo/public/uploads/image/img2.png" alt="" />Centralize all your customer conversations so nothing gets ignored and everything is searchable from one place. Easily organize, prioritize and engage with others on support requests to ensure your customers get accurate and timely responses. But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure<span class="page-break"><!--more--></span></p>\r\n<p>Centralize all your customer conversations so nothing gets ignored and everything is searchable from one place. Easily organize, prioritize and engage with others on support requests to ensure your customers get accurate and timely responses. But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure</p>\r\n<p>Centralize all your customer conversations so nothing gets ignored and everything is searchable from one place. Easily organize, prioritize and engage with others on support requests to ensure your customers get accurate and timely responses. But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure</p>', 0, '2012-05-27 19:24:42', 'L'),
(27, 6, 'people', 'Hulaifi Lutfi', 'hulaifi-lutfi', NULL, '<p><img width="95" height="99" align="left" alt="" src="http://localhost/dgo/public/uploads/image/img3.png" />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie.<span class="page-break"><!--more--></span></p>\r\n<p>Duis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.</p>', 0, '2012-05-27 19:35:54', 'L');

-- --------------------------------------------------------

-- 
-- Table structure for table `go_content_images`
-- 

CREATE TABLE `go_content_images` (
  `image_id` bigint(20) unsigned NOT NULL auto_increment,
  `content_id` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`image_id`),
  UNIQUE KEY `image_id` (`image_id`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `go_content_images`
-- 

INSERT INTO `go_content_images` (`image_id`, `content_id`, `filename`) VALUES 
(1, 16, 'portfolio-1-detailgallery.png'),
(2, 16, 'portfolio-1-detailgallery2.png'),
(3, 16, 'portfolio-1-detailgallery3.png');

-- --------------------------------------------------------

-- 
-- Table structure for table `go_content_profiles`
-- 

CREATE TABLE `go_content_profiles` (
  `profile_id` bigint(20) unsigned NOT NULL auto_increment,
  `profile_name` varchar(255) NOT NULL,
  `profile_value` text NOT NULL,
  PRIMARY KEY  (`profile_id`,`profile_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- 
-- Dumping data for table `go_content_profiles`
-- 

INSERT INTO `go_content_profiles` (`profile_id`, `profile_name`, `profile_value`) VALUES 
(26, 'file_name', 'irke-silvany-riasanty.pdf'),
(26, 'file_type', 'pdf'),
(27, 'file_name', 'hulaifi-lutfi.doc'),
(27, 'file_type', 'doc');

-- --------------------------------------------------------

-- 
-- Table structure for table `go_options`
-- 

CREATE TABLE `go_options` (
  `option_id` bigint(20) unsigned NOT NULL auto_increment,
  `option_name` varchar(255) NOT NULL,
  `option_value` text NOT NULL,
  PRIMARY KEY  (`option_id`,`option_name`),
  UNIQUE KEY `option_id` (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `go_options`
-- 

INSERT INTO `go_options` (`option_id`, `option_name`, `option_value`) VALUES 
(1, 'option_general', 'a:9:{s:5:"email";s:14:"admin@go.com";s:9:"show_news";s:1:"5";s:15:"show_portfolios";s:1:"9";s:10:"front_page";s:4:"home";s:9:"news_page";s:4:"news";s:15:"portfolios_page";s:9:"portfolio";s:6:"g_mail";s:20:"dani.gojay@gmail.com";i:0;a:1:{s:10:"g_password";s:88:"LiuBLvjlay8OEiLOZ+H+gcGAf++SKOCLxBPZCSCwCULxw/QnUWiNJsayBu2yAU+TCoHMNcJLK1/NHDNcIZNSuw==";}s:13:"ga_profile_id";i:54153050;}'),
(2, 'option_social', 'a:6:{s:8:"facebook";s:8:"facebook";s:7:"twitter";s:7:"twitter";s:20:"twitter_access_token";s:0:"";s:27:"twitter_access_token_secret";s:0:"";s:13:"twitter_count";s:1:"1";s:8:"linkedin";s:8:"linkedin";}'),
(3, 'option_office', 'a:1:{i:0;a:4:{s:7:"address";s:53:"Jl. Mendawai I No. 40A Kebayoran Baru Jakarta Selatan";s:5:"phone";s:13:"021 - 7222337";s:3:"fax";s:13:"021 - 7253055";s:5:"email";s:0:"";}}'),
(4, 'option_analytics', 'a:3:{s:6:"g_mail";s:20:"dani.gojay@gmail.com";s:10:"g_password";s:44:"5LBWxGSWxR96II85qeVvjUAMd0ip+NyCyvdV8W2i6+I=";s:13:"ga_profile_id";i:54153050;}'),
(5, 'gallery_slide', 'a:3:{i:0;s:15:"slide_slide.png";i:1;s:17:"slide_1_slide.png";i:2;s:17:"slide_1_slide.png";}');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `go_content_images`
-- 
ALTER TABLE `go_content_images`
  ADD CONSTRAINT `go_content_images_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `go_contents` (`content_id`);

-- 
-- Constraints for table `go_content_profiles`
-- 
ALTER TABLE `go_content_profiles`
  ADD CONSTRAINT `go_content_profiles_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `go_contents` (`content_id`);
