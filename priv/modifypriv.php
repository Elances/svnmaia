<?php
session_start();
header("content-type:text/html; charset=gb2312");
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('���Ƚ���ϵͳ����!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
// error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "����<a href='../user/loginfrm.php'>��¼</a> ��";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>";  	
	exit;
}
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
	echo "����Ȩ���д˲�����";
	exit;
}
include('../../../config.inc');
include('../include/basefunction.php');
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	//У�������ȷ��
	$repos=mysql_real_escape_string($_POST['repos']);
	$path=mysql_real_escape_string($_POST['path']);
	$url="./dirpriv.php?d=$repos{$path}";
	$para=array($repos,$path);
	if(keygen($para) != $_POST['sig'])
	{
		echo "�����Ƿ�������ԽȨ������";
		exit;
	}
	$adminonly=$_POST['adminonly'];
	if($adminonly =='true')
	{
		$admin_array=$_POST['manager'];
		$clear=false;
		foreach($admin_array as $v)
		{
			list($user,$uid)=explode(' ',$v);
			if(! $clear)
			{
				$clear=true;
				$query="delete from svnauth_dir_admin where repository=\"$repos\" and path=\"$path\"";
				mysql_query($query);
				$err=mysql_error();
			}
			if(! is_numeric($uid))continue;
			$query="insert into svnauth_dir_admin (repository,path,user_id) values(\"$repos\",\"$path\",$uid)";
			mysql_query($query);
			$err .= mysql_error();
		}


	}else
		if(!empty($_POST['fromdir']))
		{
			//����Ŀ¼
$dir=trim(mysql_real_escape_string($_POST['fromdir']));
$dir=str_replace($svnurl,'',$dir);
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
list($f_repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
				$query="select * from svnauth_permission where repository=\"$f_repos\" and path = \"$dir\" ";
$result=mysql_query($query);
if (mysql_num_rows($result) > 0){
	$clear=false;
		if(! $clear)
		{
				$clear=true;
				$query="delete from svnauth_permission where repository=\"$repos\" and path=\"$path\"";
				mysql_query($query);
				$err=mysql_error();
		}
	$query="insert into svnauth_permission (user_id,repository,path,permission,expire) select user_id,\"$repos\",\"$path\",permission,expire from svnauth_permission where  repository=\"$f_repos\" and path = \"$dir\" ";
	mysql_query($query);
//	$err .= mysql_error();

}else
{
	$err .= "<strong>Error��</strong>$f_repos{$dir} ��Ŀ¼��û������Ȩ��,�޷��Ӹ�Ŀ¼����Ȩ�ޡ�";
}

	}else{
		$detail_array=$_POST['permission_detail'];
		$clear=false;
		foreach($detail_array as $v)
		{
			list($rights,$user,$uid,$type)=explode(' ',$v);
			$user_expire="d_{$uid}";
			if(! $clear)
			{
				$clear=true;
				$query="delete from svnauth_permission where repository=\"$repos\" and path=\"$path\"";
				mysql_query($query);
				$err=mysql_error();
			}
			if(trim($type)=='c')continue;
			if(empty($uid))continue;
			$user=safe($user);
			$uid=safe($uid);
			if(is_numeric($_POST[$user_expire])){
				$expire=mktime(0, 0, 0, date("m")  , date("d")+$_POST[$user_expire], date("Y"));
			}else
			{
			 switch($rights)
      	    		{
      	    		case 'r':
      	    			 $expire=mktime(0, 0, 0, date("m")  , date("d")+$read_t, date("Y"));
      	    		 	break;
      	    		case 'w':
      	    		  	$expire=mktime(0, 0, 0, date("m")  , date("d")+$write_t, date("Y"));
      	    		  	break;
      	    		default:
      	    		  	$expire=mktime(0, 0, 0, date("m")  , date("d"), date("Y")+2);
      	    		}}      	    	
			$rights=safe($rights);
      	    		$expire=strftime("%Y-%m-%d",$expire);		
			$query="insert into svnauth_permission(user_id,repository,path,permission,expire)values($uid,\"$repos\",\"$path\",$rights,\"$expire\")";
			mysql_query($query);
			$err .= mysql_error();
		}

	}
	if(!empty($err))
		echo "����Ȩ�޹����з������󣬿���Ȩ��û�����óɹ���������Ϣ��<br>$err";
	else
	echo <<<HTML
<p style='text-align:center;line-height:2;border:solid 1px;background:#ecf0e1;margin-top:100px;'>
<br>����ɹ�������δ��Ч��
<br>��Ҫ��
<a href="$url">���ؼ�������</a> <br>���ǣ�
<a href="./gen_access.php?fromurl=$url">������Ч������access�ļ�)</a>?
</p>
HTML;


}
?>
