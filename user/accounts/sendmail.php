<?php
session_start();
header("content-type:text/html; charset=gb2312");
/*
   �ļ�����sendmail.php
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
$reg_usr=mysql_real_escape_string($_POST['username'],$mlink);
//���û��ʼ���cookie������֤������û��ʼ������û�һ���������������ʾ����
include('../../config/config.php');
$email=$reg_usr.$email_ext;
if(empty($reg_usr))
{
	echo " <script>window.alert(\"�������û���!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
  exit;
	
}
$query="select email from svnauth_user where user_name =\"$reg_usr\";";
$result = mysql_query($query);
if($result)$totalnum=mysql_num_rows($result); 
if($totalnum>0){
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if(empty($row['email']))
	{
	    $email=$reg_usr.$email_ext;
	}
	else
	  $email=$row['email'];
}
$cookie=explode('&',$_COOKIE['CNSSO']);
foreach($cookie as $name)
{
	if(stristr($name,'userid='))
	{
		$uEmail=str_replace('userid=','',$name).$email_ext;
		break;
	}
}
/**
if($email != $uEmail)
{
	echo " <script>window.alert(\"�������ַ������ʵ�����ַ������\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
  exit;
	
}
**/
$salt=mt_rand();
$ss=microtime();
$ss=str_replace(" ","",$ss);
$ss=base64_encode(str_replace("0.","",$ss));
$para_str=urlencode(md5($reg_usr.$salt.SECRET_KEY.$ss));
$u=urlencode($reg_usr);
$url="http://".$_SERVER['HTTP_HOST']. rtrim(dirname($_SERVER['PHP_SELF']))."/index.php?u=$u&c=$para_str&ss=$ss";
//$para_str=urldecode($para_str);
//��¼��������

	$createtb = "create table IF NOT EXISTS svn_chpwd(
		`autoid` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (autoid),		
		`username` varchar(40) UNIQUE,
		`hexkey` varchar(255)
		)ENGINE=MyISAM;";
	mysql_query($createtb);
 $query="update svn_chpwd set hexkey=\"$para_str\" where username=\"$reg_usr\";";
		mysql_query($query);
		if (mysql_affected_rows() == 0)
		{
			$query="insert IGNORE into svn_chpwd set username=\"$reg_usr\",hexkey=\"$para_str\";";
			mysql_query($query);
		}


//���ַ���������Ӧ����
		include("../../include/email.php");
		$addr=$_SERVER['REMOTE_ADDR'];
$body="���ڽ�������������svn�������
Ҫ������������ $reg_usr �ʻ�����Ĺ��̣��������������\n

$url

���ͨ��������������޷����ʣ��뽫����ַ���Ʋ�ճ�����µ�����������С�
���������ʹ�����һ����빦�ܣ���������һ���ʼ�Ϊ׼��

������������ʻ����κ���������ʣ���ظ���������ϵ��
��Ҫ����������������룬��ɾ�����ʼ���

��ֻ��һ��ϵͳ�Զ��������ʼ������ǲ�������ķ��͡�

�������ʼ�����IP:$addr

--------------------
���ù�����\n
";
$subject="svn�������";
$sendinfo =send_mail($email,$subject,$body);
if ($sendinfo === true) {
    echo "<br>�ʼ��ѷ���<br>��ע����������ʼ� $email";
}else {
	echo(is_string($sendinfo) ? $sendinfo : 'reg_email_fail');
}
#echo "    <script>setTimeout('document.location.href=\"javascript:window.history.back(-3)\"',5)</script> ";
if($_SESSION['role']=='admin')
{
	echo <<<HTML
<hr>
<strong>Hi,����Ա:</strong>
<br>&nbsp;&nbsp;&nbsp;����{$email}�Ѿ��յ���ϵͳ���������������ʼ�������Ϊ����Ա����Ҳ������������ʽ��֪�Է��������ӣ�
ͨ�������ӿ���ֱ�ӽ����������ã�������ԭ���룺
<br><a href="$url">$url</a>
<p><strong>��ע��</strong>�����������˻�ô����ӵ�ַ��</p>
<p><a href='../../default.htm'>����svnMaia����ϵͳ</a></p>
HTML;
exit;
}
echo "<meta http-equiv=\"Refresh\" content=\"3;url=../../default.htm \">";
?>
