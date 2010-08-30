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
?>

<script language="javascript">
<!--
function turnback()
{ 
  // setTimeout('document.location.href="aa_fullview.php?y_site_domain='+site_domain+'&skey=6a817251398f92f265"',0)
  setTimeout('document.location.href="javascript:history.back()"',0)
}
-->
</script>

<?php

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
	$c=1;
	foreach($groupArray as $value)
	{
		$value= safe($value);
		if(!empty($value))$paras_array[]= ' group_id='.$value;
		$c++;
	}
	$paras=implode(' or ',$paras_array);
	if($action == '删除')
	{
		$query="delete from svnauth_group where $paras";
		//echo $query;exit;
		$result=mysql_query($query);	
		$query="delete from svnauth_g_permission where $paras";
		$result=mysql_query($query);
		@include('../priv/gen_access.php?fromurl=viewgroup.php');
	}
	if($action == '重命名')
	{
	echo <<<HTML
		<form method="post" action="">
		<fieldset>
		<legend>编辑组名</legend>
		<input type=hidden name=action value='modify'>
		<table  cellspacing='1' cellpadding='0' width='70%' border='0' >
		<tr><td><b>权限组名</b></td></tr>
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
	if($action == '复制组')
	{
		if($c >1 ){
	  		echo " <script>window.alert(\"不能同时复制多个组！\")</script>";
			echo " <script>setTimeout('document.location.href=\"javascript:history.back()\"',3)</script>";
			exit;	
		}
	echo <<<HTML
		<form method="post" action="">
		<fieldset>
		<legend>复制组</legend>
		<input type=hidden name=action value='copygroup'>
说明：复制组的成员或权限到目标组，如果目标组不存在则创建该组；如果目标已存在，则覆盖之。
		<table  cellspacing='1' cellpadding='0' width='70%' border='0' >
		<tr><td><b>从权限组</b></td><td><b>复制到</b></td></tr>
HTML;
		$query="select group_id,group_name from svnauth_group where $paras";
			$result = mysql_query($query); 			
			while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
				$group_id=$row['group_id'];
				$group_name=$row['group_name'];
				echo "<tr><td><input type=hidden name='groupArray' value='$group_id'>
				 <input type=text readonly value='$group_name'></td><td><input type=text name='groupname'></td>";
			}
	echo <<<HTML
		</table>
<input type=checkbox checked value='cpm' name='copym'>复制组成员 <input type=checkbox value='cpp' name='copypriv'>复制组权限
		<table style="position:relative;left:300px;top:20px" >
		<tr><td><input style="width:80" type=submit value="确定" ></td><td><input style="width:80" type=reset value="取消" onclick="turnback()"></td></tr>
	</table>
		</fieldset></form>
HTML;
	}
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
		if(!is_numeric($userid[$i]))continue;
		if(empty($userid[$i]))continue;
		$query="update svnauth_group set group_name=$username[$i] where group_id=$userid[$i]";
		  	  mysql_query($query);
	}
}
if($action == 'copygroup')
{
	$gid=safe($_POST['groupArray']);
	$gname=safe($_POST['groupname']);
	if(!is_numeric($gid))continue;
	if(empty($gid))continue;
	$query="select group_id from svnauth_group where group_name=$gname";
	$result=mysql_query($query);
	$newgroup=false;
	if(($result) and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$togroupid=$row['group_id'];
		if(empty($togroupid))
		{
			$newgroup=true;
			$query="insert into svnauth_group set group_name='$gname'";		
	//	echo $query;
			mysql_query($query);
			$query="select group_id from svnauth_group where group_name='$gname'";
			$result=mysql_query($query);
			$row=mysql_fetch_row($result);
			$togroupid=$row[0];
		}
	}	
	if($_POST['copym'] == 'cpm')
	{
		if(!$newgroup)
		{
			$query="delete from svnauth_groupuser where group_id=$togroupid";
			mysql_query($query);
		}
		$query="insert into svnauth_groupuser (group_id,user_id) select '$togroupid',user_id from svnauth_groupuser where group_id=$gid";
		mysql_query($query);
		echo mysql_error();
	}
	if($_POST['copypriv'] == 'cpp')
	{
		if(!$newgroup)
		{
			$query="delete from svnauth_g_permission where group_id=$togroupid";
			mysql_query($query);
		}
		$query="insert into svnauth_g_permission (group_id,repository,path,permission) select '$togroupid',repository,path,permission from svnauth_g_permission where group_id=$gid";
		mysql_query($query);
		echo mysql_error();
	}
}
echo " <script>setTimeout('location.href=\"./viewgroup.php\"',0)</script>";
?>

