<?php
header("content-type:text/html; charset=gb2312");
/*
   �ļ�����chpasswd.php
   ���ܣ��޸�����
   ���룺�û�����ǩ����������
   �������
   �߼��� ��֤ǩ���Ƿ���ȷ
					�޸�����
					ɾ��pwdurl���û����Ͳ���
*/
$user=stripslashes(trim($_POST["user"]));
$sig=stripslashes($_POST["sig"]);
$para_str=stripslashes($_POST["para"]);
$newpasswd=($_POST['pswd']);
$newpasswd1=trim($_POST['pswd0']);
include('../../../../config.inc');
include('../../include/basefunction.php');
$sig0=md5($para_str.$user.SECRET_KEY);
if(strcasecmp(urlencode($sig0),trim($sig))!=0)
{
	echo "<font color=red><h2>��Ч����</h2></font>";
	echo "<meta http-equiv=\"Refresh\" content=\"2;url=/\">";
	exit;
};

$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ����Ա");
mysql_select_db(DBNAME) or die("����ѡ�����ݿ⣡");

if (($newpasswd != $newpasswd1)or(strlen($newpasswd)<6))  
{ echo " <script>window.alert(\"������������벻һ�£�����������!\")</script>";
   echo " <a href='javascript:history.back()'>������ﷵ��</a>";
   echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
   exit;
} 
include('../../config/config.php');
$pwdpath=$passwdfile;
$cmdpath=$htpasswd;

$usr= mysql_real_escape_string($user,$mlink);
$passwd1= mysql_real_escape_string($newpasswd,$mlink);
$passwd1=cryptMD5Pass($passwd1);
if(($passwd1 == "")||($usr ==""))
{
	echo " <script>window.alert(\"������û�������Ϊ�գ�������!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo "<script>history.go(-1);</script>";
  exit;
}


//SQL��ѯ���;
//$query = "SELECT user_name,password FROM svnauth_user WHERE user_name =\"$usr\""; 
$query = "update svnauth_user set password=\"$passwd1\" WHERE user_name =\"$usr\";";
// ִ�в�ѯ
$result =mysql_query($query);
if (mysql_affected_rows($mlink) == 0){
		echo "<script>window.alert(\"�û��������ڣ�����������ԭ������ͬ��\")</script>"; 
		echo "<script>history.go(-1);</script>";
		mysql_close($mlink);
		exit;
}else
{
	exec($cmdpath.' -m -b '. $pwdpath . ' '.$usr.' '.$passwd1);
 $query="delete from svn_chpwd  where username=\"$usr\";";
	mysql_query($query);
  echo "<script>window.alert(\"�������óɹ�����\")</script>"; 
	 echo "    <script>setTimeout('document.location.href=\"/\"',5)</script>
    ";
		mysql_close($mlink);
		exit;
}	
echo "<meta http-equiv=\"Refresh\" content=\"1;url=/\">";
?>
