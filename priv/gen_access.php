<?php
header("content-type:text/html; charset=gb2312");
include('../include/requireAuth.php');
include('../../../config.inc');
include('../include/dbconnect.php');
$query="select svnauth_user.user_name,repository,path,permission from svnauth_permission,svnauth_user where svnauth_permission.user_id=svnauth_user.user_id order by repository,path";
$result = mysql_query($query);
$i=0;
$repos='';
$path='';
$groups=array();
$alluser=array();
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
  if(($repos == $row['repository'])and($path == $row['path']))
  {
    //----------
     $user=$row['user_name'];
    switch(strtolower($row['permission'])){
    case 'w':
        if($user=='*')
        {
    	  $alluser[$repos][$path]="* = rw\n";
    	  break;
        }
       $group['w'][]="$user";
       break;
    case 'r':
      if($user=='*')
        {
    	  $alluser[$repos][$path]="* = r\n";
    	  break;
        }
      $group['r'][]="$user";
      break;
    default:
      if($user=='*')
        {
    	  $alluser[$repos][$path]="* =\n";
    	  break;
        }
       $group['n'][]="$user";       
    }
    //----------       
  }else{
    
    $wg=$repos.'_w';
    $rg=$repos.'_r';
    $ng=$repos.'_n';
    
    if(!empty($group['w']))
    {
      $temp=implode(',',&$group['w']);
      if(in_array($temp,$groups))
      {
    	 $old_g=array_search($temp,$groups);
    	 if($old_g)$temp='@'.$old_g;
      }
      $groups[$wg.$i]=$temp;
    }
    if(!empty($group['r']))
    {
      $temp=implode(',',&$group['r']);
      if(in_array($temp,$groups))
      {
    	$old_g=array_search($temp,$groups);
    	if($old_g)$temp='@'.$old_g;
      }
      $groups[$rg.$i]=$temp;
    }
    if(!empty($group['n']))
    {
      $temp=implode(',',&$group['n']); 
      if(in_array($temp,$groups))
      {
    	$old_g=array_search($temp,$groups);
    	if($old_g)$temp='@'.$old_g;
      }
     $groups[$ng.$i]=$temp;
    }
    $group=array();
    //----------
    $repos=$row['repository'];
    $path=$row['path'];
    $dirs[$repos][$i]=$path;
    
     $user=$row['user_name'];
    switch(strtolower($row['permission'])){
    case 'w':
        if($user=='*')
        {
    	  $alluser[$repos][$path]="* = rw\n";
    	  break;
        }
       $group['w'][]="$user";
       break;
    case 'r':
      if($user=='*')
        {
    	  $alluser[$repos][$path]="* = r\n";
    	  break;
        }
      $group['r'][]="$user";
      break;
    default:
      if($user=='*')
        {
    	  $alluser[$repos][$path]="* =\n";
    	  break;
        }
       $group['n'][]="$user";       
    }
    //----------
    
    $i++;
  }
}
$wg=$repos.'_w';
    $rg=$repos.'_r';
    $ng=$repos.'_n';
    
    if(!empty($group['w']))
    {
      $temp=implode(',',&$group['w']);
      if(in_array($temp,$groups))
      {
    	 $old_g=array_search($temp,$groups);
    	 if($old_g)$temp='@'.$old_g;
      }
      $groups[$wg.$i]=$temp;
    }
    if(!empty($group['r']))
    {
      $temp=implode(',',&$group['r']);
      if(in_array($temp,$groups))
      {
    	$old_g=array_search($temp,$groups);
    	if($old_g)$temp='@'.$old_g;
      }
      $groups[$rg.$i]=$temp;
    }
    if(!empty($group['n']))
    {
      $temp=implode(',',&$group['n']); 
      if(in_array($temp,$groups))
      {
    	$old_g=array_search($temp,$groups);
    	if($old_g)$temp='@'.$old_g;
      }
     $groups[$ng.$i]=$temp;
    }
$access="\n[groups]\n";
foreach($groups as $key => $value)
{
  if( empty($value))continue;  
    $access .= $key.'='.$value."\n";  
}
$group='';
$group=array_keys($groups);
foreach($dirs as $key => $value)
{
  foreach($value as $i=>$path)
  {
    if(empty($path))
    {  
    	$access .="\n[$key]\n";
    }else{
       $access .= "\n[$key:$path]\n";
    }
    $wg=$key.'_w'.($i+1);
    $rg=$key.'_r'.($i+1);
    $ng=$key.'_n'.($i+1);
    if(!empty($alluser[$key][$path]))$access .= $alluser[$key][$path];
    if(!empty($groups[$wg]))$access .= "@{$wg} = rw \n";
    if(!empty($groups[$ng]))$access .= "@{$ng} =\n";
    if(!empty($groups[$rg]))$access .= "@{$rg} = r \n";
  }
}
//echo str_replace("\n","<br>",$access);

		$handle=fopen($accessfile,'w+');
		if (fwrite($handle, $access) === FALSE) {
       			 echo "<strong>Error:</strong>不能写入到文件 $accessfile ! 保存失败！";
		}else
			echo "权限生效成功！";
		fclose($handle);
		


