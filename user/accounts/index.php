<?php header("Cache-Control: no-cache"); 
header("content-type:text/html; charset=gb2312");
/*
   �ļ�����index.php
   ���ܣ�����������޸��������
   ���룺�û����������ַ���
   �����ǩ�����û�����������
   �߼��������û����ͼ����ַ�����Ѱ��pwdurl�ļ����ж��Ƿ������ͬ��Ϣ
         û������ʾ��Ч���ӣ�����������ҳ��url��
				 �У�����ʾ�޸�������棬�����chpasswd.php������user��ĳ�ַ����ļ���ǩ������
*/
include('../../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ<a href='mailto:yahoo-scm@list.alibaba-inc.com'>����Ա</a>");
if (!mysql_select_db(DBNAME))
{
  exit;
}
$user=urldecode(trim($_GET["u"]));
$para_str=stripslashes(trim($_GET["c"]));
$ss=base64_decode(stripslashes(trim($_GET["ss"])));
$nt=microtime();
$nt=str_replace(" ","",$nt);
$nt=str_replace("0.","",$nt);
$nt=substr($nt,6);
$nt=$nt-substr($ss,6);
if(($nt>3600*23)||($nt<0))
{
	echo "�����ѹ��ڣ������»�ȡ�������ӡ�";
	exit;
}
//��֤�����Ƿ���ȷ
$trueurl=false;
$user=mysql_real_escape_string($user,$mlink);
$para_str=mysql_real_escape_string($para_str,$mlink);
$query="select username from svn_chpwd where username=\"$user\" and hexkey=\"$para_str\";";
$result =mysql_query($query);
if (mysql_num_rows($result) == 0){
	$trueurl=false;
}else
  $trueurl=true;
//û���ҵ�������ʾ��Ч����
if (!$trueurl)
{
	echo "<font color=red><h2>����������Ч����</h2></font>";
	echo "<p>���<a href=/>������ҳ</a>";
	echo "<p><IMG  src='../../img/waiting.gif'>";
	exit;
}

//�У�������ǩ������ʾ�޸�������档
$sig=md5($para_str.$user.SECRET_KEY);
?>
<style type='text/css'>
 fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:1px solid #A4CDF2;background:#FFFFFF;}
</style>
<h1>svn����������޸�����</h1>
<form name=regform action="./chpasswd.php" method="post"  onSubmit="return fCheck()">
	<fieldset>
		<legend>��������</legend>
<table >
	<tr>
		<th>�û�����</th>
		<td><input type=text readonly name=user value="<?php echo $user ?>">
			<input name=sig type=hidden value=<?php echo urlencode($sig) ?>>
		<input name=para type=hidden value=<?php echo $para_str ?>>
		<td>
	</tr>
	<tr>
		<th>�����������룺</th>
		<td><input name=pswd type=password></td>
	</tr>
	<tr>
		<th>��������һ�Σ�</th>
		<td><input name=pswd0 type=password></td>
	</tr>
	<tr>
		<td><input type=reset value=ȡ��></td>
		<td><input type=submit value=�ύ></td>
	</tr>
</table>
</fieldset>
</form>


<script language="javascript">
<!--
function fCheck(){
	
	if( ! isPassword( regform.pswd.value ) )
   {
        alert("\��������������,����������6��Ӣ����ĸ��������� !"); 
        regform.pswd.select();
        regform.pswd.focus();
        return false;
   }
  if( regform.pswd0.value =="" ) {
      alert("\����������ȷ�� !");
      regform.pswd0.select();
      regform.pswd0.focus();
      return false;
  }
  if( regform.pswd0.value != regform.pswd.value ) {
     alert("\�����������벻һ�� !");
     regform.pswd.focus();
     return false;
  }
  function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
}
-->
</script>
