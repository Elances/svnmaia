<?php
header("content-type:text/html; charset=gb2312");
//����Ԫ�����ã������û����������û���Ч��\���Ѵ�������Ϊ0��
//������֤�û����������Ƿ���ȷ�������ȷ������֮��
include('../include/basefunction.php');
include('../../../config.inc');
include('../config/config.php');
echo "<h3>�û���Ϣȷ��������</h3>";
	$user=$_GET['u'];
	$email=$_GET['email'];
	$uid=$_GET['uid'];
	$sig0=$_GET['sig'];
	$action=$_GET['action'];
	$para=array($user,$email,$uid);
	$sig=keygen($para);
	if($sig != $sig0)
	{
		echo "<h3>�˼������Ӳ����ڣ���ȷ�ϡ�</h3>";
		exit;
	}
	$hidden_str="<input type=hidden name='email' value='$email'><input type=hidden name='sig' value='$sig'>";
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	if($action == 'actived'){
		if($_GET['sure']=='ȷ������')
		{
		$fullname=mysql_real_escape_string($_GET['fullname'],$mlink);
		$email_n=mysql_real_escape_string($_GET['email_n'],$mlink);
		$staff_no=mysql_real_escape_string($_GET['staff_no'],$mlink);
		$department=mysql_real_escape_string($_GET['department'],$mlink);
		$expire=date("Y-m-d" , strtotime("+$user_t day"));
		$query="update svnauth_user set full_name=\"$fullname\",email=\"$email_n\",staff_no=\"$staff_no\",department=\"$department\",expire=\"$expire\",infotimes=0 where user_id=$uid";
		mysql_query($query) or die('<strong>����ʧ��:</strong>'.mysql_error());
		echo " <script>window.alert(\"����ɹ���\")</script>";
		echo "<h3>����ɹ�������<a href='/'>svn</a></h3>";
		echo " <script>self.close();</script>";
		exit;
		}else
			if($_GET['sure']=='���Ѳ���Ҫ��ɾ��')
			{
				$query="delete from svnauth_user where user_id=$uid";
				mysql_query($query);
				echo "<h3>ɾ���ɹ�����رձ�ҳ��</h3>";
				exit;
			}
		
	}

	$query="select * from svnauth_user where user_id=$uid";
	$result = mysql_query($query); 			
	$exist=false;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$exist=true;
				$user_id=$row['user_id'];
				$user_name=$row['user_name'];
				$full_name=$row['full_name'];
				$staff_no=$row['staff_no'];
				$department=$row['department'];
				$email_n=$row['email'];
				$expire=$row['expire'];
				$tb_str="
			<tr><td><input type=hidden name='uid' value=$user_id>	
				 <input type=text readonly style='background:#ece9d8;' name='u' value=$user_name ></td>			 			 <td><input type=text name='fullname' value=$full_name></td>
				 <td><input type=text name='staff_no' value=$staff_no></td>
				  <td><input type=text name='department' value=$department></td>
				 <td><input type=text  name='email_n' value=$email_n></td></tr>

";
	}
	if(!$exist)$tb_str='<tr><td>���û��ѱ�ɾ����</td></tr>';

			

}
$exp=date('Y-m-d' , strtotime('+2 week')); 
if($expire>$exp)
{
		echo " <script>window.alert(\"���Ѽ����������û������ٴμ��\")</script>";
		echo "<h3>���Ѽ����������<a href='/'>svn</a></h3>";
		echo " <script>self.close();</script>";
		exit;
}
?>
<p>
<strong>˵����</strong>����svn�û���Ч������<?php echo $expire ?>�������û��������Զ�ɾ�����������Ҫ����ʹ��svn����ȷ��������Ϣ�������ȷ��������
<br>������Ѳ���Ҫʹ��svn����رձ����ڡ�
<div style='text-align:center';>
<form method="get" action="">

		<fieldset>

		<legend>ȷ���û���Ϣ</legend>

		<input type=hidden name='action' value='actived'>

		<table  cellspacing='1' cellpadding='0' width='70%' border='0' >

		<tr><th>�û���</th><th>��ʵ����</th><th>����</th><th>����</th><th>�ʼ�</th></tr>

<?php echo $tb_str.$hidden_str;?>
	</table>

		<table style="position:relative;top:20px" >

		<tr><td><input name='sure' title='birth' style="width:80" type=submit value="ȷ������"/></td><td><input  name='sure'  type=submit value='���Ѳ���Ҫ��ɾ��' onclick="return confirm('��ѡ����ɾ�����û�����ȷ��ô��'); "/></td></tr>

	</table>

		</fieldset></form>

</div>
