<?php
	include('../config/config.php');
header("content-type:text/html; charset=gb2312");
?>
<!--
Author:lixuejiang
Site:http://www.scmbbs.com
Date:2009-02-19
-->
<html>
<head>
  <title>svn�û�ע��</title>
</head>
<style type='text/css'>
h1{text-align:center;}
p{margin-left:40px}
div{margin:15px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
 a{color:green;text-decoration:underline;}
.m{color:red;font-size:12px}
td{height:35px;}
.lb{width:122px;text-align:right;}
</style>
<body>

  <p>
  <noscript><strong>�����������֧��script�ű���<br>�û�ע�Ṧ�ܽ���������ʹ�ã�<br></noscript>
  	<form name='regform' action=reg.php method=post onsubmit="return fCheck()">
	<fieldset>
  	 <legend>ע��subversion�û�</legend>
   	<table>
	<tr>
		<td  class='lb'>�û�����</td>
		<td ><input type="text" name="username" size="20" onblur="addemail()"> * <span class='m'>������������ǰ׺����һ�£�����ĸ���</span></td>
 	</tr>
 
	<tr>
		<td  class='lb'>���룺</td>
		<td ><input type="password" name="passwd" size="20"> * </td>
	</tr>
	<tr>
		<td  class='lb'>ȷ�������룺</td>
		<td><input type=password name="passwd0" size="20"> *</td>
	</tr>
	<tr>
		<td  class='lb'>����������</td>
		<td ><input type="text" name="fullname" size="20" > * <span class='m'>����д��ʵ����</span></td>
 	</tr>
 	<tr>
		<td  class='lb'>���ţ�</td>
		<td ><input type="text" name="staff_no" size="20" >  <span class='m'>ʵϰ���ɲ���</span></td>
 	</tr>	
 	<tr>
		<td  class='lb'>���ţ�</td>
		<td ><input type="text" name="department" size="20" >  <span class='m'></span></td>
	</tr>	
		<tr>
		<td  class='lb'>�����ʼ���</td>
		<td ><input type="text" name="email" size="20" > </td>
 	</tr>	
 </table>
	<table border="0" width="84%" id="table2">
	<tr>
		<td width="104"><input type=button value="�ύ" onclick="return tCheck()"></td>
		<td><input type=reset value="ȡ��"></td>
		<td align=right><a href='./viewuser.php'>�޸��û�</a></td>
		</tr>
	</table>
</fieldset>
	</form>

</body>
</html>
<script language="javascript">
	<!--
	

function addemail()
{
	regform.email.value=regform.username.value+"<?php echo $email_ext;?>";
}
 function fCheck(){
 		 
 
 if( regform.username.value =="") 
 {
       alert("\�û�������Ϊ��!");
       regform.username.focus();
       return false;
  }
 if( regform.passwd.value =="") 
 {
       alert("\���벻��Ϊ��!");
       regform.passwd.focus();
       return false;
  }

   if( ! isPassword( regform.passwd.value ) )
   {
        alert("\��������������,����������6��Ӣ����ĸ��������� !"); 
        regform.passwd.select();
        regform.passwd.focus();
        return false;
   }
  if( regform.passwd0.value =="" )
  {
      alert("\����������ȷ�� !");
      regform.passwd0.select();
      regform.passwd0.focus();
      return false;
  }
  if(  regform.passwd0.value != regform.passwd.value ) {
     alert("\�����������벻һ�� !");
     regform.passwd.focus();
     return false;
  }
 if( regform.fullname.value =="" )
  {
      alert("\������������ʵ����!");
      regform.fullname.select();
      regform.fullname.focus();
      return false;
  }
function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
  return true;
}  
function tCheck()
{
	if(!fCheck())return false;
	loadTip();
	return true;
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
function displayTip(content) {
	alert(content);
}
//�ӷ��������عؼ��ʵ���ϸ��Ϣ
function loadTip() {
    if(!fCheck())return false;
   var username="username="+regform.username.value
      +"&passwd="+regform.passwd.value+"&passwd0="+regform.passwd0.value
      +"&fullname="+regform.fullname.value
      +"&staff_no="+regform.staff_no.value
      +"&email="+regform.email.value;
    createXmlHttp();                                //����XMLHttpRequest����
    xmlHttp.onreadystatechange = loadTipCallBack;   //���ûص�����
    xmlHttp.open("POST", "./reg.php", true);
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlHttp.send(username);
}

//��ȡ��ѯѡ��Ļص�����
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //��ʾ������ϵ���ϸ��Ϣ
        if(xmlHttp.responseText=="�û�ע��ɹ���")regform.reset();
        regform.username.focus();
        
    }
}

	-->
</script>

