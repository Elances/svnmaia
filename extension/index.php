<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['username'])){	
//	exit;
}
header("content-type:text/html; charset=gb2312");
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
//	echo "����Ȩ���д˲�����";
//	exit;
}
?>
<style type='text/css'>
.left{float:left;width:49%;border:1px solid lightpink;}
.right{float:right;width:49%;border:1px solid lightblue;}
br{clear:both;}
.st{margin-left:10px;line-height:30px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
</style>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<h2>����</h2>
<br>
<div class='left'>
 <h3>Ȩ�޿������û��ļ�</h3>
 <div class='st'>
 <a href='./showaccess.php' target='_blank'>�鿴Ȩ�޿����ļ�</a>
 <br><a href='../user/gen_passwd.php'>�����û��ļ�</a>
 <br><a href='../priv/gen_access.php'>����Ȩ�޿����ļ�</a>
 </div>
 <h3>�ƻ�����</h3>
 <div class='st'>
 <a href='../scheme/scheme.php' target='_blank'>��������ƻ�����</a>
 <br><a href='../scheme/cleanuser.php'>�����û�����ƻ�</a>
 </div>
 <h3>��ʼ��</h3>
 <div class='st'>
 <a href='../user/import_user.php' onclick="">��passwd�ļ������û�����</a>
 <br><a href='../priv/import_access.php' onclick="">���µ���accessȨ������</a>
 </div>
 </div>
<div class='right'>
 <h3>�û�����</h3>
 <div class='st'>
  <a href='../extension/pwdhelp.php' target='_blank'>�޸����빤��</a>
  <br><a href='../user/reg_user.php' target='_blank'>�û�ע�Ṥ��</a>
  <br><a href='../extension/topwd.php' target='_blank'>�һ����빤��</a>
 </div>
 <h3>Ȩ������</h3>
  <div class='st'>
  Ȩ������
  </div>
<h3>�Զ��幤�߼�</h3>
<div class='st'>
<?php
if(file_exists('./addon.ini'))
{
	$f=true;
	$url_array=parse_ini_file('./addon.ini',true);
	foreach($url_array as $comment=>$url_det)
	{
		$br='<br>';
		if($f)
		{
			$f=false;
			$br='';
		}
		if(!isset($url_det['url']))continue;
		$target=(isset($url_det['target']))?('target='.$url_det['target']):("");
		echo "$br <a href=".$url_det['url']." $target>$comment</a>\n";
	}
}
?>
</div>
</div>

