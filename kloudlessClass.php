<?php 
//
// Copyright Chris Barlow 2010-14
//
// You may do whatever you want with this source code - there are no restrictions.
//
//

class Kloudless
{
    // property declaration
    public $accountid, $keyid, $apiid, $cheader;
	

    // method declaration
			
	public function init($account_id, $key_id, $api_id) 
	{
		$this->accountid = $account_id;
		$this->keyid = $key_id;
		$this->apiid = $api_id;
		$this->cheader = array('Authorization: ApiKey ' . $key_id);
		
	}	

	function getFileInfo($fileid)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid"; 
		
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
		
	
	
	}

	function downloadFile($fileid)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid/contents"; 
		
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		
		$hdr = substr($result, 0, $info['header_size']);
		
		$result = substr($result, $info['header_size']);
		
		return $result;
		
	
	
	}


	function downloadFileLocally($fileid, $targname)
	{
	 	global $GlobalFileHandle;
		
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid/contents"; 
		
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
	    $GlobalFileHandle = fopen($targname, 'w+');


		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY,
			
			CURLOPT_BUFFERSIZE => 64000,
			CURLOPT_FILE => $GlobalFileHandle,
			CURLOPT_BINARYTRANSFER => true,
			CURLOPT_TIMEOUT => -1,
			CURLOPT_WRITEFUNCTION => 'curlWriteFile'
			
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		curl_close($ch);
		fclose( $GlobalFileHandle);
		
				
		return;
		
	
	
	}
	
	function sendDownloadFile($fileid, $altName="")
	{
		$info = $this->getFileInfo($fileid);
		//pr($info);
		header("Content-type: ".$info->mime_type);
		if($altName)
			header("Content-Disposition: attachment;Filename=\"{$altName}\"");
		else
			header("Content-Disposition: attachment;Filename=\"{$info->name}\"");
	
	
		header("Cache-Control: private");
		
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid/contents"; 
		
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY,
			CURLOPT_BUFFERSIZE => 64000
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		echo curl_exec($ch);
		
		return;

	}



	function deleteFile($fileid)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid"; 
		
		

		$chdr[]="Content-Type:multipart/form-data";
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_CUSTOMREQUEST => 'DELETE',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
		
	
	
	}
	
	function deleteFolder($folderID, $recursive=false)
	{
		$ch = curl_init(); 
		
		if($recursive)
			$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders/$folderID/?recursive=true"; 
		else
			$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders/$folderID/"; 
		

		$chdr[]="Content-Type:multipart/form-data";
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_CUSTOMREQUEST => 'DELETE',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
		
	
	
	}



	function rootInfo()
	{
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders/root"); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->cheader);	
		
		$result=json_decode(curl_exec ($ch));
		curl_close ($ch);  
		return $result;
	}
	
	function getFolderInfo($folderID)
	{
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders/$folderID"); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->cheader);	
		
		$result=json_decode(curl_exec ($ch));
		
		curl_close ($ch);  
		return $result;
	}
	
	function getFolderContents($folderID)
	{
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders/$folderID/contents"); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->cheader);	
		
		$result=json_decode(curl_exec ($ch));
		curl_close ($ch);  
		return $result;
	}
	
	function updateFile($fileid, $filepath)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files/$fileid"; 
		
		
		$postfields = array("file" => "@$filepath");

		$chdr[]="Content-Type:multipart/form-data";
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_POST => 1,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_POSTFIELDS => $postfields,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY,
			CURLOPT_CUSTOMREQUEST => "PUT"
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
	
	
	}
	
	
	function uploadFile($folderID, $filename, $filepath)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/files"; 
		
		$meta_data['parent_id']=$folderID;
		$meta_data['name']=$filename;
		$meta=json_encode($meta_data);
		$postfields = array("file" => "@$filepath", "metadata"=>$meta);

		$chdr[]="Content-Type:multipart/form-data";
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_POST => 1,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_POSTFIELDS => $postfields,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		FHTlog("33333", $options);
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
	
	
	}
	
	function createFolder($parentID, $folderName)
	{
		$ch = curl_init(); 
		$url = "https://api.kloudless.com/v0/accounts/{$this->accountid}/folders"; 
		
		$meta_data['parent_id']=$parentID;
		$meta_data['name']=$folderName;
		$meta=json_encode($meta_data);
		

		$chdr[]="Content-Type:application/json";
		$chdr[]="Authorization: ApiKey " . $this->keyid; 		
		
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_POST => 1,
			CURLOPT_HTTPHEADER => $chdr,
			CURLOPT_POSTFIELDS => $meta,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY
		); // cURL options
		
		curl_setopt_array($ch, $options);
	
		$result = curl_exec($ch);
		
		// Find length of header and remove - could have used curl_setopt($ch, CURLOPT_HEADER, false); but I dont know
		$info   = curl_getinfo($ch);
		$result = substr($result, $info['header_size']);
		
		return json_decode($result);
	
	
	}
	

}


function curlWriteFile($cp, $data) {
	  global $GlobalFileHandle;
	  $len = fwrite($GlobalFileHandle, $data);
	  return $len;
	}




















?>