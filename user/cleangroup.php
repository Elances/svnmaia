<?php
session_start();
include("../include/charset.php");
include('../../../config.inc');
include('../include/dbconnect.php');
if( $_SESSION['role'] !="admin")
{
	echo "无权操作！";
	exit;
}
if(!empty($_POST['gArray']))
{
	$dirArray=$_POST["gArray"];
	foreach($dirArray as $gid)
	{
		$gid= trim($value);
		if(is_numeric($gid))
		{
			$query="delete from svnauth_groupuser where group_id=$gid";
			mysql_query($query);
		}
	}
}
?>
<form method="post" action="" name='dirform' onsubmit="return fCheck()">	
	<table class='subtitle'>
	   <tr>
	  <td><input type=button value='全选' onclick="selall()"/></td><td width=280>&nbsp;</td><td>操作:<input name="action" type='submit' value='删除' onclick="return confirm('将删除该空组，你确认吗？');"/></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>组名（空组）</td>
	  </tr>

<?php
$query="select group_name,group_id from svnauth_group order by group_name";
$result = mysql_query($query);
$i=0;
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
	$gid=$row['group_id'];
	$gname=$row['group_name'];
	$sql="select user_name from svnauth_groupuser,svnauth_user where svnauth_user.user_id=svnauth_groupuser.user_id and group_id=$gid group by user_name";
	$result_u=mysql_query($sql);
	$num=mysql_num_rows($result_u);
	if($num==0)
	{

		if ($tr_class=="trc1"){
			$tr_class="trc2";
		}else
		{			
			$tr_class="trc1";
		}

		$i++;
		echo"<tr class=$tr_class><td><input  name=\"gArray[$i]\"  id=\"gArray[$i]\"  value=\"$gid\" type=checkbox></td>
			<td>$gname</td></tr>";

	}
}

?>
</table>
	<table class='subtitle'>
	   <tr>
	  <td><input type=button value='全选' onclick="selall()"/></td><td width=280>&nbsp;</td><td>操作:<input name="action" type='submit' value='删除' onclick="return confirm('将删除该空组，你确认吗？');"/></td>
	   </tr>
	</table>

</form>
<script language="javascript">
<!--
var odd=true;
var ii=<?php echo $i ?>;
	
function selall()
{
	for(var i=1;i<=ii;i++)
	{ 
		var uid='gArray['+i+']';	 
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
