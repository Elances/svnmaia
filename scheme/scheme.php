<?php
header("content-type:text/html; charset=gb2312");
?>
<meta http-equiv="Refresh" content="3600">
<strong>˵����</strong>����򿪱�ҳ��ʱ����ҳ��ᰴ��ϵͳ�趨���û���Ȩ����Ч�ڶ�Ȩ�޿����ļ����и��¡�
<p>
��ҳ�潫���Զ�ˢ�£���ά���û���Ȩ���ļ��ĸ��£������㲻Ҫ�رձ�ҳ�档
<br>
<p>
���������Խ���ҳ����Ϊ�ƻ�����ִ�У�����ÿ��ִ��һ�Σ��Ѵﵽ��ʱ����Ȩ�޵�Ŀ�ġ�
<br><br>�������£�
<br><strong>ҳ����ط�ʽ</strong>���ҵ�����ʵ�ҳ�棬�༭��ҳ��HTML���룬����body��ǩ�ڣ��������js���룺
<br>&lt;script src="<?php echo $_SERVER['PHP_SELF'] ?>"&gt;&lt;/script&gt;
<br> ����src="**"���ָ���˱�ҳ���urlλ�á� �����������˷��ʸ�ҳ��ʱ�ͻ��Զ����б�����ű���
<br><strong>Linuxϵͳ</strong>����crontab�����һ�У�
<br>  0 0 * * * "php -f /path/to/thefile/scheme.php"
<br><strong>Windowsϵͳ</strong>:�ڼƻ������У�
�򿪡�������塱-->˫�����ƻ�����-->���������-->ѡ�����г����У����������ڵ����Ի����У����뱾ҳ���url
�磺http://www.example.com/svnauth/scheme/scheme.php��Ȼ��һֱ����һ����ֱ����ɡ�
<br>
<div style="background:#B6C6D6;text-align:center;color:#fe392a;margin:20px 0 20px 0;">
<?php
include('../include/basefunction.php');
include('../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2)or die("<br>���ݿ�����ʧ�ܣ�����ϵ<a href='mailto:xuejiang.li@yahoo.com.cn'>����Ա</a>");
if (mysql_select_db(DBNAME))
{
	//�û��Ƿ񼴽����ڣ���ǰ2�����ڷ���֪ͨ���м���
	//
	//
	//����û��ѹ��ڣ��������Ѵ�������3�Σ���ɾ���û�����������Ч��
	//$expire=mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
	//$expire=strftime("%Y-%m-%d",$expire);
	$expire=date('Y-m-d' , strtotime('+2 week')); 
	$query="delete from svnauth_user where infotimes > 2 and expire < NOW()";
	$valuechanged=false;
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;
	$query="select user_id,user_name,email,infotimes,expire from svnauth_user where expire < \"$expire\"";
	$result=mysql_query($query);
	include('../include/email.php');
	if(file_exists('./tmp'))
	{
		$d=file_get_contents('./tmp');
		$tmpd=(strtotime($d)-strtotime(date('Y-m-d')))/86400;
		if($tmpd==0){
			echo "������ִ�й�������";
			exit;
		}
	}
	$handle=fopen('./tmp','w+');
	fwrite($handle,date('Y-m-d'));
	fclose($handle);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH)))
	{
		$infotimes=(empty($row['infotimes']))?0:$row['infotimes'];
		if($infotimes>3)continue;
		$infotimes++;
		$expire=$row['expire'];
		//$expire=strtotime("+7 day",strtotime($expire)");//����1week
		$user=$row['user_name'];
		$uid=$row['user_id'];
		$email=(empty($row['email']))?$user:$row['email'];
		$para=array($user,$email,$uid);
		$sig=keygen($para);
		//���ʼ�֪ͨ����
		$url="http://".$_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']))."/activeuser.php";
	        $url=$url."?sig=$sig&u=$user&uid=$uid&email=$email";
		$body="��ע�⣺����svn�û������� $expire ���ڣ��û�����$user\n
	���ں�����svn�˻������Զ�ɾ����\n
	�������Ҫ��������svn�������������ӽ�����Ϣȷ�ϣ�������������\n
			$url

	������Ѳ���Ҫ������Ա��ʼ���";
		$subject="֪ͨ������svn�˻��������ڣ�";
		$mail_info=send_mail($email,$subject,$body);
		//��¼���η��ʼ��¼�
		$query="update svnauth_user set infotimes=$infotimes where user_id=$uid";
		mysql_query($query);
		echo mysql_error();
		if($mail_info === true)
		{
			echo "<br>$user �û��������ڣ��ѷ��ʼ�֪ͨ�伤��������";
		}
		else
			echo "<br>$user �û��������ڣ�����֪ͨ�ʼ�ʱ�������󣬿��ܸ��û�û���յ���<br>$mail_info";

	}


	//�ж�дȨ���Ƿ���ڣ�����ѹ��ڣ����Ϊֻ��Ȩ�ޡ�
	$expire=date('Y-m-d' , strtotime('+20 week'));
	$query="update svnauth_permission set permission='r', expire=\"$expire\" where expire <= NOW() and permission='w' ";
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;


	//�ж϶�Ȩ���Ƿ���ڣ�����ѹ��ڣ����Ϊ��Ȩ�ޡ�
	$query="update svnauth_permission set  permission='n', expire=\"$expire\" where expire <= NOW() and permission='r' ";
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;
	//��Ч
	if($valuechanged)
	{
		$scheme=true;
		@include('../priv/gen_access.php');
	}
}
?>
</div>
