<?php
session_start();
// error_reporting(0);
header("content-type:text/html; charset=gb2312");
if (!isset($_SESSION['username'])){	
	echo "���ȵ�¼!";
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "����Ȩ���д˲�����";
	exit;
}
?>
<div id='info'>
 ���ڵ��룡
</div>
 <div id='step'>
 </div>
<?php
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('���Ƚ���ϵͳ����!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
if(! file_exists($accessfile))
{
  echo "file not found! Please check your input!";
  exit;
}
include('../../../config.inc');
include('../include/dbconnect.php'); 

$handle = fopen($accessfile, "r");
$correct = false;
$firstline = true;
$groupstart = false;
$dirstart = false;
$groupinfo=array();
$group_parent=array();
$p_info=array();
//�����û�
$query="select user_id,user_name from svnauth_user order by user_name";
$result = mysql_query($query);
$uid_array=array();
while (($result)and($row= mysql_fetch_row($result))) {
	$uid=$row[0];
	$user=$row[1];
	$uid_array[$user]=$uid;
}	
function getmember($parent,$k,$detail,&$member,$c)
{
  	$c++;
  	foreach($parent[$k] as $value)
  	{
  		if(array_key_exists($value,$parent))
  		{
  			 if($c>5)return;//ֻ�ݹ�5��
  			  getmember($parent,$value,$detail,$member,$c);
  		}else
  		  $member[]=&$detail[$value];
  	}
  	if(array_key_exists($k,$detail))
  	  $member[]=&$detail[$k];
}

if ($handle) {
	echo "<script>document.getElementById('info').innerHTML='����ִ�е��룡'</script>";
	$i=1;
    while (!feof($handle)) {
	    $buffer = trim(fgets($handle));
	    echo "<script>document.getElementById('step').innerHTML='�� $i �����ݵ���ɹ������ڼ�����...'</script><br>";
	    $i++;
        if(($buffer[0] == '#')or empty($buffer))continue;
        if($firstline and ($buffer[0] != '['))
        {
        	echo "file not correct!";
        	exit;
        }
        $firstline=false;
        if($buffer == '[groups]')
        {
        	$groupstart = true;
        	continue;
        }
        if($groupstart and ($buffer[0] == '['))
        {
        	if($buffer != '[groups]')$groupstart=false;
        	//��ȡ�ڵ�Ŀ�����path��Ϣ
        	if(ereg("^\[(.*)\]$",$buffer,$matches))$buffer=$matches[1];
        	if(! $groupstart)list($repos,$path)=explode(':',$buffer,2);//���path��null�أ�[/]
        	continue;
        }
        if($groupstart)
        {
          list($group,$group_member)=explode('=',$buffer,2);
          $group=trim($group);
          $member=explode(',',$group_member);
          foreach($member as $key=>$value)
          {
            $value=trim($value);
            if($value[0] == '@')
            {
            	$group_parent['@'.$group][]=trim($value);
            	unset($member[$key]);//ɾ����group�ĵ�Ԫ
            }
          }
          if(!empty($member))$groupinfo['@'.$group]=$member;
        }else{
          if($buffer[0] == '[')
          {
            if(ereg("^\[(.*)\]$",$buffer,$matches))$buffer=$matches[1];
            list($repos,$path)=explode(':',$buffer,2);//���path��null�أ�[/]
            continue;
          }
          list($group,$permission)=explode('=',$buffer,2); 
          $group=trim($group);         
          switch(trim($permission))//����ֻ����
          {
          	case 'r':
          		$p_info[$repos][$path]['r'][]=$group;//���path��null�أ�
          		break;
          	case 'rw':
          		$p_info[$repos][$path]['w'][]=$group;
          		break;
          	case 'wr':
          		$p_info[$repos][$path]['w'][]=$group;
          		break;
          	default:
          		$p_info[$repos][$path]['n'][]=$group;
          }
          
	}
    }
    fclose($handle);
    if(count($p_info)>1)
    {
    	$query="delete from svnauth_permission";
    	mysql_query($query);
	$query="delete from svnauth_g_permission";
    	mysql_query($query);
	$query="delete from svnauth_group";
	mysql_query($query);
	$query="delete from svnauth_groupuser";
	mysql_query($query);
	foreach($groupinfo as $group => $v)
	{
		$g1=str_replace('@','',$group);
		if(function_exists('preg_match'))
		{
			if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
			{ echo "found $group is not a group <br>";
			  continue;
			}
		}else 
			echo "���php��֧��������ʽ<br>";
		$query="insert into svnauth_group (group_name) values ('$g1')";
		mysql_query($query);
	}
	foreach($group_parent as $group => $v)
	{
		$g1=str_replace('@','',$group);
		if(function_exists('preg_match'))
		{
			if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
			{ echo "found $group is not a group <br>";
			  continue;
			}
		}else 
			echo "���php��֧��������ʽ<br>";
		$query="insert into svnauth_group (group_name) values ('$g1')";
		mysql_query($query);
	}
	$query="select group_id,group_name from svnauth_group";
	$result = mysql_query($query);
	$gid_array=array();
	while (($result)and($row= mysql_fetch_row($result))) {
		$gid=$row[0];
		$group=$row[1];
		$gid_array[$group]=$gid;
	}
    }
    date_default_timezone_set('PRC');
  
  //  print_r($p_info);print_r($group_parent);print_r($groupinfo);    
    foreach($p_info as $repos => $value)
    {
    	foreach($value as $path => $v)
    	{
      	    foreach($v as $pm => $uv)
      	    {
      	    	switch($pm)
      	    	{
      	    		case 'r':
      	    		 $expire=mktime(0, 0, 0, date("m")  , date("d")+$read_t, date("Y"));
      	    		 break;
      	    		case 'w':
      	    		  $expire=mktime(0, 0, 0, date("m")  , date("d")+$write_t, date("Y"));
      	    		  break;
      	    		default:
      	    		  $expire=mktime(0, 0, 0, date("m")  , date("d"), date("Y")+2);
      	    	}      	    	
		$expire=strftime("%Y-%m-%d",$expire);
		//echo "<script>document.getElementById('step').innerHTML +='..'</script>";
      	    	foreach($uv as $goru)
      	    	{
      	    	   if($goru[0]=='@')
      	    	   {
      	    	     //�ж��Ƿ�group_parent��Ա������ǣ����ҳ�����������г�Ա,�༶�����أ���Ҫ�ݹ顣
      	    	     if(array_key_exists($goru,$group_parent))
      	    	     {
      	    	     	$member=array();
      	    	     	getmember($group_parent,$goru,$groupinfo,&$member,0);	
      	    	    // 	print_r ($member);
      	    	     	foreach($member as $ii)
      	    	     	  foreach($ii as $user){
      	    	     	  	$user=trim($user);
				//�ҳ����Ա�������
				# $query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
				$g1=str_replace('@','',$goru);
				if(function_exists('preg_match'))
				{
					if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
					{ 
						if(empty($uid_array[$user]))continue;
						$query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
						mysql_query($query);
					}else{
						$query="insert into svnauth_groupuser(group_id,user_id) values ($gid_array[$g1],$uid_array[$user])";
						mysql_query($query);
					}
				}else 
					echo "error: preg_match function not found!";			
			//	echo $query."$user<br>";	
			  }
		
      	    	     }
      	    	     //�ж��Ƿ�groupinfo�ļ���������ǣ����ҳ������Ա��
      	    	     if(array_key_exists($goru,$groupinfo)){
      	    	     	foreach($groupinfo[$goru] as $user){
      	    	     //�ҳ����Ա�������
				$user=trim($user);
				if(empty($uid_array[$user]))continue;
				$g1=str_replace('@','',$goru);
				if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
				{ 
					if(empty($uid_array[$user]))continue;
					$query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
					mysql_query($query);
					continue;
				}
				$query="insert into svnauth_groupuser(group_id,user_id) values ($gid_array[$g1],$uid_array[$user])";      	    	      
				mysql_query($query);
			//	echo $query."$user<br>";
      	    		}
		     }
	$g1=str_replace('@','',$goru);
	$query="insert into svnauth_g_permission (repository,path,group_id,permission,expire) values (\"$repos\",\"$path\",$gid_array[$g1],\"$pm\",\"$expire\")";
      	    	       //  echo "<br>$query";
      	    	        mysql_query($query);
      	    	   }else
      	    	   {
			$goru=trim($goru);
			if(empty($uid_array[$goru]))continue;
      	    	      $query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$goru],\"$pm\",\"$expire\")";
      	    	     mysql_query($query);
      	    	   }
       	       }
      	   }
       }
    }
   echo "<script>document.getElementById('step').innerHTML='ȫ������ɹ���'</script>";
}else{
  echo "Cann't read this access file, please check the private of the file";
  exit;
}
	


?>
