<?php
session_start();
header("content-type:text/html; charset=gb2312");
if(!isset($_SESSION['username']))
{
	echo "���ȵ�¼��";
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "����Ȩ���д˲�����";
	exit;
}

?>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<style type='text/css'>
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.subtitle{background: #007ED1;color:white;}
.detail{width:680px}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.tb1{width:70%; border:1;text-align:center; background:#ecf0e1;}
</style>
<strong>˵����</strong>
�ڴ�ҳ���У�������Ϊ�����û��趨һ����Ч��(�ӽ��쿪ʼ�������Ч����������Ч����Ϊ1�죬�����쵽�ڣ������û���Ч�ڵ�ʱ��ϵͳ����ǰ2������Ϊÿ���û��������ʼ����û�����ͨ���ʼ������Ӷ���Ч�ڽ�����������������û�������������Ч�ڹ���ϵͳ���Զ�ɾ���û������ӦȨ����Ϣ���Դﵽ������Ч�û���Ŀ�ġ�
<br><strong>������Ч�ڣ�</strong>�˹��ܽ�Ϊѡ���û�ָ�����û���Ч���ޡ��������û�����ɾ������Ĭ���£��û���ɾ��ǰ2���ڻ��յ��������ڵ��ʼ�֪ͨ������ͨ���ʼ����м����
<br><strong>���ڲ�֪ͨ��</strong>�˹��ܽ�ʹ����ѡ�����û��ڵ���ʱ���ᱻĬĬɾ������������û����κ����Ѽ����ʼ�(״ֵ̬>3���û��������ͼ����ʼ�)��
<br><strong>״̬��</strong>�����ʼ��ķ��ʹ�������״ֵ̬>3ʱ�����ٷ��ͼ����ʼ���
<p class='tb1'>
<?php echo '���죺'.date("Y-m-d");?>
</p>
<p>
<form action="" method="get" name="searchform">
�û����ˣ�<input type="text" size="20" name="username"><input type="submit" onclick="return searchform.username.value;" value="����">&nbsp;&nbsp;&nbsp;&nbsp;
����������<input type="text" size="20" name="groupname"><input type="submit" onclick="return searchform.groupname.value;" value="�г����û�">
</form>

</p>
<script language="javascript">
<!--
var odd=true;
function fCheck(ii){
  	if(checkuser(ii))
  	{ return true;
	}else 
	{
		alert('�빴ѡ�û�');
		return false;
	}
}	

function checkuser(ii)
{ 
	var ii;
	var s=false;
	for(var i=1;i<=ii;i++)
	{ 
		var uid='userArray['+i+']';	 
		if(document.getElementById( uid ) )
		{
		     if ((document.getElementById( uid ).checked)){
			s=true;
			break;
		    }
		} 
	
	}
	return s;
}
function selall(ii)
{
	var ii;
	for(var i=1;i<=ii;i++)
	{ 
		var uid='userArray['+i+']';	 
		if(document.getElementById( uid ) )
		{
			if(odd)
			{
				document.getElementById( uid ).checked = 'true';
			}else
			{
				document.getElementById( uid ).checked = '';
			}
		}
	}
	if(odd){odd=false;}
	else odd=true;
}
-->
</script>
<?php
include('../../../config.inc');
include('../include/dbconnect.php');
$para='';
$user=trim(mysql_real_escape_string($_GET['username']));
$group=trim(mysql_real_escape_string($_GET['groupname']));
if(!empty($user))
{
	$para=" where user_name like '%$user%' ";
}
$query="select user_id,user_name,full_name,expire,infotimes from svnauth_user  $para order by expire ASC";
if(!empty($group))
{
	if(!empty($para))$para=" and user_name like '%$user%' ";
	$query="select svnauth_user.user_id,user_name,full_name,expire,infotimes from svnauth_user,svnauth_group,svnauth_groupuser where svnauth_group.group_name = '$group'  and svnauth_group.group_id=svnauth_groupuser.group_id and svnauth_groupuser.user_id=svnauth_user.user_id $para order by expire ASC";
}
$result = mysql_query($query);
	$ii=mysql_num_rows($result);
	echo  <<<SCMBBS
	<form method="post" action="user_expire.php" name='userform' onsubmit="return fCheck($ii)">	
		<table class='subtitle'>
	   <tr>
	 <td><input type=button value='ȫѡ' onclick="selall($ii)"/></td><td width=180>&nbsp;</td><td>�û���Ч��:<input type=text name='expire' value='14'/>��</td><td><input name="action" type=submit value='������Ч��' onclick="return confirm('ȷʵҪ������Ч����ע�⣺��Ч�ڹ������û�����Ȩ����Ϣ�ᱻɾ������ʼǰ��ȷ���ʼ����͹��������������û����ò�������֪ͨ��');"/></td><td><input name="action" type='submit' value='���ڲ�֪ͨ' onclick="return confirm('�û����ں󽫱���ͨ���ɾ������ȷ����');"/></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>�û���</td><td>��Ч��</td><td>״̬</td>
	  </tr>
SCMBBS;
$i=0;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		//�����е���ɫ���
				if ($tr_class=="trc1"){
					$tr_class="trc2";
				}else
				{			
					$tr_class="trc1";
				}
		$user_id=$row['user_id'];
		$user_name=$row['user_name'];
		$full_name=$row['full_name'];
		$status=$row['infotimes'];
		$expire=$row['expire'];		$i++;
		echo "<tr class=$tr_class><td><input  name=\"userArray[$i]\"  id=\"userArray[$i]\"  value=\"$user_id\" type=checkbox></td>
		<td>$user_name($full_name)</td><td>$expire</td><td>$status</td></tr>";
		
	}
	echo "</table>";
?>
