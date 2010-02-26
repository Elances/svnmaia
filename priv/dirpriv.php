<?php
session_start();
header("content-type:text/html; charset=gb2312");
error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
}
include('../../../config.inc');
include('../include/basefunction.php');
include('../include/dbconnect.php');
function checkurl($t_url)
{
	global $svnparentpath,$svn;
	if($t_url=='')return true;
	if(strpos($t_url,':'))return false;
//中文目录判断有问题
	if(isset($_GET['from_d']))
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
$dir=trim(mysql_real_escape_string($_GET['d']));
$dir=str_replace($svnurl,'',$dir);
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
if(!checkurl($dir))
{
	echo "<script>alert('the URL is incorrect!')</script>";
	$dir='/';
}
list($repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
$firstdir="<a href='$svnurl/{$repos}{$dir}' target=_blank>{$repos}{$dir}</a>";
$authz=false;
if(empty($repos) and ($dir=='/'))
{
	$repos='/';
	$dir='';
}

//将$line中的所有[Maiasvn:variable]的内容用$var替换
function parseTags($line, $vars)
{
 
   
   $l = '';
   // Replace the scmbbs variables
   while (ereg("\[Maiasvn:([a-zA-Z0-9_]+)\]", $line, $matches))
   {
      // Find beginning
      $p = strpos($line, $matches[0]);
      
      // add everything up to beginning
      if ($p > 0) $l .= substr($line, 0, $p);
      
      // Replace variable (special token, if not exists)
      $l .= isset($vars[$matches[1]]) ? $vars[$matches[1]] : $matches[1];
      //print_r($matches);
      // Remove allready processed part of line
      $line = substr($line, $p + strlen($matches[0]));
   }
   
   // Rebuild line, add remaining part of line
   $line = $l . $line;
   
  

   // Return the results
   return $line;
}
$cur_user=array();
$d_user=array();
$fullname=array();
$allflag=false;
//所有用户
$query="select user_id,user_name,full_name from svnauth_user";
$result = mysql_query($query);
$candidate_array=array();
$candidate='';
$uid_array=array();
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$user=$row['user_name'];
	$candidate_array[$user]='n';
	$fullname[$user]=$row['full_name'];
	$uid_array[$user]=$row['user_id'];
}
//*********************
//给出目录管理员
//*********************
$subdir=$dir;
$admin_array=array();
$diradmin='';
for($ii=0;$ii<20;$ii++)
{
	$query="select user_name,full_name,svnauth_user.user_id  from svnauth_dir_admin,svnauth_user where svnauth_dir_admin.user_id=svnauth_user.user_id and repository='$repos' and path='$subdir' order by user_name";
	//echo $query;exit;
	$result = mysql_query($query);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$uname=$row['user_name'];
		$fulln=$row['full_name'];
		$uid=$row['user_id'];
		$admin_array[$uid]=$uname;
		$fn='';
		if(!empty($fulln))$fn="($fulln)";
		$diradmin .="<option value='$uname $uid'>$uname{$fn}</option>";
	}
	if(($subdir=='/') or (empty($subdir)))break;
	if(strlen($subdir)>1)$subdir=dirname($subdir);
	if($subdir=='\\')$subdir='/';
}

//*******************************
//判断当前用户是否超级管理员
// 或当前目录管理员
// ******************************
if(($_SESSION['role']=='admin')or(in_array($_SESSION['username'],$admin_array)))
	$authz=true;
if($authz)
{
 $para=array($repos,$dir);
 $sig=keygen($para);
}
//********************************
//读取目录权限的用户列表详情
//********************************

$query="select user_name,permission,svnauth_permission.expire from svnauth_permission,svnauth_user where svnauth_user.user_id=svnauth_permission.user_id and repository='$repos' and path='$dir' order by user_name";
//echo $query;exit;
$result = mysql_query($query);
if(! $result)
{
	echo "该目录没有权限！";
	exit;
}
$expire_arr=array();
while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
	//用户消重问题解决，权限覆盖？
	//-------
	$user=$row['user_name'];
	$permission=$row['permission'];
	$tmpd=(strtotime($row['expire'])-strtotime(date('Y-m-d')))/86400;
