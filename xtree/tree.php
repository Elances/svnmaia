<?php
session_start();
header("Content-Type: text/xml;  charset=utf-8");
$d=$_GET['parentId'];
$path=urlencode(escapeshellcmd($_GET['d']));
$path=str_replace('%2F','/',$path);
include('../config/config.php');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<tree>";
if($d == '0')
{
	if(!isset($_SESSION['role']))
	{
		echo '</tree>';
		exit;
	}
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
	if(!isset($_SESSION['role']))
	{
		echo '</tree>';
		exit;
	}
	$dirs_arr=array();
	$localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$path"):("file:///$svnparentpath/$path");
	$svnlist=exec("{$svn}svn list ".$localurl,$dirs_arr);
	$i=1;
	foreach($dirs_arr as $dir)
	{
		if($dir{strlen($dir)-1}=='/')
		{
			$dir_utf=$dir;
			if(substr_count($dir,'?\\')>1)
			{
				$pattern = '/\?\\\(\d{3})/i';
				preg_match_all($pattern,$dir,$out);
				foreach($out[0] as $key =>$bitde)
				{
					$hexbit=dechex($out[1][$key]);
					$dir=str_replace($bitde,'%'.$hexbit,$dir);
				}
				$dir_utf=$dir;
				$dir = urldecode($dir);
			} 
			$url="./tree.php?d=$path/$dir_utf";
			$path_raw=urldecode($path);
			$url2="../priv/dirpriv.php?d=$path_raw/$dir";
			echo"<tree src=\"$url\" target=\"rt1\" action=\"$url2\" text=\"$dir\"/>\n";
		    $i++;
		}
	}
}
?>
</tree>
