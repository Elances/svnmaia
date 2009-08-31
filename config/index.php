<?php
session_start();
// error_reporting(0);
if (!isset($_SESSION['username'])){	
//	exit;
}
header("content-type:text/html; charset=gb2312");
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
	echo "您无权进行此操作！";
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	if(!empty($_POST['accessfile']))
	{
		$file_str="<?php \n";
		foreach($_POST as $para=>$v)
		{
	if(!file_exists($v))
				switch($para)
			{
			case 'accessfile':
			 $err_acc="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;
			case 'passwdfile':
$err_pass="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;
				case 'htpasswd':
					if($v!='htpasswd')
$err_htpa="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;
				case 'svnparentpath':
$err_svnpath="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				case 'svn':
					if(!empty($v))
$err_svn="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;

			}
	if($para=='svn'){
		if(is_file($v))$v=dirname($v).'/';
	}
			$para=mysql_real_escape_string($para);	
			$v="'".mysql_real_escape_string($v)."'";
			$query="update svnauth_para set value=$v where para=\"$para\"";
			$result=mysql_query($query);
			if(mysql_affected_rows()==0)
			{
				$query="insert into svnauth_para (para,value) values(\"$para\",$v)";
				mysql_query($query);
			}
			$file_str .= "\${$para}=$v;\n";
		
		}
		//生成配置文件
		$file_str .="?>\n";
		$handle=fopen('./config.php','w+');
		if (fwrite($handle, $file_str) === FALSE) {
			$tmppath=realpath('./');
       			 echo "<strong>Error:</strong>不能写入到文件 $tmppath/config.php ! 保存失败！";
    		}
		fclose($handle);
		echo "<script>window.alert('保存成功！')</script>";
	}
	//*****列出para参数
	$query="select para,value from svnauth_para";
	$result = mysql_query($query);	
	$para_array=array();
	$para_array['accessfile']="/home/svn/repos/authz";
	$para_array['passwdfile']="/home/svn/repos/passwd";
	$para_array['htpasswd']='htpasswd';
	$para_array['smtp_server']='localhost';
	$para_array['write_t']='180';
	$para_array['read_t']='365';
	$para_array['user_t']='1095';
	$para_array['email_ext']='@yahoo.com.cn';
	$para_array['svnurl']='http://'.$_SERVER['HTTP_HOST'];
	$para_array['svnparentpath']='/home/svn/repos/';
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$para_array[$row['para']]=$row['value'];
	}
}else{
	echo "Error:不能选择数据库!".DBNAME;
}
?>
<style type='text/css'>
.ipt{position:absolute;left:220px;clear:both;float:left;background:#ece9d8;width:250px;}
br{clear:both;}
.st{margin-left:10px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
.rt{position:absolute;left:480px;}
.sf{color:blue;font-size:10pt;CURSOR:pointer;background:#FFFFCC;}
.err{color:red;}
</style>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<script language="javascript">
<!--
	var valueChanged=0;
function modify(myid)
{
	var myObj=document.getElementById(myid);
	myObj.style.background='white';
	myObj.readOnly=false;
	myObj.focus();
}
function showreadme(myid)
{
	if(document.getElementById(myid).style.display=="none")
	  document.getElementById(myid).style.display ='inline'
	else
	  document.getElementById(myid).style.display = "none";
}
-->
</script>
<h2>设置</h2>
<form method='post' action=''>
<fieldset>
<h3>系统参数设置</h3>
<div class='st'>
<br>1、权限控制文件路径：<input type='text' class='ipt' readonly name='accessfile' id='accessfile' value="<?php echo $para_array['accessfile'];?>"> <span class='rt'> <a href="#" onclick="modify('accessfile')">修改</a>&nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip')"><img src='../img/help.gif'><span id='readmetip' class='tip' style='display:none'><b>说明：</b>【必须】指定svn的权限控制文件access file的系统路径。
 </span></font> <?php echo $err_acc ?></span>
<br>2、用户文件passwd路径：<input type='text' class='ipt'  readonly name='passwdfile' id='passwdfile'  value="<?php echo $para_array['passwdfile'];?>"> <span class='rt'> <a href="#" onclick="modify('passwdfile')">修改</a>&nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip2')"><img src='../img/help.gif'><span id='readmetip2' class='tip' style='display:none'><b>说明：</b>【必须】指定svn的用户密码文件passwd file的系统路径。
 </span></font> <?php echo $err_pass ?></span>
<br>3、htpasswd路径:<input type='text' class='ipt'  readonly name='htpasswd' id='htpasswd'  value="<?php echo $para_array['htpasswd'];?>"> <span class='rt'> <a href="#" onclick="modify('htpasswd')">修改</a> &nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip3')"><img src='../img/help.gif'><span id='readmetip3' class='tip' style='display:none'><b>说明：</b>如果服务器系统变量无法识别htpasswd，则须指定htpasswd所在的具体路径。
 </span></font><?php echo $err_htpa ?></span>
<br>4、svn父目录url:<input type='text' class='ipt'  readonly name='svnurl' id='svnurl'  value="<?php echo $para_array['svnurl'];?>"> <span class='rt'> <a href="#" onclick="modify('svnurl')">修改</a>&nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip4')"><img src='../img/help.gif'><span id='readmetip4' class='tip' style='display:none'><b>说明：</b>【必须】指定通过web访问具体svn库的URL的父级目录。
 </span></font></span>
<br>5、svn仓库路径：<input type='text' class='ipt'  readonly name='svnparentpath' id='svnparentpath'  value="<?php echo $para_array['svnparentpath'];?>">  <span class='rt'><a href="#" onclick="modify('svnparentpath')">修改</a> &nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip6')"><img src='../img/help.gif'><span id='readmetip6' class='tip' style='display:none'><b>说明：</b>【必须】指定svn仓库的系统路径，要与apache的SVNParentPath参数所指定一致。
 </span></font> <?php echo $err_svnpath ?></span>
<br>6、svnlook路径：<input type='text' class='ipt'  readonly name='svn' id='svn'  value="<?php echo $para_array['svn'];?>">  <span class='rt'><a href="#" onclick="modify('svn')">修改</a> &nbsp;&nbsp;<font class=sf onmouseover="showreadme('readmetip5')"><img src='../img/help.gif'><span id='readmetip5' class='tip' style='display:none'><b>说明：</b>如果服务器无法识别svn命令，应指定svn命令所在具体系统路径。
 </span></font><?php echo $err_svn ?></span>

<br>7、邮件设置: 
<br>&nbsp;&nbsp;&nbsp;&nbsp;smtp_server:<input type='text' class='ipt'  readonly name='smtp_server' id='smtp_server'  value="<?php echo $para_array['smtp_server'];?>"> <span class='rt'> <a href="#" onclick="modify('smtp_server')">修改</a></span>
<br>&nbsp;&nbsp;&nbsp;&nbsp;sendmail:
</div>
<br>
<h3>权限设置</h3>
<div class='st'>
<br>1、写权限默认有效期:<input type='text' class='ipt' maxlength=4 readonly name='write_t' id='write_t'  value="<?php echo $para_array['write_t'];?>"> <span class='rt'>天 <a href="#" onclick="modify('write_t')">修改</a></span>
<br>2、读权限默认有效期:<input type='text' class='ipt' maxlength=4 readonly name='read_t' id='read_t'  value="<?php echo $para_array['read_t'];?>"> <span class='rt'>天 <a href="#" onclick="modify('read_t')">修改</a></span>
<br>3、用户有效期:<input type='text' class='ipt' maxlength=4 readonly name='user_t' id='user_t'  value="<?php echo $para_array['user_t'];?>"> <span class='rt'>天 <a href="#" onclick="modify('user_t')">修改</a></span>
</div>
<br>
<h3>公司邮箱</h3>
<div class='st'>
<br>邮箱后缀：<input type='text' readonly name='email_ext' id='email_ext' class='ipt' value="<?php echo $para_array['email_ext'];?>"> <span class='rt'><a href="#" onclick="modify('email_ext')">修改</a></span>
</div>
<div class='ft'>
<input type='submit' value='提交保存'>
</div>
</fieldset>
</form>

