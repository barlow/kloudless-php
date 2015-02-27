<?php 
//
// Copyright Chris Barlow 2014-15
//
// This implements a basic folder explorer using kloudlessClass.php
// It was used to test the interface class as it was developed
//
// You may use this source code in any way you want. There are no restrictions.
// 

// The following just establishes a MYSQL connection so the code can use mysql_real_escape_string()
$hostname_localhost = "localhost";
$database_localhost = "_your_database_";
$username_localhost = "_your_userid_";
$password_localhost = "_your_password_";
$localhost = mysql_pconnect($hostname_localhost, $username_localhost, $password_localhost) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_localhost, $localhost);


include "kloudlessClass.php";

	
	$kapiid = "__put_your_API_id_here_";
	$kkey = "_put_your_key_here_";
	
	$k_accountid = "_put_your_account_id_here_";
	
	if(isset($_SESSION['currentFolder']))
		$folderID = $_SESSION['currentFolder'];
	else
		$folderID = "root";



	$k = new Kloudless();
	$k->init($k_accountid, $kkey, $kapiid);
	//pr($k->getFolderContents($folderID));
	//pr($k->rootInfo($folderID));

if(isset($_POST['home']))
{
	$_SESSION['currentFolder'] = $folderID = "root";
}

if(isset($_POST['selectFolder']))
{
	$folderID=mysql_real_escape_string($_POST['folderid']);
	$_SESSION['currentFolder'] = $folderID;
}

if(isset($_POST['up']))
{
	$finfo = $k->getFolderInfo($folderID);
	
	$_SESSION['currentFolder'] = $folderID = $finfo->parent->id;
	
}

if(isset($_POST['createfolder']))
{
	$name=mysql_real_escape_string($_POST['new_folder_name']);
	
	$result = $k->createFolder($folderID,$name);
}

if(isset($_POST['update']))
{
	$fileid=mysql_real_escape_string($_POST['fileid']);
	
	$result = $k->updateFile($fileid, $_FILES["ufile"]["tmp_name"]);
	unlink($_FILES["ufile"]["tmp_name"]);
	
}


if(isset($_POST['upload']))
{
	echo "Uploading<br>";
	$result = $k->uploadFile($folderID, $_FILES["ufile"]["name"], $_FILES["ufile"]["tmp_name"]);
	unlink($_FILES["ufile"]["tmp_name"]);
	
}

if(isset($_POST['delete']))
{
	$fileid=mysql_real_escape_string($_POST['fileid']);

	$result = $k->deleteFile($fileid);
}

if(isset($_POST['deletefolder']))
{
	$id=mysql_real_escape_string($_POST['folderid']);

	$result = $k->deleteFolder($id);
}
if(isset($_POST['deletefolder_r']))
{
	$id=mysql_real_escape_string($_POST['folderid']);

	$result = $k->deleteFolder($id, true);
}

if(isset($_POST['download2']))
{
	$fileid=mysql_real_escape_string($_POST['fileid']);

	$info = $k->getFileInfo($fileid);
	
	$data = $k->downloadFileLocally($fileid, "CHRIS.TXT"); // <-- obviously, plug in the file and path that you need
	
	


}
if(isset($_POST['download']))
{
	$fileid=mysql_real_escape_string($_POST['fileid']);
	$k->sendDownloadFile($fileid);
	exit;
}


if(isset($_POST['info']))
{
	$fileid=mysql_real_escape_string($_POST['fileid']);

	$info = $k->getFileInfo($fileid);
	
	pr($info);


}

if(isset($_POST['finfo']))
{
	$fileid=mysql_real_escape_string($_POST['folderid']);

	$info = $k->getFolderInfo($fileid);
	
	pr($info);


}

if($folderID)
{
	$finfo = $k->getFolderInfo($folderID);
	$folderName = $finfo->name;

	$info = $k->getFolderContents($folderID);
	//pr($info);
	$count = count($info->objects);
echo <<< END
<style>	
.table
{
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
	font-size: 11px;
	margin: 4px;
	width: 1030px;
	text-align: left;
	border-collapse: collapse;
}

.table th
{
	font-weight: normal;
	font-size: 12px;
	color: #000000;
	padding-top: 4px;
	padding-right: 3px;
	padding-bottom: 4px;
	padding-left: 3px;
	background-color: #b9c9fe;
}



.table td
{
	background: #e8edff;
	border-top: 1px solid #fff;
	color: #669;
	padding-top: 4px;
	padding-right: 2px;
	padding-bottom: 4px;
	padding-left: 2px;
}


#hrsweek tbody tr:hover td
{
	background: #d0dafd;
}
</style>


<TABLE class='table'>
			<TR><TH colspan='4'>$folderName [$count]</TH></TR>
			<TR><TH width="200px">Name</TH><TH>Size</TH><TH>Type</TH><TH></TH></TR>
END;
			
	foreach($info->objects as $iid=>$item)
	{
	
		if($item->type == "folder")
echo <<<END
<TR>
	<TD>{$item->name}</TD><TD>{$item->size}</TD><TD>{$item->type}</TD>
	<TD>
		<form action='$script' method='post' enctype='multipart/form-data' name='files$iid'>
		<input name="selectFolder" type="submit" value="Select">
		<input name="deletefolder" type="submit" value="Delete Folder">
		<input name="deletefolder_r" type="submit" value="Delete Everything">
		<input name="finfo" type="submit" value="Folder Info">
		<input name="folderid" type="hidden" value="{$item->id}">
		
		

		</form>
	</TD>
</TR>
END;
		else
echo <<<END
<TR>
	<TD>{$item->name}</TD><TD>{$item->size}</TD><TD>{$item->type}</TD>
	<TD>
		<form action='$script' method='post' enctype='multipart/form-data' name='files$iid'>
		<input name="delete" type="submit" value="Delete">
		<input name="download" type="submit" value="Download">
		<input name="download2" type="submit" value="Download2File">
		<input name="info" type="submit" value="File Info">
		<input name="fileid" type="hidden" value="{$item->id}">
		<input name="ufile" type="file">
		<input name="update" type="submit" value="Update">

		</form>
	</TD>
</TR>
END;

	
	
	}
	echo "</TABLE>";
	
}

echo <<<END

<form action="$script" method="post" enctype="multipart/form-data" name="fupload">
<input name="ufile" type="file">



<input name="upload" type="submit" value="Upload File">
<input name="fileid" type="hidden" value="{$result->id}">
<input name="home" type="submit" value="Home">
<input name="up" type="submit" value="Up">
<input name="new_folder_name" type="text" size="14" maxlength="50">
<input name="createfolder" type="submit" value="Create Folder">

</form>
END;

function pr($array)
{
	if(empty($array))echo "** NULL array<br />";
	echo "<pre>";
	print_r($array);
	echo "</pre>";

}

?>