<?php
//1����CNSSO��cookieʧЧ�����
//2������ʱ�����SECRETKEY��cookie=none����md5ǩ��
//3��header��pwdhelp.php�ļ���
setcookie("CNSSO","",time()-36000,"/",".yahoo.com",1);
/*
if (!empty($_COOKIE['CNSSO'])) 
{ echo $_COOKIE['CNSSO'];
exit;
}
*/
$ss=microtime();
$ss=str_replace(" ","",$ss);
$ss=str_replace("0.","",$ss);
$salt=mt_rand();
$addr=$_SERVER['REMOTE_ADDR'];
include('../../../config.inc');
$sig=md5($ss.SECRET_KEY.$addr);
@header("Location:../user/accounts/pwdhelp.php?ss=$ss&sig=$sig&salt=$salt");
echo "<script>setTimeout('document.location.href=\"../user/accounts/pwdhelp.php?ss=$ss&sig=$sig&salt=$salt\"',0)</script>";
?>
<script lang=javascript>
	function DelCookie(name) {
var Days = 5000; //�� cookie �������� 30 ��
    var exp  = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() - Days*24*60*60*1000);
    document.cookie = name + "=' '; path=/; expires=" + exp.toGMTString();
}
DelCookie("CNSSO");
</script>

