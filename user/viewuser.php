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
  <title>svn�û�����</title>
</head>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<style type='text/css'>
a{position:relative;}
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.bt{background:url(button.gif);width:87;height:26;font-size:10pt;text-align:center;}
.bt a{text-decoration:none;}a.hover{text-decoration:underline;}
.subtitle{background: #007ED1;}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.sumtd{font-style:italic;}
ul, li{
	list-style-type: none;
}
.tb2{border:1px solid #AAAAAA;}
.es { font-size: 75%; float:right;text-decoration:none;}
.tb1{ cellspacing:1; cellpadding:0; width:70%; border:0; background:#aaa}
.tb1 tr{background:#ecf0e1;}
#page {margin:3.5em 0 0 12.9707em;*margin:2em 0 0 12.6606em;}
#page a, b { border:1px solid #ddd; text-decoration:none; padding:.25em .55em; *padding:.3em .55em; margin-right:.5em;zoom:1; font-size:107%;}
 b { border:1px solid #fff; color:#000; font-weight:bold;}
#page a:hover { background:#03c;color:#fff; border:1px solid #036;}
#page a.pre,#page a.nxt,.b { font-weight:bold; font-size:107%; padding:.25em .6em; *padding:.25em .6em;}

</style>
<script type="text/javascript" src="../js/pri.js"></script>
<body>
	
	<h2><a href="./user_modify.php?userArray[]=<?php echo $_SESSION['uid'] ?>&action=�༭" class="es">���༭������Ϣ��</a>svn�û�����</h2><p>&nbsp;</p><p>
	   <form name='searchform' method="get" action="">
	   svn�û�����ѯ��<input type="text" name="username" size="20" ><input type=submit value="����" onclick="return tCheck();">
	   </form>
</p>
<?php
 
include('../../../config.inc');
include('../include/dbconnect.php');

if(! empty($_GET['username'])){
  $un=mysql_real_escape_string($_GET['username']);
  if(empty($un))$un='no_such_user';
  $query='select user_id,user_name,full_name,staff_no,department,email from svnauth_user where user_name like \'%'.$un.'%\' or full_name like \'%'.$un.'%\'';
  $result=mysql_query($query);
  echo "<table class='tb1'>";
  $found=false;
  while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$user_id=$row['user_id'];
		$user_name=$row['user_name'];
		$full_name=$row['full_name'];
		$staff_no=$row['staff_no'];
		$email=$row['email'];
		$department=$row['department'];
		$str='';
		$found=true;
		if ($_SESSION['role']=="admin")
		  $str="<td><a href='user_modify.php?userArray[]={$user_id}&action=ɾ��' onclick=\"return confirm('ȷʵҪɾ���û���?');\">ɾ��</a></td>
		  <td><a href='user_modify.php?userArray[]={$user_id}&action=�༭'>�༭</a></td><td><a href=\"../priv/viewpriv.php?u=$user_id\" onmouseover=\"showTip('$user_id',this);\" onmouseout=\"hideTip()\">Ȩ������</a></td>";
		if($_SESSION['username']==$user_name)$str="<td><a href='user_modify.php?userArray[]=myself&action=�༭'>�༭</a></td><td><a href=\"../priv/viewpriv.php?u=$user_id\" onmouseover=\"showTip('$user_id',this);\" onmouseout=\"hideTip()\">Ȩ������</a></td>";
		echo "<tr><td>$user_name</td><td>$full_name</td><td>$staff_no</td><td>$department</td><td>$email</td>$str</tr>";
  }
  if(!$found)echo "<tr><td>&nbsp;&nbsp;&nbsp;<font color=red size=2>���Ϊ��!</font></td></tr>";
  echo "</table>";
	
}
if (!((isset($_SESSION['username']))and($_SESSION['role']=="admin"))){ 
	include('../template/footer.tmpl');
	exit;
}
if(isset($_GET['c']))exit;
?>
<script language="javascript">
<!--
function tCheck(){
  if(searchform.username.value=='')return false;
  return true;
}
function fCheck(ii){
  //if(userform.action.value=='ɾ��')
  {	
  	if(checkuser(ii))
  	{ return true;
  	}else return false;
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
function nosuperv(myid)
{
	var myid;
	if(!confirm('ȷʵҪִ����Щ������?'))return false;
	userform.action.value='ȡ�������û�';
var s=document.createElement('INPUT');
s.type='text';
s.value='ȡ�������û�';
s.name='action';
userform.appendChild(s);
var uid='userArray[1]';
document.getElementById(uid).value=myid;
document.getElementById(uid).checked=true;

	userform.submit();

}
-->
</script>
 
<?php
//����

$page=empty($_GET['page'])?'1':($_GET['page']);
$perpage=100;//ÿҳ100�� 
$begin=($page-1)*$perpage;
$end=$page*$perpage;
if((!empty($_GET['w']))and(!preg_match("/^[A-Z]$/",$_GET['w'])))
{
	echo "�����Ƿ���";
	exit;
}
if(!is_numeric($page))
{
	echo "�����Ƿ���";
	exit;
}
	$query= "select user_id,user_name,full_name from svnauth_user where supervisor=1;";
	$result = mysql_query($query); 
	echo "<h3>�����û�</h3><p><table class='tb1' >";
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$user_id=$row['user_id'];
		$user_name=$row['user_name'];
		$full_name=$row['full_name'];
		$str="<a onclick=\"return nosuperv($user_id);\" href='#'>ȡ�������û�</a>";
		if(($_SESSION['username']==$user_name)or($user_name =='root'))$str='';
		echo "<tr><td>$user_name</td><td>$full_name</td><td><a href='user_modify.php?userArray[]={$user_id}&action=�༭'>�༭</a></td>
		  <td>$str</td></tr>";
	}
	echo "</table><p><h3><a class='es' href='./reg_user.php'>���½��û���</a>��ͨ�û� </h3> <p>";
	echo "<table class='tb2'><tr><td>Contents:</td><td><a href='?page=1'>Top-</a></td><td>";
	$i='A';
	for($n=0;$n<26;$n++)
	{		
		echo "<a class='b' href=\"?w=$i\">$i</a>";
		$i++;
	}
	echo "</td></tr></table>";
	$pw=$_GET['w'];
	if(empty($pw))
	{
	  $query = "select user_id,user_name,full_name,email,staff_no,department from svnauth_user ORDER BY user_name limit $begin,$perpage;";
	}else{
	  $query = "select user_id,user_name,full_name,email,staff_no,department from svnauth_user where user_name like \"{$pw}%\" ORDER BY user_name;";
	}
	if($_GET['p']=='a')
	  $query = "select user_id,user_name,full_name,email,staff_no,department from svnauth_user ORDER BY user_name;";
	$result = mysql_query($query); 
	if(!$result){
		echo "�������ݣ�";
		exit;
	}
	//����ť
	$ii=mysql_num_rows($result);
	echo  <<<SCMBBS
	<form method="post" action="user_modify.php" name='userform' onsubmit="return fCheck($ii)">	
		<table>
	   <tr>
	  <td width="40"></td>
		<td><input name="action" type=submit value="ɾ��" onclick="return confirm('ȷʵҪɾ����Щ�û���?');"></td>
		<td width=160><input name="action" type=submit value="�༭"></td>
		<td width=160><input name="action" type=submit value="��������"></td>		
		<td width=160><input name="action" type=submit value="��Ϊ�����û�"></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>�û���</td><td>����</td><td>����</td><td>�ʼ�</td><td></td>
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
		$staff_no=$row['staff_no'];
		$department=$row['department'];
		$email=$row['email'];		$i++;
		$str="onmouseover=\"showTip('$user_id',this);\" onmouseout=\"hideTip()\"";
		echo "<tr class=$tr_class><td><input  name=\"userArray[$i]\"  id=\"userArray[$i]\"  value=\"$user_id\" type=checkbox></td>
		<td>$user_name($full_name)</td><td>$staff_no</td><td>$department</td><td>$email</td>
		<td><a href=\"../priv/viewpriv.php?u=$user_id\" $str>Ȩ������</a>$i</td></tr>";
		
	}
	echo <<<HTML
	</table><table>	
	   <tr class='subtitle'>
	  <td width="40"></td>
		<td><input name="action" type=submit value="ɾ��" onclick="return confirm('ȷʵҪɾ����Щ�û���?');"></td>
		<td width=160><input name="action" type=submit value="�༭"></td>
		<td width=160><input name="action" type=submit value="��������"></td>		
		<td width=160><input name="action" type=submit value="��Ϊ�����û�"></td>
	   </tr>
	</table>
	</form>
HTML;
	//��ʾ��ҳ 
	if((!empty($_GET['w'])) or ($_GET['p']=='a'))exit;
	$query="select distinct user_id from  svnauth_user ";
 
  $result = mysql_query($query);
  if($result)$totalnum=mysql_num_rows($result); //ȡ�ý��������
  $totalpage=ceil($totalnum / $perpage);
echo "<div id='page'>";  

if($page != $totalpage){
	$p=$page+1;
	$next ="<a href=\"?page=$p\" class='nxt'>��һҳ</a>";
}else {
	$next ="<a class=nxt>��һҳ</a>";
}
for($i=1;$i<=$totalpage;$i++){
	if($page != $i){
    $cct.="<a href=\"?page=$i\">".$i.'</a>';
 }else {
 	$cct .='<b>'.$page.'</b>';
 }
}
if($page != 1)
{	$p=($page-1);
	$pre ="<a href=\"?page=$p\" class='pre'>��һҳ</a>";
}else{
	$pre ="<a class='pre'>��һҳ</a>"; 
}
$all="<a href='?p=a'>չ������</a>";
echo $pre.$cct.$next.$all;
echo "</div>";
?>

</body>
</html>

