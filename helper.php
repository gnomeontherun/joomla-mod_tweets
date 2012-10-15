<?php

/*
 * Module: Tweets Modules
 * Author: Jeremy Wilken @ Gnome on the run
 * Author URL: www.gnomeontherun.com
 * License: GNU GPL v2
 *
 */

defined('_JEXEC') or die('Direct Access is forbidden, shame on you');

class modTweetsHelper
{

    function getTweets($params)
	{
		// Initialize variables and set defaults
        $cache = dirname(__FILE__) . '/cache/';
		if (!is_dir($cache)) JFolder::create($cache, 755);
        $user = $params->get('user', 'gnomeontherun');
		$type = $params->get('type', 'user');
        $quantity = $params->get('quantity', '3');
        $cachetime = $params->get('cachetime', '30') * 60;
		$search = urlencode($params->get('query', 'joomla'));
		if ($quantity > 20 || $quantity < 0) {
            $quantity = 5;
        }

		if ($type == 'search')
		{
			$tweetURL = 'https://search.twitter.com/search.json?q=' . $search . '&rpp=' . $quantity . '&result_type=recent';
			$cachefile = JFile::makeSafe($search) . '.json';
		}
		else
		{
			$tweetURL = 'https://search.twitter.com/search.json?q=from:' . $user . '&rpp=' . $quantity . '&result_type=recent';
			$cachefile = $user . '.json';
		}
        $cachepathfile = $cache . $cachefile;

		// Check if cached version is uptodate or not
		if (!file_exists($cachepathfile) || (time() - $cachetime) > filemtime($cachepathfile))
		{
			// If curl is enabled, use it
			if (in_array('curl', get_loaded_extensions()))
			{
				$ch = curl_init($tweetURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$file = curl_exec($ch);
				curl_close($ch);
			}
			// Otherwise try to load it with file stream
			else
			{
				$file = @file_get_contents($tweetURL);
			}

			$tweets = json_decode($file);

			if (!$file)
			{
				// Unable to download a file
				$tweets->error = JText::_('MOD_TWEETS_ERROR_COULD_NOT_DOWNLOAD');
			}

			if (count($tweets) && !isset($tweets->error))
			{
				JFile::write($cachepathfile, $file);
			}
			else
			{
				// Throw error about not getting a good file from twitter
				$tweets->error = JText::_('MOD_TWEETS_ERROR_TWITTER_NO_TWEETS');
			}
        }

		// If cached, load the file and decode json
		if (!isset($tweets))
		{
			$file = JFile::read($cachepathfile);
			$tweets = json_decode($file);
		}

		$tweets = $tweets->results;

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