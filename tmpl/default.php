<?php
/*
 * File: tmpl/default.php
 * File Description: This file handles the view of the tweets, and displays them.
 */

defined('_JEXEC') or die('Direct Access is forbidden, shame on you');

if ($tweets['error']) {
    echo "<div class=\"message\">" . $tweets . "</div>";
} else {
    $date = $params->get('date', 'ago');
    $format = $params->get('format', 'd.m.y H\:m'); ?>
	
<div class="<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
    
	<?php if ($params->get('beforetext', '') !== '') :
        ?>
        <div class="beforeTweets">
        <?php print $params->get('beforetext', ''); ?>
        </div>
        <?php endif; ?>
    <ul class="tweets">
        <?php
        $i = 0;
        foreach ($tweets as $tweetData) {
            if ($i != 0) {
                $tweet = $tweetData['tweet'];
                $pubDate = strtotime($tweetData['pubDate']);
                $link = $tweetData['link'];

                $exp = "/@(.+?)\b/";
                $tweet = preg_replace($exp, "<a href=\"http://twitter.com/$1\">@$1</a>", $tweet);

                if ($date == "time") {
                    $pub = date($format, $pubDate);
                } else {
                    $pub = modTweetsHelper::timeDifference($pubDate);
                }
                ?>
                <li><?php echo $tweet; ?><br /><span class="tweet_time"><?php echo $pub; ?> - <a href="<?php echo $link; ?>"<?php echo ($params->get('linktype') == 'blank') ? ' target="_blank"' : ''; ?>><?php echo JText::_('MOD_TWEETS_VIEW'); ?> &raquo;</a></span></li>
                <?php
            } else {
                $i++;
            }
        }
        ?>

    </ul>
    <?php if ($params->get('aftertext', '') !== '') : ?>
        <div class="afterTweets">
            <?php print $params->get('aftertext', ''); ?>
        </div>
    <?php endif; ?>
</div>
    <?php
}
?>