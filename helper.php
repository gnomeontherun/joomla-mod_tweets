<?php

/*
 * Module: Tweets Modules
 * Author: Jeremy Wilken @ Gnome on the run
 * Author URL: www.gnomeontherun.com
 * License: GNU GPL
 * Module Description: This module takes the rss feed from a user's public Twitter tweets and displays them.
 *
 * File: helper.php
 * File Description: This file gets the Twitter feed, parses it, and returns a multidiminsional array with tweet, date, and link.
 */

defined('_JEXEC') or die('Direct Access is forbidden, shame on you');

class modTweetsHelper {

    function getTweets($params) {
        $tweets = array();
        $tweets['error'] = false;

        $cache = JPATH_BASE . DS . 'cache';
        if (!is_writable($cache))
            return $tweets['error'] = 'Cache folder is unwriteable. Solution: chmod 755 ' . $cache;

        $user = $params->get('user', 'gnomeontherun');
        $quantity = $params->get('quantity', '3');
        $cachetime = $params->get('cachetime', '30') * 60;
        $subtract = strlen($user) + 2;
        $tweetURL = "http://twitter.com/statuses/user_timeline/" . $user . ".rss";
        $cachefile = $user . ".xml";
        $cachepathfile = $cache . DS . $cachefile;

        if (!file_exists($cachepathfile) || (time() - $cachetime) > filemtime($cachepathfile)) {
            $file = @file_get_contents($tweetURL);
            if ($file)
                file_put_contents($cachepathfile, $file);
            else
                return $tweets['error'] = "Unable to get latest tweets at this time.";
        }

        if ($quantity > 20 || $quantity < 0) {
            $quantity = 5;
        }

        $twitter = simplexml_load_file($cachepathfile);

        if (!$twitter)
            return $tweets['error'] = "Unable to get latest tweets at this time.";

        // Ready for some exciting dishing out
        $i = 0;
        // For each slice of pie we've got to get the goods
        foreach ($twitter->xpath('channel/item') as $tweet) {
            if ($i < $quantity) {
                $tweetTitle = $tweet->title[0];
                $tweetTitle = substr($tweetTitle, $subtract);
                $tweetTitle = preg_replace("#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#", "<a href=\"\\0\">\\0</a>", $tweetTitle);
                $tweets[$i]["tweet"] = $tweetTitle;
                $tweets[$i]["pubDate"] = $tweet->pubDate[0];
                $tweets[$i]["link"] = $tweet->link[0];
                $i++;
            }
        }

        return $tweets;
    }

    function timeDifference($timestamp) {
        $d = time() - $timestamp;
        if ($d < 60)
            return JText::sprintf('MOD_TWEETS_AGO', $d, ($d == 1) ? JText::_('MOD_TWEETS_SECOND') : JText::_('MOD_TWEETS_SECONDS'));
        else {
            $d = floor($d / 60);
            if ($d < 60)
                return JText::sprintf('MOD_TWEETS_AGO', $d, ($d == 1) ? JText::_('MOD_TWEETS_MINUTE') : JText::_('MOD_TWEETS_MINUTES'));
            else {
                $d = floor($d / 60);
                if ($d < 24)
                    return JText::sprintf('MOD_TWEETS_AGO', $d, ($d == 1) ? JText::_('MOD_TWEETS_HOUR') : JText::_('MOD_TWEETS_HOURS'));
                else {
                    $d = floor($d / 24);
                    if ($d < 7)
                        return JText::sprintf('MOD_TWEETS_AGO', $d, ($d == 1) ? JText::_('MOD_TWEETS_DAY') : JText::_('MOD_TWEETS_DAYS'));
                    else {
                        $d = floor($d / 7);
                        return JText::sprintf('MOD_TWEETS_AGO', $d, ($d == 1) ? JText::_('MOD_TWEETS_WEEK') : JText::_('MOD_TWEETS_WEEKS'));
                    }//Week
                }//Day
            }//Hour
        }//Minute
    }

}