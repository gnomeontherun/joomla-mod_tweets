<?php
/*
 * File: tmpl/default.php
 * File Description: This file handles the view of the tweets, and displays them.
 */

defined('_JEXEC') or die('Direct Access is forbidden, shame on you');

if (isset($tweets->error)) 
{
    //echo "<div class=\"message\">" . $tweets->error . "</div>";
} 
else 
{
    $date = $params->get('date', 'ago');
    $format = $params->get('format', 'd.m.y H\:m'); 
?>
	
<div class="<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
    
	<?php if ($params->get('beforetext', '') !== '') :
        ?>
        <div class="beforeTweets">
        <?php print $params->get('beforetext', ''); ?>
        </div>
        <?php endif; ?>
    <ul class="tweets">
        <?php foreach ($tweets as $tweet) : 
		$tweet->text = preg_replace('#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#', '<a href=\'\\0\'>\\0</a>', $tweet->text);
		$tweet->text = preg_replace('/@(.+?)\b/', "<a href=\"https://twitter.com/#!/$1\">@$1</a>", $tweet->text);
		$tweet->screen_name = (isset($tweet->user->screen_name)) ? $tweet->user->screen_name : $tweet->from_user;
		?>
		<li>
			<span class="tweet_author"><a href="https://twitter.com/#!/<?php echo $tweet->screen_name; ?>">@<?php echo $tweet->screen_name; ?></a></span>: 
			<?php echo $tweet->text; ?><br />
			<span class="tweet_time"><?php echo modTweetsHelper::timeDifference(strtotime($tweet->created_at)); ?></span> - 
			<a href="https://twitter.com/#!/<?php echo $tweet->screen_name; ?>/status/<?php echo $tweet->id_str; ?>"<?php echo ($params->get('linktype') == 'blank') ? ' target="_blank"' : ''; ?>><?php echo JText::_('MOD_TWEETS_VIEW'); ?> &raquo;</a></span>
		</li>
		<?php endforeach; ?>
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