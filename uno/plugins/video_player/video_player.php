<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
include('../../password.php'); $user=0; $pass=0; // Lang
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<div class="blocForm">
			<h2><?php echo _("Video Player");?></h2>
			<p><?php echo _("This plugin allows you to insert a video into the content's page. The video should be accessible from the File manager.");?></p>
			<p><?php echo _("It is used with the button") .'<img src="uno/plugins/video_player/video/images/icon.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("added to the text editor when the plugin is enable.");?></p>
			<p><?php echo _("To work in all media, the video should be available in mp4 and .webm.");?></p>
			<p><?php echo _("The .mp4 must be encoded in H.264 for video and AAC for audio.");?></p>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
