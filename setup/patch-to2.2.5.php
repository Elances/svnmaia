<?php
# ��2.2.4�汾�����°汾������2.2.5ʱ��Ҫִ�д˲���
include('../config/config.php');
include('../../../config.inc');
include('../include/dbconnect.php');
$query="alter table svnauth_user modify fresh  bit(1) default 0";
mysql_query($query);
echo mysql_error();
$query="update svnauth_user set fresh=0";
mysql_query($query);
echo mysql_error();
echo "��������������ʾ�������ݿ��Ѹ��ĳɹ���";
unlink('./patch-to2.2.5.php');


