<?php
session_start();
include('../include/charset.php');
if ($_SESSION['role'] !='admin')
{
	echo "����Ȩ���д˲�����";
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
include('../config/config.php');

if(!empty($_POST['dirArray']))
{
	$action= trim($_POST["action"]);
	$dirArray=$_POST["dirArray"];
	//����ǰ�����ļ�			
	$today = date("Ymd");
	$backupfile=$accessfile.$today;
	if(!file_exists($backupfile))
	{
		if (!copy($accessfile, $backupfile)) {
    			echo "failed to backup $accessfile...\n";
		}
	}
	foreach($dirArray as $value)
	{
		$value= mysql_real_escape_string(trim($value));
		if(!empty($value))
		{
 			$dir=$value;
			$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
			$dir=str_replace('//','/',$dir);

			list($repos,$dir)=explode('/',$dir,2);
			$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
			if(empty($repos) and ($dir=='/'))
			{
				continue;
			}
			$query="delete from svnauth_permission where repository='$repos' and path='$dir'";
			mysql_query($query);
			$query="delete from svnauth_g_permission where repository='$repos' and path='$dir'";
			mysql_query($query);
		}
	}
	echo "<script>alert('�������')</script>";


}
$query="select DISTINCT repository, path from svnauth_permission order by repository";
$result = mysql_query($query);
$dir_array=array();
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
	$repos=$row['repository'];
	$path=$row['path'];
	$dir_array[]=$repos.$path;
}
$query="select DISTINCT repository, path from svnauth_g_permission order by repository";
$result = mysql_query($query);
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
	$repos=$row['repository'];
	$path=$row['path'];
	$dir_array[]=$repos.$path;
}
$dir_array=array_unique($dir_array);

function checkurl($t_url)
{
	global $svnparentpath,$svn;
	if($t_url=='')return true;
	if(strpos($t_url,':'))return false;
//����Ŀ¼�ж�������
//	if(isset($_GET['from_d']))
	{
	  $t_url=escapeshellcmd($t_url);
 	  $localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$t_url"):("file:///$svnparentpath/$t_url");
	  exec("{$svn}svn info \"$localurl\"",$dirs_arr);
	  if(count($dirs_arr)>1)
	  {
		return true;
	  }else
		return false;
	}
	return true;
}
?>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<style type='text/css'>
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.subtitle{background: #007ED1;color:white;}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.detail{width:680px}
</style>
<strong>˵����</strong>�����ñ�������У����ܻ��е�svnĿ¼�ѱ�ɾ�����Ѹ��������ƶ�λ�ã�ʹ�����Ӧ��Ȩ����Ϣ������ࡣ�����߽���Щ���ܵ�����Ȩ����Ϣ�г����������У�������ȷ����Ϊ�����֮ɾ����

<form method="post" action="" name='dirform' onsubmit="return fCheck()">	
	<table class='subtitle'>
	   <tr>
	  <td><input type=button value='ȫѡ' onclick="selall()"/></td><td width=280>&nbsp;</td><td>����:<input name="action" type='submit' value='ɾ��' onclick="return confirm('��ɾ����Ŀ¼����Ӧ������Ȩ����Ϣ����ȷ����');"/></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>��/Ŀ¼</td>
	  </tr>

<?php
$ii=count($dir_array);
$i=0;
foreach($dir_array as $dir)
{
	if ($tr_class=="trc1"){
		$tr_class="trc2";
	}else
	{			
		$tr_class="trc1";
	}
	if($dir=='/')continue;
	if(!checkurl($dir))
	{
		$i++;
		echo"<tr class=$tr_class><td><input  name=\"dirArray[$i]\"  id=\"dirArray[$i]\"  value=\"$dir\" type=checkbox></td>
			<td>$dir</td></tr>";
	}
			
}
?>
</table>
</form>
<script language="javascript">
<!--
var odd=true;
var ii=<?php echo $ii ?>;
function fCheck(){
  	if(checkuser(ii))
  	{ return true;
	}else 
	{
		alert('�빴ѡ��/Ŀ¼');
		return false;
	}
}	

function checkuser()
{ 
	var s=false;
	for(var i=1;i<=ii;i++)
	{ 
		var uid='dirArray['+i+']';	 
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
function selall()
{
	for(var i=1;i<=ii;i++)
	{ 
		var uid='dirArray['+i+']';	 
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

