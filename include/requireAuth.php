<?php
session_start();
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "<script>window.alert('���Ƚ���ϵͳ����!')</script>";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}

if (($_SESSION['role']!='admin')and($_SESSION['role']!='diradmin')){	
	if(!$scheme)echo "����Ȩ�޽��д˲��������ù���Ա���<a href='../user/loginfrm.php'>��¼</a> ��";
//	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	if(!$scheme)exit;
}

?>