//	if($tmpd<20)
		$expire_arr[$user]=round($tmpd);
	if(array_key_exists($user,$cur_user))
	   if($cur_user[$user]> $permission)continue;
	$cur_user[$user]=$permission;
	if($user=='*')$allflag=true;
}
$userright='';
$dexpire='';
foreach($cur_user as $user => $permission){
	$fn='';
	if(!empty($fullname[$user]))$fn="($fullname[$user])";
	$uid=$uid_array[$user];
	if(empty($uid))continue;
	switch(strtolower($permission)){
    	case 'w':
    	  $userright.= "<option value='w $user $uid'>R W &nbsp;&nbsp; &nbsp; $user{$fn} </option>";
    	  break;
    	case 'r':
    	  $userright.= "<option value='r $user $uid'>R&nbsp; &nbsp; &nbsp; &nbsp; $user{$fn}</option>";
    	  break;
    	default:
    	  $userright.= "<option value='n $user $uid'>none &nbsp; &nbsp; $user{$fn}</option>";
    	  break;
	}
	//给出有效期	
	if(!isset($expire_arr[$user]))continue;
	$s='d_'.$uid;
	$d=$expire_arr[$user];
	$dexpire .="<input type='hidden' name='$s' id='$s' value='$d'>";
}
$subdir=$dir;
while($result and (! $allflag))
{
	$subdir=dirname($subdir);
	if($subdir=='\\')$subdir='/';
	$query="select user_name,permission from svnauth_permission,svnauth_user where svnauth_user.user_id=svnauth_permission.user_id and   repository='$repos' and path='$subdir' order by user_name";
	//echo $query;exit;
	$s_user=array();
	$result = mysql_query($query);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$user=$row['user_name'];
		if(array_key_exists($user,$cur_user))continue;		
	        $permission=$row['permission'];
	        if(array_key_exists($user,$s_user))	
	  		if($permission < $s_user[$user])continue;
		$s_user[$user]=$permission;
		if($user=='*')$allflag=true;
	}
	foreach($s_user as $user=>$permission )
	{
		if(array_key_exists($user,$d_user))continue;
	        $d_user[$user]=$permission;
	}
	if(($subdir=='/') or (empty($subdir)))break;
}

ksort($d_user);
foreach($d_user as $user=>$permission)
{
	$fn='';
	if(!empty($fullname[$user]))$fn="($fullname[$user])";
	$uid=$uid_array[$user];
	if(empty($uid))continue;
	        switch(strtolower($permission)){
    		case 'w':
    		  $userright.= "<option value='w $user $uid c'>R W &nbsp;&nbsp; &nbsp; $user{$fn} </option>";
    		  break;
    		case 'r':
    		  $userright.= "<option value='r $user $uid c'>R&nbsp; &nbsp; &nbsp; &nbsp; $user{$fn}</option>";
    		  break;
    		default:
    		  $userright.= "<option value='n $user $uid c'>none &nbsp; &nbsp; $user{$fn}</option>";
    		  break;
    		}
	
}
//if(!$allflag)$candidate="<option value='n *'>none &nbsp; &nbsp; *(所有用户)</option>";
$candidate_array=array_diff_key($candidate_array,$d_user,$cur_user);
foreach($candidate_array as $user => $v)
{
	$fn='';
	if(!empty($fullname[$user]))$fn="($fullname[$user])";
	$uid=$uid_array[$user];
	if(empty($uid))continue;
	$candidate .="<option value='n $user $uid'>none &nbsp; &nbsp; $user{$fn}</option>";
}
//*********************
//显示目录描述
//*********************
$des='';
$query="select des from dir_des where repository='$repos' and path='$subdir'";
$result = mysql_query($query);
if($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$des=$row['des'];
}
//*********************
//显示出内容
//*********************
$newtopf=array();
$handle = fopen("../template/showdir.htm", "r");
if(! $authz)
{
	$repos='scmbbs.com';
	$path='/';
	$sig='Power by lixuejiang';
	$showbutton='display:none;';
	$candidate='';
	$dexpire='';
}
$vars=array('dir' => $firstdir,'dirprive' => $userright,'candidate' => $candidate,'diradmin' => $diradmin,'repos' => $repos,'path' => $dir,'sig' => $sig,'authz' => $showbutton,'dexpire' => $dexpire,'description' => $des);  
while (!feof($handle))
{
      $line = fgets($handle);
      echo parseTags($line, $vars);	 
}
fclose($handle);

?>

