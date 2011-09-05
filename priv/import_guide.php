<?php
session_start();
// error_reporting(0);
include('../include/charset.php');
if (!isset($_SESSION['username'])){	
	echo "请先登录!";
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "您无权进行此操作！";
	exit;
}
//import the accessfile
if(!empty($_POST['svnserver']))
{
	$serverid=$_POST['svnserver'];
	if(is_numeric($serverid))
	{
		include('./import_access.php');
	}else
		echo "$serverid 非法，无法导入！";
}
//query the svn servers
$query="select server_id,locate from svnauth_server";
$result=mysql_query($query);
$serverlist='';
while($result and (row= mysql_fetch_row($result))) {
	$serverid=$row[0];
	$server=$row[1];
	$serverlist .= "<option value='$serverid'>$server</option>";
}
//get the location of accessfile
$accesslist='<div style=\'display:none\'>';
$query="select server_id,value from svnauth_para where para='accessfile";
while($result and (row= mysql_fetch_row($result))) {
	$serverid=$row[0];
	$filelocate=$row[1];
	$accesslist.= "<span id=acc[$serverid]>$filelocate</span>";
}
echo $accesslist;
?>
<form action='' method='post'>
<legend>导入权限配置</legend>
<table rules='all'>
<tr><td>授权文件路径:</td><td><label id='authzfile'></label></td></tr>
<tr><td>请选择svn服务节点:</td><td><select name='svnserver' id='svnserver' onchange="showaccess()"><?php echo $serverlist ?></select></td></tr>
<tr><td>&nbsp;</td><td></td></tr>
</table>
<div class="ft">
<input type="submit" value="导入">
</div>
</form>
<script language="javascript">
<!--
	function showaccess()
	{
		var selobj=document.getElementById('svnserver');
		var v=selobj[selobj.selectedIndex].value;
		var myid='acc['+v+']';
		document.getElementById('authzfile').innerHTML=document.getElementById(myid).innerHTML;
	}
-->
</script>

