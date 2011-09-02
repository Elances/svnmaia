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
$typeflag="<input type=hidden name=t value='n'>";
if (mysql_select_db(DBNAME))
{
	//------------新增节点
	if($_POST['t'] == 'n')
	{
		$name=mysql_real_escape_string($_POST['servername']);
		$locate=mysql_real_escape_string($_POST['server']);
		$query="insert into svnauth_server (name,locate) values('$name','$locate')";
		mysql_query($query);
		$error=mysql_error();
		if(!empty($error))
		{
				echo "<span class='err'><strong>Error:</strong>$error</span>";
				exit;
		}
		$query="select server_id from svnauth_server where name='$name'";
		$result=mysql_query($query);
		if($result and ($row= mysql_fetch_array($result, MYSQL_BOTH))) {
			$serverid=$row['server_id'];
		}
	}
	//-------修改节点
	if(is_numeric($_POST['serverid']))
	{
		$serverid=$_POST['serverid'];
		$name=mysql_real_escape_string($_POST['servername']);
		$locate=mysql_real_escape_string($_POST['server']);
		$query="update svnauth_server set name='$name',locate='$locate' where server_id=$serverid";
		mysql_query($query);
		$query="delete from svnauth_para where server_id=$serverid";	
		mysql_query($query);

	}
	if(($_POST['t'] == 'n')or(is_numeric($_POST['serverid'])))
	{
		$founderr=false;
	    foreach($_POST as $para=>$v)
 	    {	
			$v=trim($v);
			if($para == 'servername')continue;
			if($para == 'server')continue;
			if($_POST['isremote'] == 'true')
                        {
                             $_POST['isremote']=1;
                             break;
                        }
			if(!file_exists($v))
			 switch($para)
                         {
                                case 'accessfile':
					$err_acc="<span class='err'><strong>Error:</strong>$v file not found!</span>";
					$founderr=true;
                                   break;
                                case 'passwdfile':
					$err_pass="<span class='err'><strong>Error:</strong>$v file not found!</span>";
					$founderr=true;
                                    break;
			}
			if($para=='svnparentpath')
			{
				$v=str_replace('\\','/',$v);
				$sp = opendir( $v );
				if( $sp ) {
					$nodb=true;
					while( $dir = readdir( $sp ) ) {
					   if($_POST['isremote'] == '1')break;
					   if ($dir == "." || $dir == "..")continue; 
				           $svndir = $v . "/" . $dir;
					   $svndbdir = $svndir . "/db";
					   $svnhooksdir=$svndir ."/hooks";
					   if( is_dir( $svndir ) && is_dir( $svndbdir ) && is_dir($svnhooksdir))
					   {
						   $nodb=false;
						   break; 
					   }
					}
					if($nodb){
						$err_svnpath="<span class='err'><strong>Error:</strong>$v 该目录下没找到任何svn库!试试填写上一级目录？</span>";
						$founderr=true;
					}
				}
			}
			if(($para =='svnuser')and ($_POST['isremote']==1))
			{
				$random=rand();
				$rand64=substr("./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz",$random  %  64,2);
				$k1=rand().rand().$rand64;
				$passwd=md5($k1);
				$passwd=base64_encode($passwd);
				$query="insert into svnauth_para(server_id,para,value) values($serverid,'svnpasswd','$passwd')";
				mysql_query($query);
				//how to make it effect?
			}
			if($para=='svnurl')
			{
				foreach($_POST['svnurl'] as $k => $v1)
				{
					$para=mysql_real_escape_string($para);	
					$v1="'".mysql_real_escape_string($v1)."'";
					$query="insert into svnauth_para (server_id,para,value) values($serverid,'$para',$v1)";
					mysql_query($query);
					echo mysql_error();
				}

			}else
			{
					$para=mysql_real_escape_string($para);	
					$v="'".mysql_real_escape_string($v)."'";
					$query="insert into svnauth_para (server_id,para,value) values($serverid,'$para',$v)";
					mysql_query($query);
					echo mysql_error();
			}
		}
		if (($_POST['isremote']==1)or(!$founderr))echo "<script>self.close();</script>";
	}
	
	//------------修改节点
	if($_GET['t'] == 'm')
	{
		$serverid=$_GET['id'];
		$typeflag="<input type=hidden name=serverid value='$serverid'>";
		if(!is_numeric($_GET['id']))
		{
			echo "参数错误，server id 不是数字！请不要做任何手工修改http变量。";
			exit;
		}
		$query="select name,locate from svnauth_server where server_id=$serverid";
		$result=mysql_query($query);
		if($result and ($row= mysql_fetch_array($result, MYSQL_BOTH))) {
			$servername=$row['name'];
			$server=$row['locate'];
		}
		$inputstat=" readonly ";
		$query="select para,value from svnauth_para where server_id=$serverid";
		$result=mysql_query($query);
		while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
			$para_array[$row['para']]=$row['value'];
			if(($row['para']=='isremote')and($row['value']=='1'))
			{
				$isremote='checked';
			}
		}
	}

	include('./css.html');
}
?>
<form method='post' action=''>
<fieldset>
 <legend>svn服务器节点</legend>
