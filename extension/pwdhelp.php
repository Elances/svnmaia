<?php
header("content-type:text/html; charset=gb2312");
?>
<html>
<head>
<title>svn�û�Ȩ�޹���ϵͳ---�����޸�</title>
<style type="text/css">
form{margin:100px 170px;padding:20px;}
.in{width:140px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:400px;}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
.tp{margin:40px 40px 0px 0px;}
</style>
</head>
<body>
</p>
  <form id="chpasswd" method="post" action="pwdch.php" onSubmit="return pcheck()">
<fieldset>
	<legend>�޸�svn����</legend>
	<table>
		<tr>
			<td width="122" height="19">�û�����</td>
			<td height="19"><input type="text" name="username" size="20" > *</td>
  	</tr>

		<tr>
			<td width="122" height="19">ԭ���룺</td>
			<td height="19"><input type="password" name="oldpasswd" size="20"> * </td>
	</tr>
	<tr>
		<td width="122">�����룺</td>
		<td><input type=password name="newpasswd" size="20"> *		</td>
	</tr>
	<tr>
		<td width="122">ȷ�������룺</td>
		<td><input type=password name="newpaswd0" size="20"> *</td>
	</tr>
	</table>
	
	
	<table border="0" width="84%" id="table2">
	<tr>
		<td width="104"><input type=submit value="ȷ��" ></td>
		<td><input type=reset value="ȡ��"></td>
		<td><a href='topwd.php'>�һ�����</a></td>
	</tr>
</table>
</fieldset>
</form>
<script language="javascript">
	<!--
function pcheck(){

   if( chpasswd.oldpasswd.value =="") 
 {
       alert("\������ԭ���� !");
       chpasswd.oldpasswd.focus();
       return false;
  }

   if( ! isPassword( chpasswd.newpasswd.value ) )
   {
        alert("\��������������,����������6��Ӣ����ĸ��������� !"); 
        chpasswd.newpasswd.select();
        chpasswd.newpasswd.focus();
        return false;
   }
  if( chpasswd.newpaswd0.value =="" )
  {
      alert("\����������ȷ�� !");
      chpasswd.newpaswd0.select();
      chpasswd.newpaswd0.focus();
      return false;
  }
  if(  chpasswd.newpaswd0.value != chpasswd.newpasswd.value ) {
     alert("\�����������벻һ�� !");
     chpasswd.newpasswd.focus();
     return false;
  }

function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
}  


	-->
</script>
</body>
</html>
