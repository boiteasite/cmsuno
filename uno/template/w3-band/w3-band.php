<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
// ********************* actions *************************************************************************
if(isset($_POST['action']))
	{
	switch($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<div class="blocForm">
			<h2>W3 Band</h2>
			<p>This responsive W3.CSS website templates was created by <a href="https://www.w3schools.com/w3css/w3css_templates.asp">www.w3schools.com</a>.</p>
			<p>It has been adapted to be used with CMSUno. You should :</p>
			<ul style="list-style-type:circle;margin:10px 40px 20px;">
				<li>Load w3.css (see Config),</li>
				<li>Add the <span style="font-weight:700">Box</span> plugin and create a block named <span style="font-weight:700">footer</span> to fill the footer of your site,</li>
				<li>Add the <span style="font-weight:700">Transition</span> plugin to customize each section of your site.</li>
				<li>Add the <span style="font-weight:700">Model</span> plugin to create columns.</li>
			</ul>
			<p>Examples of customization with <span style="font-weight:700">Transition</span> :</p>
			<p>DIV CLASS : <span style="color:#4F6F6F">w3-black</span> - Sub-DIV CLASS : <span style="color:#4F6F6F">w3-container w3-content</span> - Sub-DIV STYLE : <span style="color:#4F6F6F">max-width:800px;</span></p>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		}
	}
?>
