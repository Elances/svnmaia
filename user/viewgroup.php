<?php
session_start();
include("../include/charset.php");
  // error_reporting(0);
?>
<!--
Author:lixuejiang
Site:http://www.scmbbs.com
Date:2009-02-19
-->
<html>
<head>
  <title>svn�û������</title>
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
function batchadd()
{
	guform.submit();
}	
function addowner()
{
	var gn=document.getElementById('groupowner').value;
	if(gn == "")return false;	
	setowner();
}
function setowner()
{
	document.getElementById("editowner").value=1
}
function checkg()
{
	var gn=document.getElementById('groupname').value;
	if(gn == "")return false;	
	var rs=/^[\w._\-\/]{2,50}$/.test( gn );
	if(! rs)
	{
		alert('�����Ƿ�����������ĸ�����ֺ��������._-/����');
		return false;
	}
}
-->
</script>
<body>
<?php
include('../../../config.inc');
include('../include/dbconnect.php');
$isdir_admin=false;
$isadmin=false;
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
function checkinput($str)
{
	$p="/^[\w._\-\/]{2,50}$/";
	if(preg_match($p,$str))
	{
		return true;
	}else
		return false;
}
function isadmin($gid)
{
 if ((isset($_SESSION['username']))and($_SESSION['role']=="admin")){ 
	return true;
 }
 
 if ((isset($_SESSION['username']))and($_SESSION['role']=="diradmin")){ 
	$sql="select user_id from svnauth_user where user_name='".$_SESSION['username']."'";
	$t_res=mysql_query($sql);
	$row= mysql_fetch_array($t_res, MYSQL_BOTH);
	$t_uid=$row['user_id'];
	$sql="select isowner from svnauth_groupuser where group_id=$gid and user_id=$t_uid";
	$t_res=mysql_query($sql);
	$row= mysql_fetch_array($t_res, MYSQL_BOTH);
	$t_isowner=trim($row['isowner']);
	if(!empty($t_isowner))return true;

	$query="select repository,path from svnauth_g_permission where group_id=$gid";
	$result=mysql_query($query);
	$t_allmatch=true;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$path=$row['repository'].$row['path'];
		$t_match=false;
		foreach($_SESSION['s_admindir'] as $v)
		{
			$t_v=$v;
			$v=str_replace('/','\/',$v);
			$p="/^$v/";
			if(($path == $t_v)or(preg_match($p,$path)))$t_match=true;
		}
		if(!$t_match)$t_allmatch=false;
	}	
	if($t_allmatch)return true;
 }
 return false;
}

if (!isset($_SESSION['username'])){ 
	include('../template/footer.tmpl');
	exit;
}
//----������
if(isset($_POST['groupname']))
{
	if(( $_SESSION['role'] =="admin")or($_SESSION['role']=="diradmin"))
	{
		$gname=trim($_POST['groupname']);
		if(! checkinput($gname))
		{
			echo "<script>alert('incorrect groupname!');</script>";
			exit;
		}
		$gname=mysql_real_escape_string($gname);
		$query="insert into svnauth_group set group_name='$gname'";		
	//	echo $query;
		mysql_query($query);
		$query="select group_id from svnauth_group where group_name='$gname'";
		$result=mysql_query($query);
		$row=mysql_fetch_row($result);
		$gid=$row[0];
		echo "<script>window.location =\"viewgroup.php?gid=$gid&grp=$gname\";</script>";
		exit;
	}else
		echo "����Ȩ����Ȩ���飡";

}

