<?php
include('../include/charset.php');
include('../include/requireAuth.php');
//import user from passwd file
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
	mysql_query("SET NAMES UTF8"); 

	$query="select user_name,password from svnauth_user  where fresh!=1 order by user_name";
	$result=mysql_query($query);
	$filestr='';
	while($result and ($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
		$user=$row['user_name'];
		$passwd=$row['password'];
		$filestr .="$user:$passwd\n";
	}
	if(empty($filestr))
	{
		echo "用户信息为空！";
		exit;
	}

	//write the file
	$query="select name  from svnauth_server ";
	$result=mysql_query($query);
	while($result and ($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
		$name=trim($row['name']);
		$handle = fopen($name$passwdfile, "w+");
		if (fwrite($handle, $filestr) === FALSE) {
       			 echo "<strong>Error:</strong>不能写入到文件 $name$passwdfile ! 保存失败！";
		}else
			echo "$name 用户已生效！";
		fclose($handle);
	}

?>
