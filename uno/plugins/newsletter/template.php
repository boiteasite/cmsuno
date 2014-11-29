<?php
// ********************************* A PERSONNALISER **********************
// https://github.com/leemunroe/html-email-template
function f_top()
	{
	$racine = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']).'/../../';
	$t = '<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
*{margin:0;padding:0;font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6;}
img{max-width:100%;}
body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%!important;height:100%;}
a{color:#348eda;}
.last{margin-bottom:0;}
.first{margin-top:0;}
.padding{padding:10px 0;}
table.body-wrap{width:100%;padding:20px;}
table.body-wrap .container{border:1px solid #f0f0f0;}
table.footer-wrap{width:100%;clear:both!important;}
.footer-wrap .container p{font-size:12px;color:#666;}
table.footer-wrap a{color:#999;}
h1,h2,h3{font-family:"Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;line-height:1.1;color:#000;margin:15px 0 10px;line-height:1.2;font-weight:400;}
h1{font-size:36px;}
h2{font-size:28px;}
h3{font-size:22px;}
p,ul,ol{margin-bottom:10px;font-weight:normal;font-size:14px;}
ul li,ol li{margin-left:5px;list-style-position:inside;}
.container{display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important;}
.body-wrap .container{padding: 20px;}
.content{max-width:600px;margin:0 auto;display: block;}
.content table{width:100%;}
</style>
</head>
<body bgcolor="#f6f6f6">
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">
			<div class="content">
			<table>
				<tr>
					<td>';
	return $t;
	}
// ********************************
function f_bottom()
	{
	$b = '
					</td>
				</tr>
			</table>
			</div>
		</td>
		<td></td>
	</tr>
</table>
<table class="footer-wrap">
	<tr>
		<td></td>
		<td class="container">
			<div class="content">
				<table>
					<tr>
						<td align="center">
							<p>[[unsubscribe]]</p>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td></td>
	</tr>
</table>
</body>
</html>';
	return $b;
	}
// ********************************
$top = f_top();
$bottom = f_bottom();