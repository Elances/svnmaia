<?php header("Cache-Control: no-cache"); 
header("content-type:text/html; charset=gb2312");
error_reporting(0);
/*
   �ļ�����index.php
   ���ܣ�����������Ȩ�޽���
   ���룺����url�ַ���
   �����Ȩ��
   �߼���
*/
include('../../../../config.inc');
include('../../config/config.php');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ����Ա");
if (!mysql_select_db(DBNAME))
{
  exit;
}
if(isset($_POST['flag']))
{
  $ops==stripslashes(trim($_POST["u"]));
  $para_str=stripslashes(trim($_POST["c"]));
  $salt=stripslashes(trim($_POST["ss"]));
  $id=stripslashes(trim($_POST["id"]));
  $mail_back=$_POST['email_back'];
  if(empty($mail_back))$mail_back='��';
  $optype=mysql_real_escape_string(stripslashes(trim($_POST["optype"])));
//��֤url�Ƿ��޸�
  $sig=urldecode(md5($salt.SECRET_KEY.$id));
  if($sig != $para_str)
  {
    echo "url�����Ƿ���";
    exit;
  }
  $para_str=mysql_real_escape_string($para_str,$mlink);
  $query="delete from svn_hex where id=$id and hexkey='$para_str';";
  $result =mysql_query($query);  
  if (mysql_affected_rows($mlink) == 0){
	  echo "�����ѱ����˴������";
	  exit;
  }
  //*****��¼����
  $query="update rt_svnpriv set ops='$ops',optype='$optype' where id=$id";
  $result=mysql_query($query);
  $query="select username,repository,path,permission,email from  rt_svnpriv where id=$id";
  $result=mysql_query($query);
  include("../../include/email.php");
  if($result)
  {
	$row= mysql_fetch_array($result, MYSQL_BOTH);
	$b_email=$row['email'];
	$us=$row['username'];
	$repos=$row['repository'];
	$path=$row['path'];
	$wpriv=$row['permission'];
  }
  $subject="����svnȨ�������Ѵ���";
  $windid="svn-rt";
  if('denied'==$optype)
  {
	  echo "����ɹ���<a href='' onclick=\"javascript:self.close;\">�ر�</a>";
	  //���ʼ�֪ͨ
	  $body="Hi,$us\n
	���svnȨ�������ѱ��ܾ�����ִ���£�$mail_back
��ֻ��һ��ϵͳ�Զ��������ʼ�������ظ���
--------------------
���ù�����
		  ";
          $sendinfo =send_mail($b_email,$subject,$body);
	  exit;
  }
  if('other'==$optype)
  {
	  echo "<script>setTimeout('document.location.href=\"../../default.htm\"',5)</script>";//��ת
	  $body="Hi,$us\n
    ���svnȨ�������ѱ��ֹ�������ִ���£�$mail_back
 ��ֻ��һ��ϵͳ�Զ��������ʼ�������ظ���
--------------------
���ù�����
";
          $sendinfo =send_mail($b_email,$subject,$body);
	  exit;
  }
  //*****��ʼ����Ȩ��
  $query="select user_id from svnauth_user where user_name='$us'";
  $result=mysql_query($query);
  if($result)
  {
	  $row= mysql_fetch_array($result, MYSQL_BOTH);
	  $uid=$row['user_id'];
  }
  switch($wpriv)
  {
     case 'r':
      	 $expire=mktime(0, 0, 0, date("m")  , date("d")+$read_t, date("Y"));
      	 break;
     case 'w':
       	$expire=mktime(0, 0, 0, date("m")  , date("d")+$write_t, date("Y"));
       	break;
     default:
       	$expire=mktime(0, 0, 0, date("m")  , date("d"), date("Y")+2);
  }
  $expire=strftime("%Y-%m-%d",$expire);	
  $query="update svnauth_permission set permission='$wpriv',expire='$expire' where  repository='$repos' and path = '$path' and user_id=$uid";
  mysql_query($query);
  if (mysql_affected_rows() == 0){
     	$query="insert into svnauth_permission (user_id,repository,path,permission,expire) values($uid,'$repos','$path','$wpriv','$expire'); ";
	mysql_query($query);
  }
  $scheme=true;
  @include('../../priv/gen_access.php');
  echo "����ɹ���<a href='' onclick=\"javascript:self.close;\">�ر�</a>";
	  //���ʼ�֪ͨ
  $body="Hi,$us\n
	  ���svnȨ�������ѱ���׼����ִ���£�$mail_back
 ��ֻ��һ��ϵͳ�Զ��������ʼ�������ظ���
--------------------
���ù�����
";
  $sendinfo =send_mail($b_email,$subject,$body);
  exit;
}
$ops=base64_decode(urldecode(trim($_GET["u"])));
$para_str=stripslashes(trim($_GET["c"]));
$salt=stripslashes(trim($_GET["ss"]));
$id=stripslashes(trim($_GET["s"]));
//��֤url�Ƿ��޸�
$sig=urldecode(md5($salt.SECRET_KEY.$id));
if($sig != $para_str)
{
  echo "url�����Ƿ���";
  exit;
}
//��֤�Ƿ��ѱ�����:
$trueurl=false;
$para_str=mysql_real_escape_string($para_str,$mlink);
$query="select id from svn_hex where id=$id and hexkey='$para_str';";
$result =mysql_query($query);
if (mysql_num_rows($result) == 0){
	$trueurl=false;
}else
  $trueurl=true;
//û���ҵ�������ʾ��Ч����
if (!$trueurl)
{
	echo "<font color=red><h2>��url�Ѳ����ڣ����ܸ������ѱ�����</h2></font>";
	echo "<p>���<a href=/>������ҳ</a>";
	echo "<p><IMG  src='../../img/waiting.gif'>";
	exit;
}
$query="select username,repository,path,permission from  rt_svnpriv where id=$id";
$result=mysql_query($query);
if($result)
{
	$row= mysql_fetch_array($result, MYSQL_BOTH);
	$us=$row['username'];
	$repos=$row['repository'];
	$path=$row['path'];
	$wpriv=$row['permission'];
	('w'==$wpriv)?($priv='��д'):($priv='ֻ��');
	$tip="&nbsp;&nbsp;&nbsp;Hi,$us ������ $svnurl/$repos/$path �� $priv Ȩ�ޣ�����������";
}
?>
<style type='text/css'>
 fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:1px solid #A4CDF2;background:#FFFFFF;}
.st{margin-left:10px;line-height:30px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
</style>
<h1>����svnȨ������</h1>
<form name=regform action="" method="post">
<fieldset>
<legend>Ȩ���Զ�����</legend>
<div class='st'>
<?php echo $tip ?>
<br>&nbsp;
<input type='hidden' name='u' value="<?php echo $ops ?>">
<input type='hidden' name='ss' value="<?php echo $salt ?>">
<input type='hidden' name='id' value="<?php echo $id ?>">
<input type='hidden' name='c' value="<?php echo $para_str ?>">
<input type='hidden' name='flag' value='1'>
<br><input type='radio' name='optype' value='agreed' id='diradmin'><label for='diradmin'>ͬ��(��ȫͬ���������url��Ȩ�ޣ����Զ�����)</label>
<br><input type='radio' name='optype' value='denied'  id='superadmin'><label for='superadmin'>�ܾ�</label>
<br><input type='radio' name='optype' value='other' id='tolist'><label for='tolist'>�ֶ���������Ȩ�޹���ϵͳ����</label>
<br>��ִ��������:<input type=text name='email_back'>
</div>
<div class='ft'>
<input type='submit' value='ȷ���ύ'>
</div>
</fieldset>
</form>


