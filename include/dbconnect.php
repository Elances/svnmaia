<?php
	$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("���ݿ�����ʧ�ܣ�����ϵ����Ա");
	mysql_select_db(DBNAME) or die("����ѡ�����ݿ⣡");
//	mysql_query("SET NAMES UTF8"); 
	$program_char='gbk';

	$pattern='/(\d+)\.\d+\.\d+/i';
# ��������������룬��ȥ��������ӵ�ע�ͷ�'#'
#	preg_match($pattern,mysql_get_server_info(),$out);
	if($out[1] > 4) //mysql version > 4
	{

//ȡ����ǰ���ݿ���ַ���

	$sql='SELECT @@character_set_database';
	$result = mysql_query($sql);
	$char=mysql_result($result,0);
	mysql_query("SET character_set_connection=$char, character_set_results=$program_char, character_set_client=binary",$mlink);

	}

?>
