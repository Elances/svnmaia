<?php
session_start();
header("content-type:text/html; charset=gb2312");
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
include('./autopriv.conf');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ����Ա");
if (!mysql_select_db(DBNAME))
{
  exit;
}
foreach($_POST as $k=>$v)
{
	$v=htmlspecialchars($v,ENT_QUOTES);
	$_POST[$k]=mysql_real_escape_string($v);
}
$reg_usr=$_POST['username'];
$b_email=$_POST['email'];
$wurl=$_POST['wurl'];
$wpriv=$_POST['wpriv'];
$comment=$_POST['comment'];
//******����Ϸ��Լ��********
if((empty($reg_usr))or(empty($wpriv))or(empty($wurl))or(empty($comment)))
{
	echo " <script>window.alert(\"������Ϣ��ȫ!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
	
}
function checkurl($t_url)
{
	global $svnparentpath,$svn;
	if($t_url=='')return true;
	if(strpos($t_url,':'))return false;
//����Ŀ¼�ж�������
//	if(isset($_GET['from_d']))
	{
	  $t_url=escapeshellcmd($t_url);
 	  $localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$t_url"):("file:///$svnparentpath/$t_url");
	  exec("{$svn}svn info \"$localurl\"",$dirs_arr);
	  if(count($dirs_arr)>1)
	  {
		return true;
	  }else
		return false;
	}
	return true;
}
$dir=trim($wurl);
$dir=str_replace($svnurl,'',$dir);
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
if(!checkurl($dir))
{
  echo " <script>window.alert(\"url����ȷ!\")</script>";
  echo " <a href='javascript:history.back()'>������ﷵ��</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
}
list($repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
if(empty($repos) and ($dir=='/'))
{
	$repos='/';
	$dir='';
}
$subdir=$dir;
$maillist=array();
//*****Ŀ¼����Ա����
if($dir_admin_op=='checked')
 for($ii=0;$ii<20;$ii++)
{
	$query="select user_name,email from svnauth_dir_admin,svnauth_user where svnauth_dir_admin.user_id=svnauth_user.user_id and repository='$repos' and path='$subdir' order by user_name";
	//echo $query;exit;
	$result = mysql_query($query);
	$t_found=false;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$uname=trim($row['user_name']);
		if(empty($uname))continue;
		$t_email=$row['email'];
		if(empty($t_email))$t_email=$uname.$email_ext;
		$maillist[]=$t_email;
		$t_found=true;
	}
	if($t_found)break;
	if(($subdir=='/') or (empty($subdir)))break;
	if(strlen($subdir)>1)$subdir=dirname($subdir);
	if($subdir=='\\')$subdir='/';
}
//********���͵���������Ա
if(($tosuper_op=='checked')or((!$t_found)and ($dir_admin_op=='checked'))
{
  $query="select user_name,email from svnauth_user where supervisor=1";
  $result = mysql_query($query);
  while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$uname=trim($row['user_name']);
	$t_email=$row['email'];
	if(empty($uname))continue;	
	if(($uname=='root')and (empty($t_email)))continue;
	if(empty($t_email))$t_email=$uname.$email_ext;	
	$maillist[]=$t_email;
  }
}
//*********���͵�ָ���ʼ��б�
if($tolist_op=='checked')
{
	$listArray=preg_split('/[;, ]/',$email_list);
	foreach($listArray as $v)
		if(strpos($v,'@'))$maillist[]=$v;
}
if($thenlist_op=='checked')
{
	$listArray=preg_split('/[;, ]/',$email_list2);
	foreach($listArray as $v)
		if(strpos($v,'@'))$maillist[]=$v;
}
//****************	
if(count($maillist)==0)
{
	echo "�޷����ָ�url�������ߣ��������û�з��ͳɹ��������������ϵ��";
	exit;
}
//$para_str=urldecode($para_str);
//��¼��������
$createtb = "create table IF NOT EXISTS rt_svnpriv(
		`id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`),		
		`username` varchar(40) NOT NULL,
  `repository` varchar(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `email` varchar(80) default NULL,
  `permission` varchar(1) NOT NULL,
`rtdate` date, 
  `ops` varchar(40),
  `optype` ENUM('agreed','denied','other')
		)ENGINE=MyISAM;";
	mysql_query($createtb);
$query="select max(`id`) + 1 from rt_svnpriv";
$result=mysql_query($query);
if($result)
{
	$row= mysql_fetch_array($result, MYSQL_BOTH);
	$id=$row[0];
	if(empty($id))$id=1;
}

$query="insert into rt_svnpriv (`id`,`username`,`repository`,`path`,`permission`,`email`,`rtdate`) values($id,'$reg_usr','$repos','$dir','$wpriv','$b_email',NOW())";
mysql_query($query);

//******���ɴ�������*******
$salt=mt_rand();
$para_str=urlencode(md5($salt.SECRET_KEY.$id));
$url_raw="http://".$_SERVER['HTTP_HOST']. rtrim(dirname($_SERVER['PHP_SELF']))."/index.php?c=$para_str&ss=$salt&s=$id";

//**��¼����********
$createtb = "create table IF NOT EXISTS svn_hex(
		`id`  INT UNIQUE, PRIMARY KEY (id),
		`hexkey` varchar(255)
		)ENGINE=MyISAM;";
	mysql_query($createtb);
$query="insert IGNORE into svn_hex set id=$id,hexkey='$para_str';";
mysql_query($query);

//���ַ���������Ӧ����
include("../../include/email.php");
$addr=$_SERVER['REMOTE_ADDR'];
($wpriv=='w')?($priv='��д'):($priv='ֻ��');
foreach($maillist as $mail)
{
  list($user_raw,$m)=explode('@',$mail);
  $user=urlencode(base64_encode($user_raw));
  $url=$url_raw."&u=$user";
  $body="Hi,$user_raw\n
   $reg_usr ����svn����Ȩ�ޣ���Ҫ���Ĵ������������£�
	�������·����$wurl
	����Ȩ�ޣ�$priv
	����˵����$comment

��������ͬ�⻹�Ǿܾ��������������ӽ��д�����
$url

���ͨ��������������޷����ʣ��뽫����ַ���Ʋ�ճ�����µ�����������С�

��ֻ��һ��ϵͳ�Զ��������ʼ������ǲ�������ķ��͡�

�������ʼ�����IP:$addr

--------------------
���ù�����\n
";
 $subject="svnȨ������";
 $windid="svn-rt";
 $sendinfo =send_mail($mail,$subject,$body);
 if ($sendinfo === true) {
    echo "<br>�����ѷ����� $mail";
 }else {
	echo(is_string($sendinfo) ? $sendinfo : 'reg_email_fail:'.$mail);
 }
}
echo "<br>���<a href='' onclick='javascript:self.close();'>�ر�</a>";
?>