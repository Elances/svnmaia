<?php
session_start();
header("content-type:text/html; charset=gb2312");
?>
<?php
include('../../../config.inc');
include('../include/basefunction.php');
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('���Ƚ���ϵͳ����!')";
	echo" <script>setTimeout('document.location.href=\"../setup/setup.php\"',0)</script>";  	
	exit;
}
include('../include/dbconnect.php');
$usr= mysql_real_escape_string($_POST['username'],$mlink);
$passwd= $_POST['pswd'];
if($usr =="")
{
	echo " <script>window.alert(\"�û�������Ϊ�գ�������!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo "<script>history.go(-1);</script>";
  exit;
}

$user_id=0;
//SQL��ѯ���;
mysql_query("SET NAMES utf8");
$query = "SELECT supervisor,user_id,password FROM svnauth_user WHERE user_name ='$usr';"; 

// ִ�в�ѯ
$result =mysql_query($query);
if($result)$totalnum=mysql_num_rows($result);
if($totalnum>0){
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	  $fpasswd=$row['password'];
	  if(verifyPasswd($passwd,$fpasswd))
	  {	  
     	  	$_SESSION['username'] =$usr;
	  	$token=trim($row['supervisor']);
	  	$_SESSION['role']=(empty($token))?'user':'admin';
	  	$user_id=$row['user_id'];
	  	$_SESSION['uid']=$row['user_id'];
	 	echo "��ӭ���������������<a href='../default.htm'>Maia SVN�û�������ҳ</a>";
	  }else
	  {
		echo "<script>window.alert(\"������󣡣�\")</script>"; 
		echo "<script>window.history.back();</script>";
		exit;
	  }
  }

}else{
//�û������������
  echo "<script>window.alert(\"�û������ڣ���\")</script>"; 
  echo "<script>window.history.back();</script>";
  exit;
}		
//�û��Ƿ�Ŀ¼����Ա
$query="select repository,path from svnauth_dir_admin where svnauth_dir_admin.user_id=\"$user_id\"";
$result =mysql_query($query);
$num=mysql_num_rows($result);
if(($num > 0)and($_SESSION['role']=='user'))$_SESSION['role']='diradmin';
echo "<script>history.go(-2);</script>";


?>
