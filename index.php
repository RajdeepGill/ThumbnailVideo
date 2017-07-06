<?php
class Download {

	const MAX_URL_LENGTH = 1050;
	//Clean URL
	protected function cleanURL($url){
		if(isset($url)){
			if(!empty($url)){
				if(strlen($url) < self::MAX_URL_LENGTH){
					return strip_tags($url);
				}
			}
		}
	}

	//Check URL
	protected function checkURL($url){
		$url = $this->cleanURL($url);
		if(isset($url)){
			if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)){
				return $url;
			}
			else{
				echo "not a valid URL";
			}
		}
	}

	//Get extension
	protected function getExtention($url){
		if($this->checkURL($url)){
			$end = end(preg_split("/[.]+/", $url));
				if(isset($end)){
					return $end;
				}
		}
	}

	//Download Video File
	public function downloadFile($url){
		if($this->cleanURL($url)){
			$extension = $this->getExtention($url);
			if ($extension) {
				//echo $url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$return = curl_exec($ch);
				curl_close($ch);

				$destination = "downloads/video.$extension";
				$file = fopen($destination, "w+");
				fputs($file, $return);
				if (fclose($file)) {
					echo "Video Downloaded";

					$thumbnail = 'thumbnails/thumbnail.jpg';

					// shell command [highly simplified, please don't run it plain on your script!]
					shell_exec("ffmpeg -i $file -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $thumbnail 2>&1");
				}
			}
		}
	}

}

$obj = new Download();
if (isset($_POST['url'])) {
	$url = $_POST['url'];
}
?>
<form action="http://localhost/index.php" method="post">
	<input type="text" name="url" maxlength="1000">
	<input type="submit" value="Download">
</form>

<?php
if (isset($url)) {
	$obj->downloadFile($url);
}


?>