//edit group details
if(isset($_POST['guact']))
{
	$gid=$_POST['guact'];
	if(! isadmin($gid))
	{
		echo "����Ŀ¼������Ȩ�޷�Χ������Ȩ���д˲���";
		exit;
	}
	$data_c=false;
	if(isset($_POST['guArray']))//del users
	{		
		foreach($_POST['guArray'] as $v)
		{
			list($gid_nouse,$t_uid)=explode('_',$v);
			if(! is_numeric($t_uid))continue;
			if(empty($_POST["editowner"]))
			{
			    $query="delete from svnauth_groupuser where group_id=$gid and user_id=$t_uid ";		
			    $data_c=true;
			}else
			{
				$query="update svnauth_groupuser set isowner=0 where group_id=$gid and user_id=$t_uid ";
			}
			mysql_query($query);
		}
		$_GET['gid']=$gid;
		if($data_c)@include('../priv/gen_access.php');

	}else
	{
		if(!empty($_POST["editowner"]))unset($_POST['batchinput']);
		if(isset($_POST['batchinput']))//add user
		{			
			$usrArray=preg_split('/[;, \n\r]/',$_POST['batchinput']);
			foreach($usrArray as $i=>$u)
			{
				if(empty($u))continue;
				$u=safe($u);
				$query="insert into svnauth_groupuser (group_id,user_id) select $gid,user_id from svnauth_user where user_name=$u";
			//	echo $query;
				mysql_query($query);
				if (mysql_affected_rows() > 0)
				{
					unset($usrArray[$i]);
					$data_c=true;
				}
			}
			if(count($usrArray)>0)
			{
				$unknow_usr=implode(' ',$usrArray);
				$unknow_usr=trim($unknow_usr);
				if(!empty($unknow_usr))
				{
					echo " <script>window.alert(\"��Щ�û������ڻ����Ѿ�����Ա��: $unknow_usr ��\")</script>";
				}else unset($unknow_usr);
			}
			$_GET['gid']=$gid;
			if($data_c)@include('../priv/gen_access.php');
		}
		if(isset($_POST['groupowner']))
		{
				$u=safe($_POST['groupowner']);
				$query="insert into svnauth_groupuser (group_id,user_id,isowner) select $gid,user_id,1 from svnauth_user where user_name=$u";
				mysql_query($query);
	 			$t_e=mysql_error();
				if (!empty($t_e))
				{
					$query="select user_id from svnauth_user where user_name=$u";
					$result=mysql_query($query);
					$row= mysql_fetch_array($result, MYSQL_BOTH);
					$t_uid=$row['user_id'];
					$query="update svnauth_groupuser set isowner=1 where group_id=$gid and user_id=$t_uid";
					mysql_query($query);
					//echo mysql_error();
					//echo $query;
				}
		}
	}	
}
//------
if(isset($_GET['rowid']))
{
	$gid=$_GET['d_gid'];
	if(! isadmin($gid))
	{
		echo "����Ŀ¼������Ȩ�޷�Χ������Ȩ���д˲���";
		exit;
	}
	$rowid=$_GET['rowid'];
	if(! is_numeric($rowid))exit;
	$query="delete from svnauth_g_permission where id=$rowid";
	mysql_query($query);
        $_GET['gid']=$gid;
	if (mysql_affected_rows() > 0)@include('../priv/gen_access.php');
}
//----------------
//show users and priv in group
if(isset($_GET['gid']) )
{
	$gid=$_GET['gid'];
	$grp=$_GET['grp'];
	if(!is_numeric($gid))exit;
	if(isadmin($gid))$isadmin=true;
	$ownerArray="";
	$fromurl=$_GET['fromurl'];
	if(empty($fromurl))$fromurl='viewgroup.php';
	//-------
	$sql="show columns from svnauth_groupuser";
	$result=mysql_query($sql);
  	if(mysql_num_rows($result)<3)
	{
		$sql="ALTER TABLE svnauth_groupuser ADD isowner  bit(1) default 0";
		mysql_query($sql);
	}
	//------	
	echo "������<a href='$fromurl'>����</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='#grouppriv'>��Ȩ��</a>";
	$query="select user_name,full_name,svnauth_groupuser.user_id,isowner from svnauth_groupuser,svnauth_user where svnauth_groupuser.user_id=svnauth_user.user_id and svnauth_groupuser.group_id=$gid";
	$result=mysql_query($query);
	//��ӡ�����û��б�Ȩ��Ŀ¼
	echo "<h3>$grp �����飺</h3><h4>���Ա</h4>";
	if($isadmin)
	echo  <<<SCMBBS
	<form method="post" action="" name='guform' onsubmit="return fCheck($ii)">
  	<table><tr>
		<td width=100><input name="act" type=submit value="ɾ��" onclick="return confirm('ȷʵҪ������ɾ����Щ�û���?');"></td>
		<td width=160><a  onclick='showadd(0)'>����û�</a></td>
		<td width=160><a onclick='showadd()'>��������û�</a><input type=hidden name='guact' value='$gid'></td>
	</tr></table>
SCMBBS;
	echo "<table><tr><td><table>";
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
		$t_uid=$row['user_id'];
		$isowner=trim($row['isowner']);
		$fullname=empty($row['full_name'])?'':'('.$row['full_name'].')';
		$sl="<td><input  name=\"guArray[$i]\"   value=\"{$gid}_{$t_uid}\" type=checkbox></td>";
		echo "<tr class=$tr_class>$sl<td>$user_name{$fullname}</td></tr>";
		if(!empty($isowner))$ownerArray.="<tr>$sl<td>$user_name{$fullname}</td></tr>";
	}
	echo "</table></td><td valign=top>";
	if(isset($unknow_usr)){
		$tip=$unknow_usr."�û�������";
	}
	else $tip="��ʾ�����û���֮�����÷ֺ�';'��','��ո�' '���зָ����ÿ��һ���û���";
	echo <<<HTML
