<?php
session_start();
if(!isset($_SESSION['role']))exit;
header("Content-Type: text/xml;  charset=gb2312");
$d=$_GET['parentId'];
$path=escapeshellcmd($_GET['d']);
include('../config/config.php');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<tree>";
if($d == '0')
{
    $sp = opendir( $svnparentpath );
    if( $sp ) {
	    $id=1;
        while( $dir = readdir( $sp ) ) {
            $svndir = $svnparentpath . "/" . $dir;
	    $svndbdir = $svndir . "/db";
	    $svnhooksdir=$svndir ."/hooks";
	    if( is_dir( $svndir ) && is_dir( $svndbdir ) && is_dir($svnhooksdir)) {
		    $url2="../priv/dirpriv.php?d=$dir"; 
		    $url="./tree.php?d=$dir";
		    echo "<tree src=\"$url\" target=\"rt1\" action=\"$url2\" text=\"$dir\"/>\n";
		    $id++;
	    }
	}
    }

}else
{
	$dirs_arr=array();
	$localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$path"):("file:///$svnparentpath/$path");
	$svnlist=exec("{$svn}svn list ".$localurl,$dirs_arr);
	$i=1;
	foreach($dirs_arr as $dir)
	{
		if($dir{strlen($dir)-1}=='/')
		{
			$url2="../priv/dirpriv.php?d=$path/$dir";
			$url="./tree.php?d=$path/$dir";
			echo"<tree src=\"$url\" target=\"rt1\" action=\"$url2\" text=\"$dir\"/>\n";
		    $i++;
		}
	}
}
?>
</tree>
