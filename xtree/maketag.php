<?php
session_start();
$dir=$_GET['d'];
if(empty($dir))$para='&parentId=0';
$path_arr=explode('/',dirname($dir));
?>
<head>
<link rel="stylesheet" href="xtree.css" type="text/css" media="screen">
<script type="text/javascript" src="xtree.js" language="JavaScript"></script>
<script type="text/javascript" src="xmlextras.js" language="JavaScript"></script>
<script type="text/javascript" src="xloadtree.js" language="JavaScript"></script>
</head><body>
<?php
function setUsername() {
    if(isset($_SESSION['username']))
    {
	    return $_SESSION['username'];
    }
    if (isset($_SERVER["REMOTE_USER"])) {
      return $_SERVER["REMOTE_USER"];
    } else if (isset($_SERVER["REDIRECT_REMOTE_USER"])) {
      return $_SERVER["REDIRECT_REMOTE_USER"];
    } else if (isset($_SERVER["PHP_AUTH_USER"])) {
      return $_SERVER["PHP_AUTH_USER"];
    }else return $_SERVER["REMOTE_ADDR"];

}
include('../config/config.php');

$username=setUsername();
if(isset($_POST['tags']))
{
	$src=escapeshellarg($_POST['src']);
	$dst=escapeshellarg($_POST['dst']);
	$tags=escapeshellarg($_POST['tags']);
	$m=escapeshellarg($_POST['cmt']);
	$src='file:///'.str_replace($svnurl,$svnparentpath,$src);
	$dst='file:///'.str_replace($svnurl,$svnparentpath,$dst).$tags;	
	if(empty($m))$m="\"created by $username\"";
	system("{$svn}svn cp $src $dst -m{$m} 2>&1 ");
}

?>
<h2>���ǩ����</h2>
�������÷���svn cp  SRC[@REV]... DST
<br>&nbsp;
<br>��tag����<b>ʹ�÷���</b>��
<br>1��ѡȡsrc·�������[ѡ��](����ѡȡ,����[�ɿ�])
<br>2��ѡȡdst·�������[ѡ��]
<br>3���޸ı�ǩ��(��'_R'ǰ���ϰ汾�ţ�����<a href="http://wiki.corp.cnb.yahoo.com/wiki/index.php/SNS%E6%A0%87%E7%AD%BE%E5%91%BD%E5%90%8D%E8%A7%84%E8%8C%83" target=_blank>��ǩ�����淶</a>)����������ǩ��
<?php if($_SESSION['role']=='admin')echo "<br>��ǰ�û���".$username; ?>
<hr>
<?php
foreach($path_arr as $path)
{
	if(empty($path))continue;
	$v .='/'.$path;
	echo "<a href=?d=$v>$path</a>/";
}
?>
<script type="text/javascript">
var tree= new WebFXLoadTree('<?php echo $dir ?>','svntree.php?<?php echo 'd='.$dir.$para."','javascript:setpath($dir);" ?>','explorer');
tree.setBehavior('explorer');
document.write(tree);
function set(id)
{
	var vl=document.getElementById(id).value;
	if(vl =='ѡ��')	document.getElementById(id).value='�ɿ�';
	else document.getElementById(id).value='ѡ��';
}
function setpath(path)
{
	var url='<?php echo $svnurl ?>/';
  var src=document.getElementById('bt1').value;
  var dst=document.getElementById('bt2').value;
  if(src == 'ѡ��')document.getElementById('src').value=url+path;
  if(dst == 'ѡ��'){
	  document.getElementById('dst').value=url+path+'/';
     var tg=path.substr(path.lastIndexOf('/')+1);
     var myDate = new Date();   
     var mon=myDate.getMonth()+1;
     if(mon<=9)mon='0'+mon;
     document.getElementById('tags').value=tg+'_R_'+myDate.getFullYear()+mon+ myDate.getDate();
    // document.getElementById('tags').value=tg+'_R_'+myDate;
  }
}
function sbmt()
{
	var src=document.getElementById('src').value;
        var dst=document.getElementById('dst').value;
	var tags=document.getElementById('tags').value;
	if(dst==(src+'/'))
	{
		alert('Error:������ѡ����ǩ��λ�ã�');
		return false;
	}
	if(confirm("ȷʵҪ��:\n"+src+"\n���ǩ��:\n"+dst+tags+" ��\n"))
	{
		tagfrm.submit();
	}else return false;
   
}
</script>
<hr>
<form method='post' action='' name='tagfrm'>
��Ŀ¼��<input name='src' id='src' type=text readonly style='width:400'> <input type=button id='bt1' value='ѡ��' onclick="set('bt1')">
<br>���ǩ����<input name='dst' id='dst' type=text readonly style='width:400'><input type=text name='tags' id='tags' style='width:180' > <input type=button id='bt2' value='ѡ��' onclick="set('bt2')">&nbsp;&nbsp;<img alt='write down some comments' src='../img/comments.ico' onclick="document.getElementById('cmt').style.display='';"><textarea id='cmt' name='cmt' cols=20 rows=2 style="display:none"><?php echo "created by $username" ?></textarea>
<br><input type=button value='���ǩȷ��' onclick='sbmt()'>
</form>
