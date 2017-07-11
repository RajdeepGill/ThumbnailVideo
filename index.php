<!doctype html>
<head>
<meta charset="utf-8">

<title>Thumbnail Video.</title>

<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<meta name="description" content="MCS Programming Question - Thumbnail Video.">
<meta name="author" content="Rajdeep Gill">
</head>

<body>

<div class="container">
	<form class="form-signin" action="http://localhost/index.php" method="post">
    <h2 class="form-signin-heading">Please enter video URL</h2>
    <div class="input-group">
		<input type="text" name="url" class="form-control" placeholder="Video address" required="" autofocus="">
		<span class="input-group-btn">
        	<button class="btn btn-default" type="submit">Download!</button>
      	</span>
    </div><!-- /input-group -->
	</form>
	<br>
    <div  class="thumbnail">
      <img id="myThumnail" src="thumbnails/image.jpg?=<?php echo filemtime($filename)?>" alt="thumbnail">
    </div>
    <div class="progress">
    	<div id="theprogressbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">0% Complete
  		</div>
  	</div>
</div> <!-- /container -->





<?php

//If URL is entered begen process
if (isset($_POST['url'])) {
	$url = $_POST['url'];
	$obj = new Download($url);
	$obj->downloadFile();
}


class Download {
	const MAX_URL_LENGTH = 1050;
	protected $url;
	protected $videoLocation;

  	public function __construct($url) {
    	$this->url = $url;
  	}

	//Check URL Validity
	protected function checkURL(){
		if(isset($this->url)){
			if(filter_var($this->url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)){
				return true;
			}
			else{
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>not a valid URL</div>';
			}
		}
	}

	//Get video extension
	protected function getExtention(){
		$end = end(preg_split("/[.]+/", $this->url));
		if(isset($end)){
			return $end;
		}
	}

	//Updates video download progress bar
	protected function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
	{
		if($download_size > 0) {
			$percentageVal = round( ($downloaded / $download_size  * 100), 2);
			echo "<script>$('#theprogressbar').attr('aria-valuenow', $percentageVal).css('width',$percentageVal+'%');</script>";
			echo "<script>$('#theprogressbar').text($percentageVal + '% Complete');</script>";
		}
	}

	//Download Video File
	public function downloadFile(){
		ini_set('memory_limit', "512M");
		$extension = $this->getExtention();
			if ($extension) {
				//echo $url;
				$this->videoLocation = "downloads/video.$extension";// Set video download destination
				$file = fopen($this->videoLocation, "w+");
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->url);
				curl_setopt( $ch, CURLOPT_FILE, $file );
				curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
				curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$return = curl_exec($ch);
				curl_close($ch);
				fputs($file, $return);
				fclose($file);
				$this->createThumbnail();
			}
	}

	//Create Thumbnail
	protected function createThumbnail(){
		// where ffmpeg is located  
		$ffmpeg = '/usr/local/Cellar/ffmpeg/3.3.2/bin/ffmpeg';  
		//video dir  
		$video = $this->videoLocation;  
		//where to save the image  
		$image = 'thumbnails/image.jpg';  
		//time to take screenshot at  
		$interval = 5;
		//screenshot size  
		//ffmpeg command  
		$cmd = "$ffmpeg -i $video -ss $interval -f mjpeg -t 1 -r 1 -y $image 2>&1";

		exec($cmd);
		echo "<script>$('#myThumnail').attr('src', 'thumbnails/image.jpg?=<?php echo filemtime($filename)?>');</script>";
	}
}
?>

</body>
</html>