<?php
header("content-type:text/html; charset=gb2312");
error_reporting(0);
/*
   �ļ�����checkurl.php
   ���ܣ�У��url��ȷ�ԣ������ȷ�����url��Ӧ������һ��Ŀ¼����Ա
   ���룺url
   ����������
   
*/
include('../../../../config.inc');
include('../../include/dbconnect.php');
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
include('../../config/config.php');
$dir=trim(mysql_real_escape_string($_GET['wurl']));
$dir=str_replace($svnurl,'',$dir);
if(preg_match("/^http:/i",$dir)){
	$dir=str_replace("http://",'',$dir);
	list($tmp1,$tmp2,$dir)=explode('/',$dir,3);
}
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
if(!checkurl($dir))
{
	echo "URL������!";
	exit;
}
list($repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
if(empty($repos) and ($dir=='/'))
{
	$repos='/';
	$dir='';
}
$subdir=$dir;
for($ii=0;$ii<20;$ii++)
{
	$query="select user_name,full_name from svnauth_dir_admin,svnauth_user where svnauth_dir_admin.user_id=svnauth_user.user_id and repository='$repos' and path='$subdir' order by user_name";
	//echo $query;exit;
	$result = mysql_query($query);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$uname=trim($row['user_name']);
		$fulln=$row['full_name'];;
		$fn='';
		if(!empty($fulln))$fn="($fulln)";
		if(!empty($uname))
		{
			echo "�����ߣ�$uname{$fn}";
			exit;	
		}		
	}
	if(($subdir=='/') or (empty($subdir)))break;
	if(strlen($subdir)>1)$subdir=dirname($subdir);
	if($subdir=='\\')$subdir='/';
}
$query="select user_name,full_name from svnauth_user where supervisor=1";
$result = mysql_query($query);
$root='';
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$uname=trim($row['user_name']);
	$fn=$row['full_name'];
	if(empty($uname))continue;
	if($uname=='root')
	{
		$root=$fn;
	}else
	{
		echo "�����ߣ�$uname($fn)";
		exit;
	}
}
echo "������:$root";
	

?>
