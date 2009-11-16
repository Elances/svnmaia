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
<?php
include('../../../config.inc');
include('../include/dbconnect.php');
if (!((isset($_SESSION['username']))and($_SESSION['role']=="admin"))){ 
	include('../template/footer.tmpl');
	exit;
}
$query="select group_id,group_name from svnauth_group";
$result = mysql_query($query);
	echo  <<<SCMBBS
	<form method="post" action="group_modify.php" name='groupform' onsubmit="return fCheck($ii)">	
		<table>
	   <tr>
	  <td width="40"></td>
		<td><input name="action" type=submit value="删除" onclick="return confirm('确实要删除这些组吗?');"></td>	
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
	$group_id=$row['group_id'];
	$group_name=$row['group_name'];
	echo "<tr class=$tr_class><td><input  name=\"groupArray[$i]\"  id=\"groupArray[$i]\"  value=\"$group_id\" type=checkbox></td><td>$group_name</td><td><a href='viewgroup.php?gid=$group_id'></a></td></tr>";
}
echo "</table>";
?>
</body>
</html>

