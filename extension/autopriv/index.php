<?php header("Cache-Control: no-cache"); 
header("content-type:text/html; charset=gb2312");
error_reporting(0);
/*
   文件名：index.php
   功能：审批、处理权限界面
   输入：加密url字符串
   输出：权限
   逻辑：
*/
include('../../../../config.inc');
include('../../config/config.php');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("数据库链接失败！请联系管理员");
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
  if(empty($mail_back))$mail_back='无';
  $optype=mysql_real_escape_string(stripslashes(trim($_POST["optype"])));
//验证url是否被修改
  $sig=urldecode(md5($salt.SECRET_KEY.$id));
  if($sig != $para_str)
  {
    echo "url参数非法！";
    exit;
  }
  $para_str=mysql_real_escape_string($para_str,$mlink);
  $query="delete from svn_hex where id=$id and hexkey='$para_str';";
  $result =mysql_query($query);  
  if (mysql_affected_rows($mlink) == 0){
	  echo "请求已被他人处理完毕";
	  exit;
  }
  //*****记录过程
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
  $subject="您的svn权限申请已处理";
  $windid="svn-rt";
  if('denied'==$optype)
  {
	  echo "处理成功！<a href='' onclick=\"javascript:self.close;\">关闭</a>";
	  //发邮件通知
	  $body="Hi,$us\n
	你的svn权限申请已被拒绝，回执如下：$mail_back
这只是一封系统自动发出的邮件，请勿回复。
--------------------
配置管理组
		  ";
          $sendinfo =send_mail($b_email,$subject,$body);
	  exit;
  }
  if('other'==$optype)
  {
	  echo "<script>setTimeout('document.location.href=\"../../default.htm\"',5)</script>";//跳转
	  $body="Hi,$us\n
    你的svn权限申请已被手工处理，回执如下：$mail_back
 这只是一封系统自动发出的邮件，请勿回复。
--------------------
配置管理组
";
          $sendinfo =send_mail($b_email,$subject,$body);
	  exit;
  }
  //*****开始处理权限
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
  echo "处理成功！<a href='' onclick=\"javascript:self.close;\">关闭</a>";
	  //发邮件通知
  $body="Hi,$us\n
	  你的svn权限申请已被批准，回执如下：$mail_back
 这只是一封系统自动发出的邮件，请勿回复。
--------------------
配置管理组
";
  $sendinfo =send_mail($b_email,$subject,$body);
  exit;
}
$ops=base64_decode(urldecode(trim($_GET["u"])));
$para_str=stripslashes(trim($_GET["c"]));
$salt=stripslashes(trim($_GET["ss"]));
$id=stripslashes(trim($_GET["s"]));
//验证url是否被修改
$sig=urldecode(md5($salt.SECRET_KEY.$id));
if($sig != $para_str)
{
  echo "url参数非法！";
  exit;
}
//验证是否已被处理:
$trueurl=false;
$para_str=mysql_real_escape_string($para_str,$mlink);
$query="select id from svn_hex where id=$id and hexkey='$para_str';";
$result =mysql_query($query);
if (mysql_num_rows($result) == 0){
	$trueurl=false;
}else
  $trueurl=true;
//没有找到，这显示无效链接
if (!$trueurl)
{
	echo "<font color=red><h2>此url已不存在，可能该请求已被处理！</h2></font>";
	echo "<p>点击<a href=/>返回主页</a>";
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
	('w'==$wpriv)?($priv='读写'):($priv='只读');
	$tip="&nbsp;&nbsp;&nbsp;Hi,$us 申请了 $svnurl/$repos/$path 的 $priv 权限，请审慎处理：";
}
?>
<style type='text/css'>
 fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:1px solid #A4CDF2;background:#FFFFFF;}
.st{margin-left:10px;line-height:30px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
</style>
<h1>处理svn权限请求</h1>
<form name=regform action="" method="post">
<fieldset>
<legend>权限自动处理</legend>
<div class='st'>
<?php echo $tip ?>
<br>&nbsp;
<input type='hidden' name='u' value="<?php echo $ops ?>">
<input type='hidden' name='ss' value="<?php echo $salt ?>">
<input type='hidden' name='id' value="<?php echo $id ?>">
<input type='hidden' name='c' value="<?php echo $para_str ?>">
<input type='hidden' name='flag' value='1'>
<br><input type='radio' name='optype' value='agreed' id='diradmin'><label for='diradmin'>同意(完全同意所申请的url和权限，将自动处理)</label>
<br><input type='radio' name='optype' value='denied'  id='superadmin'><label for='superadmin'>拒绝</label>
<br><input type='radio' name='optype' value='other' id='tolist'><label for='tolist'>手动处理（进入权限管理系统处理）</label>
<br>回执给申请人:<input type=text name='email_back'>
</div>
<div class='ft'>
<input type='submit' value='确定提交'>
</div>
</fieldset>
</form>


