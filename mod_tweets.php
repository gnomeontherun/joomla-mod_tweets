<?php
/*
* Module: Tweets Modules
* Author: Jeremy Wilken @ Gnome on the run
* Author URL: www.gnomeontherun.com
* License: GNU GPL
* Module Description: This module takes the rss feed from a user's public Twitter tweets and displays them.
*
* File: mod_tweets.php
* File Description: This file is the model for the module, and controls the program.
*/

defined('_JEXEC') or die ('Direct Access is forbidden, shame on you');

require_once( dirname(__FILE__).DS.'helper.php');

$tweets = modTweetsHelper::getTweets($params);

require_once(JModuleHelper::getLayoutPath('mod_tweets'));

?>