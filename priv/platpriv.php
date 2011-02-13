<?php
session_start();
include('../include/charset.php');
error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
}
include('../../../config.inc');
include('../include/basefunction.php');
include('../include/dbconnect.php');
function safe($str)
{ 
	$str=htmlspecialchars($str,ENT_QUOTES);
	return "'".mysql_real_escape_string($str)."'";
}
$dir=safe($_GET['p']);
$repos=safe($_GET['r']);
if($repos=='/')
{
	echo "不能对根目录进行递归！";
	exit;
}
#$query="select user_name,permission,svnauth_permission.expire from svnauth_permission,svnauth_user where svnauth_user.user_id=svnauth_permission.user_id and repository='$repos' and path='$dir' order by user_name";
#1、取得递归的dir名，从组权限表和用户权限表
$dir_p=$dir.'/%';
echo $dir_p;
$query="select svnauth_permission.path as upath,svnauth_g_permission as gpath from svnauth_g_permission,svnauth_permission where (svnauth_g_permission.repository=$repos and svnauth_g_permission.path like $dir_p ) or(svnauth_permission.repository=$repos and svnauth_permission.path like $dir_p )";
$result = mysql_query($query);
$a_path=array();
$a_upath=array();
$a_gpath=array();
while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
{
	if(!empty($row['upath']))$a_upath[]=$row['upath'];
	if(!empty($row['gpath']))$a_gpath[]=$row['gpath'];
}
$a_path=array_merge($a_upath,$a_gpath);
$a_path[]=$dir;
$a_path=sort(array_unique($a_path));
#2、将dir唯一化，逐个遍历组权限表、用户权限表
foreach($a_path as $path)
{
	echo "<h5>$repos$path</h5>";
	if(in_array($path,$a_upath))
	{
		$query="select user_id,user_name,full_name,permission from svnauth_permission,svnauth_user where svnauth_user.user_id=svnauth_permission.user_id and repository='$repos' and path='$path' order by user_name";
		$result=mysql_query($query);
		while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
		{
			$user_name=$row['user_name'];
			if(!empty($row['full_name']))$full_name='('.$row['full_name'].')';
			$permission=$row['permission'];
			echo "$user_name$full_name $permission";
		}
	}
	if(in_array($path,$a_gpath))
	{
		$query="select group_id,group_name,permission from svnauth_g_permission,svnauth_group where svnauth_group.group_id=svnauth_g_permission.group_id and repository='$repos' and path='$path' order by group_name";
		$result=mysql_query($query);
		while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
		{
			$group_name=$row['group_name'];
			$per=$row['permission'];
			echo "$group_name(组) $per";
		}
	}
}
