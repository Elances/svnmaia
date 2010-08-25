<?php
session_start();
include("../include/charset.php");
include('../../../config.inc');
include('../include/dbconnect.php');

if (!isset($_SESSION['username'])){	
	echo "请先<a href='./loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"./loginfrm.php\"',0)</script>";  	
	exit;
}

function safe($str)
{ 
	$str=htmlspecialchars($str,ENT_QUOTES);
	return "'".mysql_real_escape_string($str)."'";
}

$action= trim($_POST["action"]);
if ($_SESSION['role']!='admin')exit;
//--------------删除组
if(isset($_POST['del_g']))
{

	$groupArray=$_POST['groupArray'];
	if(empty($groupArray))
	{
	  echo " <script>window.alert(\"选择为空！\")</script>";
			echo " <script>setTimeout('document.location.href=\"javascript:history.back()\"',3)</script>";
			exit;	
	}
	foreach($groupArray as $value)
	{
		$value= safe($value);
		if(!empty($value))$paras_array[]= ' group_id='.$value;
	}
	$paras=implode(' or ',$paras_array);
	if($action == '删除')
	{
		$query="delete from svnauth_group where $paras";
		//echo $query;exit;
		$result=mysql_query($query);	
		$query="delete from svnauth_g_permission where $paras";
		$result=mysql_query($query);
		@include('../priv/gen_access.php');
	}
	if($action == '重命名')
	{
	echo <<<HTML
		<form method="post" action="">
		<fieldset>
		<legend>编辑组名</legend>
		<input type=hidden name=action value='modify'>
		<table  cellspacing='1' cellpadding='0' width='70%' border='0' >
		<tr><th>权限组名</th></tr>
HTML;
		$query="select group_id,group_name from svnauth_group where $paras";
			$result = mysql_query($query); 			
			while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
				$group_id=$row['group_id'];
				$group_name=$row['group_name'];
				echo "<tr><td><input type=hidden name='groupArray[]' value='$group_id'>
				 <input type=text name='groupname[]' value='$group_name'></td>";
			}
	echo <<<HTML
		</table>
		<table style="position:relative;left:300px;top:20px" >
		<tr><td><input style="width:80" type=submit value="确定" ></td><td><input style="width:80" type=reset value="取消" onclick="turnback()"></td></tr>
	</table>
		</fieldset></form>
HTML;
	}
	echo " <script>setTimeout('top.location.href=\"./viewgroup.php\"',0)</script>";
	exit;
}
//---------------
//修改组名-------

if($action == 'modify')
{

	$userid=$_POST['groupArray'];
	$username=$_POST['groupname'];
	for($i=0;$i<count($userid);$i++)
	{
		$username[$i]=safe($username[$i]);
		if(!is_numeric($userid))continue;
		if(empty($userid[$i]))continue;
		$query="update svnauth_group set group_name=$username[$i] where group_id=$userid[$i]";
		  	  mysql_query($query);
	}
}
echo " <script>setTimeout('top.location.href=\"./viewgroup.php\"',0)</script>";

