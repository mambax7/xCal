<?php
require_once 'header.php';
define('BASE','./');
include(BASE.'functions/ical_parser.php');

if ($cookie_uri == '') {
	$cookie_uri = $HTTP_SERVER_VARS['SERVER_NAME'].substr($HTTP_SERVER_VARS['PHP_SELF'],0,strpos($HTTP_SERVER_VARS['PHP_SELF'], '/'));
}
$current_view = "preferences";
$default_view = "$default_view" . ".php";
if ($allow_preferences == 'no') header("Location: $default_view");
$action = $HTTP_GET_VARS['action'];
$startdays = array ('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

if ($action == 'setcookie') { 
	$cookie_language 	= $HTTP_POST_VARS['cookie_language'];
	$cookie_calendar 	= $HTTP_POST_VARS['cookie_calendar'];
	$cookie_view 		= $HTTP_POST_VARS['cookie_view'];
	$cookie_style 		= $HTTP_POST_VARS['cookie_style'];
	$cookie_startday	= $HTTP_POST_VARS['cookie_startday'];
	$cookie_time		= $HTTP_POST_VARS['cookie_time'];
	$cookie_unset		= $HTTP_POST_VARS['unset'];
	$the_cookie = array ("cookie_language" => "$cookie_language", "cookie_calendar" => "$cookie_calendar", "cookie_view" => "$cookie_view", "cookie_startday" => "$cookie_startday", "cookie_style" => "$cookie_style", "cookie_time" => "$cookie_time");
	$the_cookie 		= serialize($the_cookie);
	if ($cookie_unset) { 
		setcookie("phpicalendar","$the_cookie",time()-(60*60*24*7) ,"/","$cookie_uri",0);
	} else {
		setcookie("phpicalendar","$the_cookie",time()+(60*60*24*7*12*10) ,"/","$cookie_uri",0);
	}
	$HTTP_COOKIE_VARS['phpicalendar'] = $the_cookie;
}

if ($HTTP_COOKIE_VARS['phpicalendar']) {
	$phpicalendar 		= unserialize(stripslashes($HTTP_COOKIE_VARS['phpicalendar']));
	$cookie_language 	= $phpicalendar['cookie_language'];
	$cookie_calendar 	= $phpicalendar['cookie_calendar'];
	$cookie_view 		= $phpicalendar['cookie_view'];
	$cookie_style 		= $phpicalendar['cookie_style'];
	$cookie_startday	= $phpicalendar['cookie_startday'];
	$cookie_time		= $phpicalendar['cookie_time'];
	if ($cookie_unset) { 
		unset ($cookie_language, $cookie_calendar, $cookie_view, $cookie_style,$cookie_startday);
	}
}
#echo "$cookie_uri";
#print_r(unserialize($HTTP_COOKIE_VARS['phpicalendar']));
#print_r($phpicalendar);


if ((!isset($HTTP_COOKIE_VARS['phpicalendar'])) || ($cookie_unset)) {
	# No cookie set -> use defaults from config file.
	$cookie_language = ucfirst($language);
	$cookie_calendar = $default_cal;
	$cookie_view = $default_view;
	$cookie_style = $style_sheet;
	$cookie_startday = $week_start_day;
	$cookie_time = $day_start;
}

$back_page = BASE.$default_view.'.php?cal='.$cal.'&amp;getdate='.$getdate;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><?php echo "$cal $calendar_lang - $preferences_lang"; ?></title>
  	<link rel="stylesheet" type="text/css" href="<?php echo BASE."styles/$style_sheet/default.css"; ?>">
</head>
<body bgcolor="#FFFFFF">
<?php include (BASE.'includes/header.inc.php'); ?>
<center>
<table border="0" width="700" cellspacing="0" cellpadding="0">
	<tr>
		<td width="520" valign="top" align="center">
			<table width="640" border="0" cellspacing="0" cellpadding="0" class="calborder">
				<tr>
					<td align="center" valign="middle">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="120" class="navback"><?php echo '<a href="'.$back_page.'"><img src="'.BASE.'styles/'.$style_sheet.'/back.gif" alt=" " border="0" align="left"></a>'; ?></td>
								<td class="navback">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td align="center" class="navback" nowrap valign="middle"><font class="H20"><?php echo "$preferences_lang"; ?></font></td>
										</tr>
									</table>
								</td>
								<td align="right" width="120" class="navback">	
									<table width="120" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td><?php echo '<a class="psf" href="'.BASE.'day.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="'.BASE.'styles/'.$style_sheet.'/day_on.gif" alt=" " border="0"></td>'; ?>
											<td><?php echo '<a class="psf" href="'.BASE.'week.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="'.BASE.'styles/'.$style_sheet.'/week_on.gif" alt=" " border="0"></td>'; ?>
											<td><?php echo '<a class="psf" href="'.BASE.'month.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="'.BASE.'styles/'.$style_sheet.'/month_on.gif" alt=" " border="0"></td>'; ?>
											<td><?php echo '<a class="psf" href="'.BASE.'year.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="'.BASE.'styles/'.$style_sheet.'/year_on.gif" alt=" " border="0"></td>'; ?>
										</tr>
									</table>
								</td>
							</tr>
			      		</table>
					</td>
				</tr>
				<tr>
					<td class="dayborder"><img src="images/spacer.gif" width="1" height="5" alt=" "></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="G10B">	
							<?php
							if ($action == 'setcookie') { 
								if (!$cookie_unset) {
									$message = $prefs_set_lang;
								} else {
									$message = $prefs_unset_lang;
								}
							?>
							<tr>
								<td colspan="2" align="center"><font class="G10BOLD"><?php echo "$message"; ?></font></td>
							</tr>
							<?php } ?>
							<tr>
								<td width="2%"></td>
								<td width="98%" valign="top" align="left">
								<form action="preferences.php?action=setcookie" METHOD="post">
								<table border="0" width="620" cellspacing="2" cellpadding="2" align="center">
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_lang_lang"; ?></td>
										<td align="left" valign="top" width="20"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top" width="300">
											<?php
											
												// Begin Language Selection
												//
												print "<select name=\"cookie_language\" class=\"query_style\">\n";
												$dir_handle = @opendir(BASE.'languages/');
												$tmp_pref_language = urlencode(ucfirst($language));
												while ($file = readdir($dir_handle)) {
													if (substr($file, -8) == ".inc.php") {
														$language_tmp = urlencode(ucfirst(substr($file, 0, -8)));
														if ($language_tmp == $cookie_language) {
															print "<option value=\"$language_tmp\" selected>$language_tmp</option>\n";
														} else {
															print "<option value=\"$language_tmp\">$language_tmp</option>\n";
														}
													}
												}
												closedir($dir_handle);
												print "</select>\n";
											?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_cal_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top">
											<?php
											
												// Begin Calendar Selection
												//
												print "<select name=\"cookie_calendar\" class=\"query_style\">\n";
												$dir_handle = @opendir($calendar_path) or die(error(sprintf($error_path_lang, $calendar_path), $cal_filename));
												$filelist = array();
												while ($file = readdir($dir_handle)) {
													if (substr($file, -4) == ".ics") {
														array_push($filelist, $file);
													}
												}
												natcasesort($filelist);
												foreach ($filelist as $file) {
													$cal_filename_tmp = substr($file,0,-4);
													$cal_tmp = urlencode($cal_filename_tmp);
													$cal_displayname_tmp = str_replace("32", " ", $cal_filename_tmp);
													if (!in_array($cal_filename_tmp, $blacklisted_cals)) {
														if ($cal_tmp == $cookie_calendar) {
															print "<option value=\"$cal_tmp\" selected>$cal_displayname_tmp $calendar_lang</option>\n";
														} else {
															print "<option value=\"$cal_tmp\">$cal_displayname_tmp $calendar_lang</option>\n";	
														}		
													}	
												}			
												foreach($list_webcals as $cal_tmp) {
													if ($cal_tmp != '') {
														$cal_displayname_tmp = basename($cal_tmp);
														$cal_displayname_tmp = str_replace("32", " ", $cal_displayname_tmp);
														$cal_displayname_tmp = substr($cal_displayname_tmp,0,-4);
														$cal_encoded_tmp = urlencode($cal_tmp);
														if ($cal_tmp == $cal_httpPrefix || $cal_tmp == $cal_webcalPrefix) {
															print "<option value=\"$cal_encoded_tmp\" selected>$cal_displayname_tmp Webcal</option>\n";
														} else {
															print "<option value=\"$cal_encoded_tmp\">$cal_displayname_tmp Webcal</option>\n";	
														}		
													}
												}
												closedir($dir_handle);
												print "</select>\n";
											?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_view_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top">
											<?php
											
												// Begin View Selection
												//
												print "<select name=\"cookie_view\" class=\"query_style\">\n";
												print "<option value=\"day\"";
												if ($cookie_view == "day") print " selected";
												print ">$day_lang</option>\n";
												print "<option value=\"week\"";
												if ($cookie_view == "week") print " selected";
												print ">$week_lang</option>\n";
												print "<option value=\"month\"";
												if ($cookie_view == "month") print " selected";
												print ">$month_lang</option>\n";
												//print "<option value=\"print\"";
												//if ($cookie_view == "print") print " selected";
												//print ">$printer_lang</option>\n";
												print "</select>\n";
											?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_time_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top">
											<?php
											
												// Begin Time Selection
												//
												print "<select name=\"cookie_time\" class=\"query_style\">\n";
												for ($i = 500; $i <= 900; $i += 100) {
													$s = sprintf("%04d", $i);
													print "<option value=\"$s\"";
													if ($s == $cookie_time) {
														print " selected";
													}
													print ">$s</option>\n";
												}
												print "</select>\n";
											?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_day_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top">
											<?php
												
												// Begin Day Start Selection
												//
												print "<select name=\"cookie_startday\" class=\"query_style\">\n";
												$i=0;
												foreach ($daysofweek_lang as $daysofweek) {
													if ($startdays[$i] == "$cookie_startday") {
														print "<option value=\"$startdays[$i]\" selected>$daysofweek</option>\n";
													} else {
														print "<option value=\"$startdays[$i]\">$daysofweek</option>\n";
													}
													$i++;
												}
												print "</select>\n";
											?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" width="300" nowrap><?php echo "$select_style_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top">
											<?php
											
												// Begin Style Selection
												//
												print "<select name=\"cookie_style\" class=\"query_style\">\n";
												$dir_handle = @opendir(BASE.'styles/');
												while ($file = readdir($dir_handle)) {
													if (($file != ".") && ($file != "..") && ($file != "CVS")) {
														if (!is_file($file)) {
															$file_disp = ucfirst($file);
															if ($file == "$cookie_style") {
																print "<option value=\"$file\" selected>$file_disp</option>\n";
															} else {
																print "<option value=\"$file\">$file_disp</option>\n";
															}
														}
													}
												}
												closedir($dir_handle);
												print "</select>\n";
											?>
										</td>
									</tr>
									<?php if ($HTTP_COOKIE_VARS['phpicalendar']) { ?>
									<tr>
										<td align="left" valign="top" nowrap><?php echo "$unset_prefs_lang"; ?></td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top"><INPUT TYPE="checkbox" NAME="unset" VALUE="true"></td>
									</tr>
									<?php } ?>
									<tr>
										<td align="left" valign="top" nowrap>&nbsp;</td>
										<td align="left" valign="top"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0"></td>
										<td align="left" valign="top"><input type="submit" name="set" value="<?php echo "$set_prefs_lang"; ?>"></button></td>
									</tr>
								</table>
								</form>
								<br>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include (BASE.'includes/footer.inc.php'); ?>
</center>
</body>
</html>
