<?php
session_start();
header("content-type:text/html; charset=gb2312");
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
 error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>";  	
	exit;
}
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
	echo "您无权进行此操作！";
	exit;
}
include('../../../config.inc');
include('../include/basefunction.php');
function safe($str)
{ 
//	$str=htmlspecialchars($str,ENT_QUOTES);
	return "'".mysql_real_escape_string($str)."'";
}
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	//校验参数正确性
	$repos=mysql_real_escape_string($_POST['repos']);
	$path=mysql_real_escape_string($_POST['path']);
	$para=array($repos,$path);
	if(keygen($para) != $_POST['sig'])
	{
		echo "参数非法！请勿越权操作！";
		exit;
	}
	$des=safe($_POST['newdescript']);
	$encode='';
	if($out[1] > 4) //mysql version > 4
	{
		echo "Mysql version:".mysql_get_server_info()."<br>";
		$encode=" DEFAULT CHARSET=utf8 ";
	}
	$createtb = "create table IF NOT EXISTS dir_des(
  `repository` varchar(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `des` text(500) default NULL,
  PRIMARY KEY  (`path`,`repository`,`des`)
		)ENGINE=MyISAM  $encode;";
	mysql_query($createtb);
	$query="insert into dir_des (repository,path,des) values('$repos','$path',$des)";
	mysql_query($query);
	$err=mysql_error();
	if(!empty($err)){
		$query="update dir_des set des=$des where repository='$repos' and path='$path'"; 
		mysql_query($query);
		$err=mysql_error();
	}
	if(empty($err))echo "successful";
}
?>
