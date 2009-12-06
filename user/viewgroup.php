<?php
   session_start();
header("content-type:text/html; charset=gb2312");
  // error_reporting(0);
?>
<!--
Author:lixuejiang
Site:http://www.scmbbs.com
Date:2009-02-19
-->
<html>
<head>
  <title>svn用户组管理</title>
</head>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<style type='text/css'>
a{position:relative;font-style:underline;color:blue;CURSOR:pointer;}
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.bt{background:url(button.gif);width:87;height:26;font-size:10pt;text-align:center;}
.bt a{text-decoration:none;}a.hover{text-decoration:underline;}
.subtitle{background: #007ED1;}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.sumtd{font-style:italic;}
.rt{display:none;z-index:0;border:2px solid #a4cdf3;
.tb2{border:1px solid #AAAAAA;}
.es { font-size: 75%; float:right;text-decoration:none;}
.tb1{ cellspacing:1; cellpadding:0; width:70%; border:0; background:#aaa}
.tb1 tr{background:#ecf0e1;}
 b { border:1px solid #fff; color:#000; font-weight:bold;}

</style>
<script language="javascript">
<!--
var clned=0;
	function showadd(myflag)
	{
		if(myflag == '1')
		{
			document.getElementById('batchdiv').style.display='none';
		}else
		{
			document.getElementById('batchdiv').style.display='block';
		}
	}
function cleartip()
{
	if (clned != 0)return 0;
	clned=1;
	document.getElementById('batchinput').value='';
	document.getElementById('batchinput').style.background="yellow"

}

-->
</script>
<body>
<?php
include('../../../config.inc');
include('../include/dbconnect.php');
$isdir_admin=false;
$isadmin=false;
if ((isset($_SESSION['username']))and($_SESSION['role']=="admin")){ 
	$isadmin=true;
}
if ((isset($_SESSION['username']))and($_SESSION['role']=="diradmin")){ 
	$isdir_admin=true;
}
if (!isset($_SESSION['username'])){ 
	include('../template/footer.tmpl');
	exit;
}
if(isset($_GET['gid']) )
{
	$gid=$_GET['gid'];
	$grp=$_GET['grp'];
	$fromurl=$_GET['fromurl'];
	if(empty($fromurl))$fromurl='viewgroup.php';
	echo "导航：<a href='$fromurl'>返回</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='#grouppriv'>组权限</a>";
	$query="select user_name,full_name from svnauth_groupuser,svnauth_user where svnauth_groupuser.user_id=svnauth_user.user_id and svnauth_groupuser.group_id=$gid";
	$result=mysql_query($query);
	//打印出组用户列表、权限目录
	echo "<h3>$grp 组详情：</h3><h4>组成员</h4>";
	echo  <<<SCMBBS
	<form method="post" action="" name='guform' onsubmit="return fCheck($ii)">
  	<table><tr>
		<td width=100><input name="action" type=submit value="删除" onclick="return confirm('确实要从组中删除这些用户吗?');"></td>
		<td width=160><a  onclick='showadd(0)'>添加用户</a></td>
		<td width=160><a onclick='showadd()'>批量添加用户</a></td>
	</tr></table>
<table><tr><td><table>
SCMBBS;
	$i=0;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		if ($tr_class=="trc1"){
			$tr_class="trc2";
		}else
		{			
			$tr_class="trc1";
		}
		$i++;
		$user_name=$row['user_name'];
		$fullname=empty($row['full_name'])?'':'('.$row['full_name'].')';
		$sl="<td><input  name=\"guArray[$i]\"  id=\"guArray[$i]\"  value=\"{$gid}_{$uid}\" type=checkbox></td>";
		echo "<tr class=$tr_class>$sl<td>$user_name{$fullname}</td></tr>";
	}
	echo "</table></td><td valign=top>";
	echo <<<HTML
<div class='rt' id='batchdiv'>
	<img src='../img/close.bmp' ALT='close' style='float:right;' onclick="showadd('1')">
	<textarea id='batchinput' rows=13 cols=24 onfocus="cleartip()">提示：多用户名之间请用分号';'或','或空格' '进行分割或者每行一个用户。</textarea>
	<button type=button onclick='batchadd()'>添加</button>
</div>
HTML;
	echo "</td></tr></table>";
	//&******************	
	echo "<a id='grouppriv'></a><h4>组权限</h4><table>";
	if($isadmin)$st='操作';
	echo "<tr><td>目录</td><td></td><td>权限</td><td>$st</td></tr>";
	$query="select id,repository,path,permission from svnauth_g_permission where group_id=$gid";
	$result=mysql_query($query);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	if ($tr_class=="trc1"){
		$tr_class="trc2";
	}else
	{			
		$tr_class="trc1";
	}
	$id=$row['id'];
	$repos=$row['repository'];
	$path=$row['path'];
	$permission=$row['permission'];
	if($isadmin)$st="<a href='?action=del&id=$id'>删除</a>";
	echo "<tr class=$tr_class><td>$repos{$path}</td><td width=100>&nbsp;</td><td>$permission</td><td>$st</td></tr>";
	}
	echo "</table></form>";
	echo "<hr>导航：<a href='$fromurl'>返回</a>";
}
if(isset($_GET['gid']) )exit;
 $query="select group_id,group_name from svnauth_group group by group_name";
$result = mysql_query($query);
	echo  <<<SCMBBS
	<form method="post" action="group_modify.php" name='groupform' onsubmit="return fCheck($ii)">	
		<table>
	   <tr>
	  <td width="40"></td>
		<td><input name="action" type=submit value="删除" onclick="return confirm('确实要删除这些组吗?');"></td><td width=100>&nbsp;</td><td><a href="#addgroup" class='bt'>创建组</a></td>	
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>组名</td><td></td>
	  </tr>
SCMBBS;
$i=0;
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		//定义行的颜色相隔
	if ($tr_class=="trc1"){
		$tr_class="trc2";
	}else
	{			
		$tr_class="trc1";
	}
	$i++;
	$group_id=$row['group_id'];
	$group_name=$row['group_name'];
	echo "<tr class=$tr_class><td><input  name=\"groupArray[$i]\"  id=\"groupArray[$i]\"  value=\"$group_id\" type=checkbox></td><td>$group_name</td><td><a href='viewgroup.php?gid={$group_id}&grp=$group_name'>组详情$i</a></td></tr>";
}
echo "</table>";
?>
<a id='addgroup'></a>
<form method="post" action="#" name='newgroupform' >
 <fieldset class='fset'>
 <legend>创建新组</legend>
 组名：<input name='groupname'type=text><input type=submit value='保存并添加组员'>
 </fieldset>
</form>
<?php 	include('../template/footer.tmpl'); ?>
</body>
</html>

