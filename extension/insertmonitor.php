<?php
session_start();
include('../../include/charset.php');
error_reporting(0);
/*
   �ļ�����sendmail.php
   ���ܣ�����Ȩ�����룬����url�͹������ʼ�����Ӧ����Ա��
   ���룺url���û���������Ȩ�����͡�����˵��
   ����������ʼ�
   �߼���
*/
include('../../../../config.inc');
include('../../config/config.php');
include('../../include/dbconnect.php');
foreach($_POST as $k=>$v)
{
	$v=htmlspecialchars($v,ENT_QUOTES);
	$_POST[$k]=mysql_real_escape_string($v);
}
$wurl=$_POST['wurl'];
if(empty($wurl))
{
	echo " <script>window.alert(\"������Ϣ��ȫ!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
	
}
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
//*******
//�������ݿ�
//*******
$createtb = "create table IF NOT EXISTS monitor_url(
		`monitor_id` varchar(128) NOT NULL UNIQUE , PRIMARY KEY (`monitor_id`),		
  `url` varchar(255) NOT NULL  UNIQUE, 
  `version` int(40) NOT NULL
		)ENGINE=MyISAM;";
mysql_query($createtb);

$createtb = "create table IF NOT EXISTS monitor_user(
	`id` INT NOT NULL AUTO_INCREMENT, 		
	`monitor_id` varchar(128) NOT NULL , 		
   `user_id` int(11) NOT NULL, 
   `pattern` varchar(40),
  PRIMARY KEY (`user_id`,`monitor_id`)
		)ENGINE=MyISAM;";
mysql_query($createtb);

//***
//��¼����
//***
include('./geturl.php');
$ver=-1;
$wurl=geturl($wurl);
if($ver < 0 )
{
	echo " <script>window.alert(\"�޷���ȡ��url�İ汾��Ϣ����ȷ�������Ƿ���ȷ!\")</script>";
	echo " <a href='javascript:history.back()'>������ﷵ��</a>";
	echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
	exit;

}
$monitor_id=md5($wurl);
$query="insert into monitor_url set url=$wurl,version=$ver,monitor_id='$monitor_id'";
echo $query;
mysql_query($query);
$nameflag=true;
$u_ID=$_SESSION['uid'];
if (($_SESSION['role'] == 'admin')or($_SESSION['role'] == 'diradmin')){
	
	$usrArray=preg_split('/[;, ]/',$_POST['notelist']);
	foreach($usrArray as $i=>$e)
	{
		if(empty($e))continue;
		list($u,$ot)=splite('@',$e);
		$u=safe($u);
		$query="insert into monitor_user (monitor_id,user_id) select '$monitor_id',user_id from svnauth_user where user_name=$u;";
			//	echo $query;
		mysql_query($query);
		$error=mysql_error();
		$nameflag=false;
		if (mysql_affected_rows() > 0)
		{
			unset($usrArray[$i]);
			//����svn�����ַ~~
			if(strpos($e,'@'))
			{
				$e=safe($e);
				$query="update svnauth_user set email=$e where user_name=$u and email=''";
				mysql_query($query);
			}

		}else
		{
			echo $error;
			echo "<br><b>Error</b>: $u not found in svn username lists! ";
		}

	}
	if($nameflag)
	{		
		$query="insert into monitor_user (monitor_id,user_id) values ('$monitor_id',$u_ID);";
		mysql_query($query);
	}
}else{
	$query="insert into monitor_user (monitor_id,user_id) values ('$monitor_id',$u_ID);";
	mysql_query($query);
	
}
echo "<br>������ɣ�"

//***
//����û�û��email��Ϣ����������дemail��
//***
$query="select email from svnauth_user where user_id=$u_ID and email=''";
$result=mysql_query($query);
if($result)
{
	echo "<script>alert('����ʼ���ַΪ�գ�����������ʼ���ַ��������Ϣ��')</script>";
	$url="../user/user_modify.php?userArray[]=${u_ID}&action=�༭";
	echo" <script>setTimeout('document.location.href=\"$url\"',0)</script>";  	
	exit;
}

echo "<a href=''>����</a>";



