<?php
session_start();
header("content-type:text/html; charset=gb2312");
  // error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "����<a href='./loginfrm.php'>��¼</a> ��";
	echo" <script>setTimeout('document.location.href=\"./loginfrm.php\"',0)</script>";  	
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "����Ȩ���д˲�����";
	exit;
}
include('../../../config.inc');
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
$action= trim($_POST["action"]);
include('../include/dbconnect.php');
$userArray=$_POST["userArray"];
	$paras_array='';
	if(empty($userArray))
	{
	  echo " <script>window.alert(\"ѡ��Ϊ�գ�\")</script>";
			echo " <script>setTimeout('document.location.href=\"javascript:history.back()\"',3)</script>";
			exit;	
	}			
	foreach($userArray as $value)
	{
		$value= safe($value);
		if(!empty($value))$paras_array[]= ' user_id='.$value;
	}
 
	$paras=implode(' or ',$paras_array);
	$sc=true;
	if($action == '������Ч��')
	{
		$expire=$_POST['expire'];
		if(is_numeric($expire)){
			//$expire=mktime(0, 0, 0, date("m")  , date("d")+$expire, date("Y"));
			$expire=date('Y-m-d' , strtotime("+$expire day"));
			$query="update svnauth_user set expire=\"$expire\" where $paras";
			mysql_query($query) or $sc=false;
		}
	}
	if($action == '���ڲ�֪ͨ')
	{
		$query="update svnauth_user set infotimes=4 where $paras";
		mysql_query($query) or $sc=false;
	}
	if($sc)
	{
		echo "<p style='text-align:center;line-height:2;border:solid 1px;background:#ecf0e1;margin-top:200px;'><br>���óɹ���<br>�������<a href='./scheme.php'>��������ƻ�</a>��������";
		echo "<br>����<a href=./cleanuser.php>���ؼ�������</a>��<br></p>";
	}
?>
