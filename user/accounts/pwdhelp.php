<?php
header("content-type:text/html; charset=gb2312");
?>
<html>
<head>
  <title>�һ�svn������</title>
</head>
<style type='text/css'>
div{margin:15px}
fieldset{border:solid 1px gray;}
</style>
<body>
   <h2>�һ��������</h2>
   <?php
//У����úϷ��ԡ�����ͨ��seraph��֤�û��ſ��Ե��ñ��ļ���
$nt=microtime();
$nt=str_replace(" ","",$nt);
$nt=str_replace("0.","",$nt);
$nt=substr($nt,6);
$ss=$_GET['ss'];
$nt=$nt-substr($ss,6);
if($nt>3600*2)
{
	echo "�����ѹ��ڣ�";
	exit;
}
$sig1=$_GET['sig'];
$addr=$_SERVER['REMOTE_ADDR'];
include('../../../../config.inc');
include('../../config/config.php');
$sig=md5($ss.SECRET_KEY.$addr);
if($sig1 != $sig)
{
	echo "�Ƿ����ã�";
	exit;
}
//��ȡcookie����ȡemail��Ϣ��
$cookie=explode('&',$_COOKIE['CNSSO']);
foreach($cookie as $name)
{
	if(stristr($name,'userid='))
	{
		$uEmail=str_replace('userid=','',$name).$email_ext;
		break;
	}
}
?>
   <form action=./sendmail.php name=pwdform method=post onSubmit="return tCheck()">
   	<fieldset>
   <div id='inputblock'>
   <h4>�������б������������svn�û�����ϵͳ������û����������������ӷ��͵�����������<br>��ע������ʼ���</h4>
   		
   <table>
   <tr><td>������svn�û�����</td><td><input type=text name=username></td></tr>
   <tr><td>&nbsp; </td></tr>
   <tr><td><input type=reset value="ȡ��"></td><td><input type=button value="��һ��" onclick="loadTip()"></td></tr>
   </table>  
  </div>
  <div id='confirmblock' style="display:none;">
  	<h4>��ȷ��������Ϣ��</h4>
  	<table>
  		<tr><td>����svn�û�����<input type=text readonly id='user'></td></tr>
  		<tr><td>��˾���䣺<input type=text readonly id='email' style="width:250px"></td></tr>
  		<tr><td>���������뽫�����͵��������У�����ǰ����ϸȷ��<font color=red>�����ַ</font>��
  			<br>��������ַ���ԣ��뵽<a href='../viewuser.php' target=_blank>svn�û�ϵͳ</a>��½�޸ģ�������ϵ�����޸ġ�</td></tr>
  		<tr><td>&nbsp; </td></tr>
  		<tr><td><input type=button value="��һ��" onclick='turnback()'>&nbsp;<input type=button style="width:80;margin-left:180px" value="ȷ��" onclick="return tCheck()"></td></tr>  		
  	</table>
  </div>
  </fieldset>
   </form>
</body>

</html>
<script language="javascript">
<!--
function turnback(){
	window.location.href = window.location.href;	
}
function fCheck(){
	
  if( pwdform.username.value =="" ) {
      alert("\�������û��� !");
      pwdform.username.select();
      pwdform.username.focus();
      return false;
  }
  return true;
}
function tCheck()
{
	if(!fCheck())return false;
	if(document.getElementById('email').value == '�û������ڣ�')
	{
		alert('���û��������ڣ���ȷ�ϣ�');
		return false;
	}
	if(document.getElementById('email').value != "<?php echo $uEmail ?>")
	{
	//	alert('�������ַ������ʵ�����ַ������');
	//	return false;
	}
	pwdform.submit();
	return true;
}
//����ϸ��Ϣ�ľ�������д��tipDiv��
function displayTip(content) {
    document.getElementById('confirmblock').style.display='';
    document.getElementById('inputblock').style.display='none';
    document.getElementById('user').value =pwdform.username.value;
    document.getElementById('email').value = content;
    
}

//���ڴ���XMLHttpRequest����
function createXmlHttp() {
    //����window.XMLHttpRequest�����Ƿ����ʹ�ò�ͬ�Ĵ�����ʽ
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox��Opera�������֧�ֵĴ�����ʽ
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE�����֧�ֵĴ�����ʽ
    }
}

//�ӷ��������عؼ��ʵ���ϸ��Ϣ
function loadTip() {
    if(!fCheck())return false;
    displayTip("���ڼ��ء���");                  //��ʾ�����ڼ��ء�������ʾ��Ϣ

    createXmlHttp();                                //����XMLHttpRequest����
    xmlHttp.onreadystatechange = loadTipCallBack;   //���ûص�����
    xmlHttp.open("GET", "./getusers.php?username=" + pwdform.username.value +"&"+Math.round(Math.random()*100), true);
    xmlHttp.send(null);
}

//��ȡ��ѯѡ��Ļص�����
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //��ʾ������ϵ���ϸ��Ϣ
    }
}

-->
</script>