<div class='rt' id='batchdiv'>
	<img src='../img/close.bmp' ALT='close' style='float:right;' onclick="showadd('1')">
	<textarea id='batchinput' name='batchinput' rows=13 cols=24 onfocus="cleartip()">$tip</textarea>
	<button type=button onclick='batchadd()'>���</button>
</div>
HTML;
	echo "</td></tr></table>";
	//*****************
	echo "<h4>�鸺����</h4>";
if($isadmin)
	echo  <<<SCMBBS
  	<table><tr>
		<td width=100><input name="act" type=submit value="ɾ��" onclick="if(confirm('ȷʵҪɾ����?')){setowner();return true;}return false;"><input type=hidden name="editowner" id="editowner" /></td>
		<td ><input type='text' name='groupowner' id='groupowner' ><input type=submit value='��Ӹ�����' onclick='return addowner()'></td>
	</tr></table>
SCMBBS;
	echo "<table>$ownerArray</table>";
	//&******************	
	echo "<a id='grouppriv'></a><h4>��Ȩ��</h4><table>";
	if($isadmin)$st='����';
	echo "<tr><td>Ŀ¼</td><td></td><td>Ȩ��</td><td>$st</td></tr>";
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
	if($isadmin)$st="<a href='?d_gid=$gid&rowid=$id'   onclick=\"return confirm('ȷʵҪɾ����?')\">ɾ��</a>";
	echo "<tr class=$tr_class><td>$repos{$path}</td><td width=100>&nbsp;</td><td>$permission</td><td>$st</td></tr>";
	}
	echo "</table>";
	echo "<hr>������<a href='$fromurl'>����</a>";
	if($isadmin)echo "</form>";
}
if(isset($_GET['gid']) )exit;
$query="select svnauth_group.group_id,svnauth_group.group_name from svnauth_group,svnauth_groupuser where svnauth_groupuser.group_id=svnauth_group.group_id and svnauth_groupuser.user_id=". $_SESSION['uid'] . " group by group_name";
$groupview="<a href='?a=1'>--><u>�鿴������</u></a>";
if(isset($_GET['a']) )
{
	$query="select group_id,group_name from svnauth_group group by group_name";
	$groupview="<a href='?reflash'>--><u>�鿴��������</u></a>";
}
$result = mysql_query($query);
	echo  <<<SCMBBS
<h3>Ȩ�����б�&nbsp; &nbsp; &nbsp;$groupview</h3>	
	<form method="post" action="group_modify.php" name='groupform' onsubmit="return fCheck($ii)">	
SCMBBS;
if($_SESSION['role']=="admin")
	echo <<<SCMBBS
		<table class='subtitle'>
	   <tr>
	  <td width="40"><input type=hidden name='del_g' value='del_g'></td>
		<td><input name="action" type=submit value="ɾ��" onclick="return confirm('ȷʵҪɾ����Щ����?');"></td><td width=80>&nbsp;</td><td><a href="#addgroup" class='bt'>������</a></td><td width=80>&nbsp;</td><td><input name="action" type=submit value="������"></td><td width=80>&nbsp;</td><td><input name="action" type=submit value="������"></td>	
	   </tr>
	</table>
SCMBBS;
	echo <<<SCMBBS
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>����</td><td></td>
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
	$i++;
	$group_id=$row['group_id'];
	$group_name=$row['group_name'];
	echo "<tr class=$tr_class><td><input  name=\"groupArray[$i]\"  id=\"groupArray[$i]\"  value=\"$group_id\" type=checkbox></td><td>$group_name</td><td><a href='viewgroup.php?gid={$group_id}&grp=$group_name'>������$i</a></td></tr>";
}
echo "</table></form>";
?>
<a id='addgroup'></a>
<form method="post" action="#" name='newgroupform' onsubmit='return checkg();'>
 <fieldset class='fset'>
 <legend>��������</legend>
 ������<input name='groupname' id='groupname' type=text><input type=submit value='���沢�����Ա'/>
 </fieldset>
</form>
<?php 	include('../template/footer.tmpl'); ?>
</body>
</html>

