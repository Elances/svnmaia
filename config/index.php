<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['username'])){	
//	exit;
}
include('../include/charset.php');
if ($_SESSION['role'] !='admin')
{
	echo "����Ȩ���д˲�����";
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
			if($para=='smtp_passwd')
			{
					$v=base64_encode($v);
			}
			if($para=='svnparentpath')$v=str_replace('\\','/',$v);
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
		//���������ļ�
		$file_str .="?>\n";
		$handle=fopen('./config.php','w+');
		if (fwrite($handle, $file_str) === FALSE) {
			$tmppath=realpath('./');
       			 echo "<strong>Error:</strong>����д�뵽�ļ� $tmppath/config.php ! ����ʧ�ܣ�";
    		}
		fclose($handle);
		echo "<script>window.alert('����ɹ���')</script>";
		if(!$flag)
		{
			$query="update svnauth_para set value='false' where para='use_smtp_authz'";
			mysql_query($query);
		}
	}
	//*****�г�para����
	$query="select para,value from svnauth_para";
	$result = mysql_query($query);	
	$para_array=array();
	$para_array['accessfile']="/home/svn/repos/authz";
	$para_array['passwdfile']="/home/svn/repos/passwd";
	$para_array['htpasswd']='htpasswd';
	$para_array['smtp_server']='localhost';
	$para_array['write_t']='180';
	$para_array['mail_method']='1';
	$para_array['read_t']='365';
	$para_array['user_t']='1095';
	$para_array['email_ext']='@yahoo.com.cn';
	$para_array['svnurl']='http://'.$_SERVER['HTTP_HOST'];
	$para_array['svnparentpath']='/home/svn/repos/';
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
}else{
	echo "Error:����ѡ�����ݿ�!".DBNAME;
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
<h2>����</h2>
<form method='post' action=''>
<fieldset>
<h3>ϵͳ��������</h3>
<div class='st'>
<br>1��Ȩ�޿����ļ�·����<input type='text' class='ipt' readonly name='accessfile' id='accessfile' value="<?php echo $para_array['accessfile'];?>"> <span class='rt'> <a href="#" onclick="modify('accessfile')">�޸�</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip')"><img src='../img/help.gif'></font> <?php echo $err_acc ?></span><span id='readmetip' class='sf' style='display:none'><p><br><b>˵����</b>�������ָ��svn��Ȩ�޿����ļ�access file��ϵͳ·����</p>
 </span>
<br>2���û��ļ�passwd·����<input type='text' class='ipt'  readonly name='passwdfile' id='passwdfile'  value="<?php echo $para_array['passwdfile'];?>"> <span class='rt'> <a href="#" onclick="modify('passwdfile')">�޸�</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip2')"><img src='../img/help.gif'></font> <?php echo $err_pass ?></span><span id='readmetip2' class='sf' style='display:none'><p><br><b>˵����</b>�������ָ��svn���û������ļ�passwd file��ϵͳ·����</p>
 </span>
<br>3��htpasswd·��:<input type='text' class='ipt'  readonly name='htpasswd' id='htpasswd'  value="<?php echo $para_array['htpasswd'];?>"> <span class='rt'> <a href="#" onclick="modify('htpasswd')">�޸�</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip3')"><img src='../img/help.gif'></font><?php echo $err_htpa ?></span><span id='readmetip3' class='sf' style='display:none'><p><br><b>˵����</b>���������ϵͳ�����޷�ʶ��htpasswd������ָ��htpasswd���ڵľ���·������:/usr/bin/htpasswd����:D:/apache2/bin/htpasswd�����ϵͳ��ʶ����ֻ��Ҫ��д"htpasswd"���ɡ������д��������ɾ�û����û��޸�����ʱ��Ҫ����Աʹ�á������û��ļ������ߺ������Ч��</p>
 </span>
<br>4��svn��Ŀ¼url:<input type='text' class='ipt'  readonly name='svnurl' id='svnurl'  value="<?php echo $para_array['svnurl'];?>"> <span class='rt'> <a href="#" onclick="modify('svnurl')">�޸�</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip4')"><img src='../img/help.gif'></font></span><span id='readmetip4' class='sf' style='display:none'><p><br><b>˵����</b>�������ָ��ͨ��web���ʾ���svn���URL�ĸ���Ŀ¼���磺http://svnmaia.scmbbs.com/repos_parent/</p>
 </span>
<br>5��svn�ֿ⸸·����<input type='text' class='ipt'  readonly name='svnparentpath' id='svnparentpath'  value="<?php echo $para_array['svnparentpath'];?>">  <span class='rt'><a href="#" onclick="modify('svnparentpath')">�޸�</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip6')"><img src='../img/help.gif'></font> <?php echo $err_svnpath ?></span><span id='readmetip6' class='sf' style='display:none'><p><br><b>˵����</b>�������ָ��svn�ֿ�Ⱥ���ڵ�ϵͳ·����Ҫ��apache��SVNParentPath������ָ��һ�¡��磺D:/svnroot/������windowsϵͳ��ע��·��Ҫ��"/"��·���ָ�������Ƿ�б�ߡ�</p>
 </span>
<br>6��svnlook·����<input type='text' class='ipt'  readonly name='svn' id='svn'  value="<?php echo $para_array['svn'];?>">  <span class='rt'><a href="#" onclick="modify('svn')">�޸�</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip5')"><img src='../img/help.gif'></font><?php echo $err_svn ?></span><span id='readmetip5' class='sf' style='display:none'><p><br><b>˵����</b>����������޷�ʶ��svn���Ӧָ��svn�������ھ���ϵͳ·�������������ա������windowsϵͳ������Ӧ���գ���ʹ������������svn����·����</p>
 </span>

<br>7���ʼ�����: 
<br>&nbsp;&nbsp;&nbsp;&nbsp;smtp_server:<input type='text' class='ipt'  readonly name='smtp_server' id='smtp_server'  value="<?php echo $para_array['smtp_server'];?>"><input type=hidden name='mail_method' id='mail_method'  value="<?php echo $para_array['mail_method'];?>"> <span class='rt'> <a href="#" onclick="modify('smtp_server')">�޸�</a></span>
<span class='rt2'><input type='button' onclick="showadvance('email_advance')" value='�߼�' /></span>
<span id='email_advance' style='display:<?php echo $display ?>;padding-left:20px;line-height:25px;'>
<br>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='use_smtp_authz' value='true' <?php echo $smtp_authz ?> id='use_authz' onclick="showadvance('smtp_authz')"><label for='use_authz'>SMTP��Ҫ��֤</label>
<div id='smtp_authz' style='display:<?php echo $display ?>;'>
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP��֤�û�����<input class='ipt'  type='text' name='smtp_user'  value="<?php echo $para_array['smtp_user'];?>">
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP��֤���룺<input class='ipt'  type='password' name='smtp_passwd'  value="<?php echo $para_array['smtp_passwd'];?>">
</div>
<br>&nbsp;&nbsp;&nbsp;&nbsp;SMTP�˿� <input class='ipt'  type='text' name='smtp_port'  value="<?php echo $para_array['smtp_port'];?>">
</span>
</div>
<br>
<h3>Ȩ������</h3>
<div class='st'>
<br>1��дȨ��Ĭ����Ч��:<input type='text' class='ipt' maxlength=4 readonly name='write_t' id='write_t'  value="<?php echo $para_array['write_t'];?>"> <span class='rt'>�� <a href="#" onclick="modify('write_t')">�޸�</a></span>
<br>2����Ȩ��Ĭ����Ч��:<input type='text' class='ipt' maxlength=4 readonly name='read_t' id='read_t'  value="<?php echo $para_array['read_t'];?>"> <span class='rt'>�� <a href="#" onclick="modify('read_t')">�޸�</a></span>
<br>3���û���Ч��:<input type='text' class='ipt' maxlength=4 readonly name='user_t' id='user_t'  value="<?php echo $para_array['user_t'];?>"> <span class='rt'>�� <a href="#" onclick="modify('user_t')">�޸�</a></span>
</div>
<br>
<h3>��˾����</h3>
<div class='st'>
<br>�����׺��<input type='text' readonly name='email_ext' id='email_ext' class='ipt' value="<?php echo $para_array['email_ext'];?>"> <span class='rt'><a href="#" onclick="modify('email_ext')">�޸�</a></span>
<br>ϵͳ����Ա���䣺<input type='text' readonly name='email_from' id='email_from' class='ipt' value="<?php echo $para_array['email_from'];?>"> <span class='rt'><a href="#" onclick="modify('email_from')">�޸�</a></span>
</div>
<div class='ft'>
<input type='submit' value='�ύ����'>
</div>
</fieldset>
</form>