<?php	echo $typeflag;?>
<div class='st'><br>节点名:<input type='text' class='ipt'  name='servername' id='servername' value=<?php echo $servername.$inputstat ?>> <span class='rt'> <a href="#" onclick="modify('servername')">修改</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip00')"><img src='../img/help.gif'></font></span><span id='readmetip00' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定svn服务器节点简称，由英文组成。</p>
 </span>
 <br>svn服务器:<input type='text' class='ipt'  name='server' id='server' value=<?php echo $server.$inputstat ?>> <span class='rt'> <a href="#" onclick="modify('server')">修改</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip0')"><img src='../img/help.gif'></font></span><span id='readmetip0' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定svn服务器显示名称，如果是远程svn服务器，请指定访问的url。</p>
 </span>
 <br><input type='checkbox' name='isremote' value='true'  id='isremote' onclick="showreadme('svnuserdiv')" <?php echo $isremote ?>><label for='isremote'>这是远程服务器</label>
 <span id='svnuserdiv'  style='display:none'><br>svn用户名:<input type='text' class='ipt'  name='svnuser' id='svnuser' value=<?php echo $para_array['svnuser'] ?>>(将自动创建)</span>
	<hr>
<br>1、权限控制文件路径：<input type='text' class='ipt'  name='accessfile' id='accessfile' value=<?php echo $para_array['accessfile'].$inputstat;?>> <span class='rt'> <a href="#" onclick="modify('accessfile')">修改</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip')"><img src='../img/help.gif'></font> <?php echo $err_acc ?></span><span id='readmetip' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定svn的权限控制文件access file的系统路径。</p>
 </span>
<br>2、用户文件passwd路径：<input type='text' class='ipt'   name='passwdfile' id='passwdfile'  value=<?php echo $para_array['passwdfile'].$inputstat;?>> <span class='rt'> <a href="#" onclick="modify('passwdfile')">修改</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip2')"><img src='../img/help.gif'></font> <?php echo $err_pass ?></span><span id='readmetip2' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定svn的用户密码文件passwd file的系统路径。</p>
 </span>

 <br>3、svn父目录url:<input type='text' class='ipt'   name='svnurl[]' id='svnurl'  value=<?php echo $para_array['svnurl'].$inputstat;?>> <span class='rt'><a href='' onclick=''>add</a> <a href="#" onclick="modify('svnurl')">修改</a>&nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip4')"><img src='../img/help.gif'></font></span><span id='readmetip4' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定通过web访问具体svn库的URL的父级目录。如：http://svnmaia.scmbbs.com/repos_parent/</p>
 </span>

	
<br>4、svn仓库父路径：<input type='text' class='ipt'   name='svnparentpath' id='svnparentpath'  value=<?php echo $para_array['svnparentpath'].$inputstat;?>>  <span class='rt'><a href="#" onclick="modify('svnparentpath')">修改</a> &nbsp;&nbsp;<font class=sf onclick="showreadme('readmetip6')"><img src='../img/help.gif'></font> <?php echo $err_svnpath ?></span><span id='readmetip6' class='sf' style='display:none'><p><br><b>说明：</b>【必填项】指定svn仓库群所在的系统路径，要与apache的SVNParentPath参数所指定一致。如：D:/svnroot/，对于windows系统请注意路径要用"/"做路径分割符而不是反斜线。</p>
 </span>
</div>
<div class='ft'>
<input type='submit' value='提交保存'>
<input type='button' value='返回' onclick="self.close()">
</div>
</fieldset>
</form>
