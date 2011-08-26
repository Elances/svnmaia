<?php
# 从2.2.4版本及以下版本升级到2.2.5时需要执行此补丁
include('../config/config.php');
include('../../../config.inc');
include('../include/dbconnect.php');
$query="alter table svnauth_user modify fresh  bit(1) default 0";
mysql_query($query);
echo mysql_error();
$query="update svnauth_user set fresh=0";
mysql_query($query);
echo mysql_error();
echo "如无其他错误显示，则数据库已更改成功！";
unlink('./patch-to2.2.5.php');


