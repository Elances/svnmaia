<?php
header("content-type:text/html; charset=gb2312");
/*
   �ļ�����getusers.php
   ���ܣ�������������
   ���룺�û���
   �����url�������ʼ�
   �߼��������û����ҵ����䣬�����û�������������ɼ����ַ���
         ���ַ������������ļ�pwdurl,����url���� accounts/?u=urlencode(username)&c=�ַ���
				���ַ���������Ӧ���䡣
*/
include('../../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ����Ա");
if (!mysql_select_db(DBNAME))
{
  exit;
}

$usr=mysql_real_escape_string($_GET['username'],$mlink);
if(empty($usr))
{
	echo "none!";
	exit;
}
include('../../config/config.php');
$email=$usr.$email_ext;
$query="select email from svnauth_user where user_name =\"$usr\";";
$result = mysql_query($query);
if($result)$totalnum=mysql_num_rows($result); 
if($totalnum>0){
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if(empty($row['email']))
	{
	   echo $usr.$email_ext;
	}
	else
	  echo $row['email'];
}else
  echo '�û������ڣ�';
?>
