<?php
session_start();
 error_reporting(0);
header("content-type:text/html; charset=gb2312");
if (!isset($_SESSION['username'])){	
	echo "����<a href='../user/loginfrm.php'>��¼</a> ��";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
$user_id=$_GET['u'];
if(!is_numeric($user_id))
{
	echo "�����Ƿ���";
	exit;
}
pri_modify();
function pri_modify()
{
	if($_SESSION['role']!='admin')return 1;
	$user_id=$_GET['u'];
	$action=mysql_real_escape_string($_GET['action']);
	$repos=mysql_real_escape_string($_GET['repos']);
	$path=mysql_real_escape_string($_GET['path']);
	if(empty($user_id)or empty($repos) or empty($path))return 1;
	if($action=='degrade')
	{
		$right=mysql_real_escape_string($_GET['right']);
		switch($right){
	case 'w':
		$right='r';
		break;
	case 'r':
		$right='n';
		break;
	case 'n':
		return 0;
		break;
		}
		$query="update svnauth_permission set permission='$right' where user_id=$user_id and repository='$repos' and path='$path'";
		mysql_query($query);
		echo mysql_error();
	}
	if($action=='del')
	{
		$query="delete from svnauth_permission where user_id=$user_id and repository='$repos' and path='$path' ";
		mysql_query($query);
	}
}
$query="select repository,path,permission from svnauth_permission where user_id = $user_id";
//echo $query;exit;
$result = mysql_query($query);
if(! $result)
{
	echo "���û�û��Ȩ�ޣ�";
	exit;
}
if($_SESSION['role']=='admin')
{
	$str='����';
}
echo <<<HTML
<style type='text/css'>
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt
</style>
HTML;
echo "<table><tr><th>·��</th><th>Ȩ��</th><th>$str</th></tr>";
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$repos=$row['repository'];
	$path=$row['path'];
	$permission=$row['permission'];
	$act='';
	if($_SESSION['role']=='admin')
	{
		$act="<a href='./viewpriv.php?action=degrade&u={$user_id}&right={$permission}&path={$path}&repos=$repos'>��Ȩ</a>&nbsp;&nbsp;<a href='./viewpriv.php?action=del&path={$path}&u={$user_id}&repos=$repos'>ɾ��</a>";
	}
	$path=$repos.$path;
	switch($permission){
	case 'w':
		$permission='write';
		break;
	case 'r':
		$permission='readOnly';
		break;
	case 'n':
		$permission='none';
		break;
	}
	($tr_class=="trc1")?($tr_class="trc2"):($tr_class="trc1");	
	echo "<tr class=$tr_class><td>$path</td><td>$permission</td><td>$act</td></tr>";
}
echo "</table>";
//--------
$query="select repository,path from svnauth_dir_admin where svnauth_dir_admin.user_id=$user_id";
$result =mysql_query($query);
$adminpath='';
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$path=$row['repository'].$row['path'];
	if(empty($path))continue;
	($tr_class=="trc1")?($tr_class="trc2"):($tr_class="trc1");
	$adminpath .= "<tr class=$tr_class><td>$path</td></tr>";
}
if(!empty($adminpath))
{
	echo "<h4>�������Ŀ¼:</h4>";
	echo "<table>";
	echo $adminpath."</table>";
}
