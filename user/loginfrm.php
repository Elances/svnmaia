<?php
header("content-type:text/html; charset=gb2312");
?>
<html>
<head>
<title>svn�û�Ȩ�޹���ϵͳ---�û���½</title>
<style type="text/css">
form{margin:100px 170px;padding:20px;}
.in{width:140px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:400px;}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
.tp{margin:40px 40px 0px 0px;}
</style>
</head>
<body>
<form name="loginform" method="post" action="login.php?action=login">
<fieldset>
<legend>��½svn�û�ϵͳ</legend>
<div class='tp'>
	<table border="0" width="100%" id="table1">
		<!-- MSTableType="nolayout" -->
		<tr>
			<td width="68">�û�����</td>
			<td ><input type="text" name="username" class='in' >��</td>
		</tr>
		<tr>
			<td>���룺</td>
			<td ><input type=password name="pswd"  class='in' >&nbsp;
			<a href="../extension/topwd.php">�һ�����</a></td>
		</tr>
	</table>
</div>
<div class='tp' >
��<table border="0" width="100%" id="table2">
		<!-- MSTableType="nolayout" -->
		<tr>
			<td width="105"><input type=submit value="ȷ��" style="width:80"></td>
			<td><input type=reset value="ȡ��" style="width:80"> </td>
            <td><a href="reg_user.php">ע��</a></td>
		</tr>
	</table>
</div>
</fieldset>
</form>
<script>loginform.username.focus();</script>
</body>
</html>
