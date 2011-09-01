<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['username'])){	
//	exit;
}
include('../include/charset.php');
if ($_SESSION['role'] !='admin')
{
	echo "您无权进行此操作！";
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	$flag=false;
	$display='none';
	if(!empty($_POST['accessfile']))
	{
		$file_str="<?php \n";
		foreach($_POST as $para=>$v)
		{
			if(!file_exists($v))
				switch($para)
				{
				case 'htpasswd':
					if($v!='htpasswd')
				$err_htpa="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;
				case 'svn':
					if(!empty($v))
					$err_svn="<span class='err'><strong>Error:</strong>$v file not found!</span>";
				break;

				}
			if($para=='svn'){
					if(is_file($v))$v=dirname($v).'/';
			}
			if($para=='smtp_passwd')
			{
					$v=base64_encode($v);
			}
			
			if($para=='use_smtp_authz')$flag=true;
			if($para=='mail_method')
			{
				if((trim($_POST['smtp_server'])=='localhost')||($_POST['smtp_server']=='127.0.0.1'))
				{
					$v='1';
				}else  $v='2';
			}
			$para=mysql_real_escape_string($para);	
			$v="'".mysql_real_escape_string($v)."'";
			$query="update svnauth_para set value=$v where para='$para'";
			$result=mysql_query($query);
			if(mysql_affected_rows()==0)
			{
				$query="insert into svnauth_para (para,value) values('$para',$v)";
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
		if(!$flag)
		{
			$query="update svnauth_para set value='false' where para='use_smtp_authz'";
			mysql_query($query);
		}
	}
	//*****列出para参数
	$query="select para,value from svnauth_para";
	$result = mysql_query($query);	
	$para_array=array();
	$para_array['htpasswd']='htpasswd';
	$para_array['smtp_server']='localhost';
	$para_array['write_t']='180';
	$para_array['mail_method']='1';
	$para_array['read_t']='365';
	$para_array['user_t']='1095';
	$para_array['email_ext']='@yahoo.com.cn';
	$para_array['svnurl']='http://'.$_SERVER['HTTP_HOST'];
	$para_array['smtp_port']='25';
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$para_array[$row['para']]=$row['value'];
		if($row['para']=='smtp_passwd')
		{
			$para_array[$row['para']]=base64_decode($row['value']);
		}
		if(($row['para']=='use_smtp_authz')and($row['value']=='true'))
		{
			$smtp_authz='checked';
			$display='';
		}
	}
	$query="select server_id,name,locate from svnauth_server";
	$result = mysql_query($query);	
	$svnserver="<table class='tb'>";
	$isseted=false;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$isseted=true;
		$serverid=$row['server_id'];
		$name=$row['name'];
		$locate=$row['locate'];
		$svnserver.="<tr><td>$name</td><td>$locate</td><td> <a href='./newserver.php?t=m&id=$serverid'>修改</a></td></tr>";
	}
	$svnserver=$svnserver."</table>";
	if(!$isseted){
		$svnserver="<span class='err'><strong>Error:</strong>请配置svn节点服务器信息!</span>";
	}

}else{
	echo "Error:不能选择数据库!".DBNAME;
}
?>
<style type='text/css'>
.ipt{position:absolute;left:220px;clear:both;float:left;background:#ece9d8;width:250px;}
br{clear:both;}
.st{margin-left:10px;}
.st p{border:solid 1px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
.rt{position:absolute;left:480px;}
.rt2{position:absolute;left:520px;}
.sf{color:blue;font-size:10pt;CURSOR:pointer;background:#FFFFCC;}
.err{color:red;}
.subdiv {border:solid 1px;margin:0 0 10 0 ;}
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
function showadvance(myid)
{
	if(document.getElementById(myid).style.display=="none")
	  document.getElementById(myid).style.display =''
	else
	  document.getElementById(myid).style.display = "none";
}
-->
</script>
<h2>设置</h2>
<form method='post' action=''>
<fieldset>
<h3>服务器参数设置</h3>
<div class='st'>
	<h4>svn节点  <a href='./newserver.php?t=n'>新增节点</a></h4>
<?php echo $svnserver ?>

<h4>中心设置</h4>
<br>1、htpasswd路径:<input type='text' class='ipt'  readonly name='htpasswd' id='htpasswd'  value="<?php echo $para_array['htpasswd'];?>"> <span class='rt'> <a href="#" onclick="modify('htpasswd')">修改</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip3')"><img src='../img/help.gif'></font><?php echo $err_htpa ?></span><span id='readmetip3' class='sf' style='display:none'><p><br><b>说明：</b>如果服务器系统变量无法识别htpasswd，则须指定htpasswd所在的具体路径。如:/usr/bin/htpasswd，或:D:/apache2/bin/htpasswd。如果系统能识别，则只需要填写"htpasswd"即可。如果填写错误，则增删用户和用户修改密码时需要管理员使用【生成用户文件】工具后才能生效。</p>
 </span>
<br>2、svnlook路径：<input type='text' class='ipt'  readonly name='svn' id='svn'  value="<?php echo $para_array['svn'];?>">  <span class='rt'><a href="#" onclick="modify('svn')">修改</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip5')"><img src='../img/help.gif'></font><?php echo $err_svn ?></span><span id='readmetip5' class='sf' style='display:none'><p><br><b>说明：</b>如果服务器无法识别svn命令，应指定svn命令所在具体系统路径，否则请留空。如果是windows系统，本栏应留空，并使环境变量包含svn命令路径。</p>
 </span>

<br>3、邮件设置: 
<br>&nbsp;&nbsp;&nbsp;&nbsp;smtp_server:<input type='text' class='ipt'  readonly name='smtp_server' id='smtp_server'  value="<?php echo $para_array['smtp_server'];?>"><input type=hidden name='mail_method' id='mail_method'  value="<?php echo $para_array['mail_method'];?>"> <span class='rt'> <a href="#" onclick="modify('smtp_server')">修改</a></span>
<span class='rt2'><input type='button' onclick="showadvance('email_advance')" value='高级' /></span>
<span id='email_advance' style='display:<?php echo $display ?>;padding-left:20px;line-height:25px;'>
<br>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='use_smtp_authz' value='true' <?php echo $smtp_authz ?> id='use_authz' onclick="showadvance('smtp_authz')"><label for='use_authz'>SMTP需要认证</label>
<div id='smtp_authz' style='display:<?php echo $display ?>;'>
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP认证用户名：<input class='ipt'  type='text' name='smtp_user'  value="<?php echo $para_array['smtp_user'];?>">
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP认证密码：<input class='ipt'  type='password' name='smtp_passwd'  value="<?php echo $para_array['smtp_passwd'];?>">
</div>
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP端口 <input class='ipt'  type='text' name='smtp_port'  value="<?php echo $para_array['smtp_port'];?>">
</span>
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
<br>系统管理员邮箱：<input type='text' readonly name='email_from' id='email_from' class='ipt' value="<?php echo $para_array['email_from'];?>"> <span class='rt'><a href="#" onclick="modify('email_from')">修改</a></span>
</div>
<div class='ft'>
<input type='submit' value='提交保存'>
</div>
</fieldset>
</form>



