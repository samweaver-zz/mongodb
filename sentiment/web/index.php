<?php

function formatDateString($dt) {
    $dt = date('H:i', $dt->sec); 
    $dt = '"' . $dt . '"';
    return $dt;
}

// connect and select a db and collection
$m = new MongoClient();
$db = $m->blackhole;
$collection = $db->sentiment;

// find all minutes
// default to apple unless something has been typed in
$brand = "apple";
if(isset($_POST['submit'])) $brand = $_POST['brand'];

$min_query = array('type' => 'minute', 'brand' => $brand);
$hour_query = array('type' => 'hour', 'brand' => $brand);
$day_query = array('type' => 'day', 'brand' => $brand);

$min_cursor = $collection->find($min_query);
$hour_cursor = $collection->find($hour_query);
$day_cursor = $collection->find($day_query);

$dt_min_data = "";
$pos_min_data = "";
$neg_min_data = "";

$dt_hour_data = "";
$pos_hour_data = "";
$neg_hour_data = "";

$dt_day_data = "";
$pos_day_data = "";
$neg_day_data = "";

foreach ($min_cursor as $document) {
	$data = $document["data"];
	foreach($data as $item) {
		$dt_min_data = $dt_min_data . "," . $item["minute"];
		$pos_min_data = $pos_min_data . "," . $item["pos_score"];
		$neg_min_data = $neg_min_data . "," . $item["neg_score"];
	}
	$dt_min_data = substr($dt_min_data, 1);
	$pos_min_data = substr($pos_min_data, 1);
	$neg_min_data = substr($neg_min_data, 1);	
}

foreach ($hour_cursor as $document) {
	$data = $document["data"];
	foreach($data as $item) {
		$dt_hour_data = $dt_hour_data . "," . $item["hour"];
		$pos_hour_data = $pos_hour_data . "," . $item["pos_score"];
		$neg_hour_data = $neg_hour_data . "," . $item["neg_score"];
	}
	$dt_hour_data = substr($dt_hour_data, 1);
	$pos_hour_data = substr($pos_hour_data, 1);
	$neg_hour_data = substr($neg_hour_data, 1);	
}

foreach ($day_cursor as $document) {
	$data = $document["data"];
	foreach($data as $item) {
		$dt_day_data = $dt_day_data . "," . $item["day"];
		$pos_day_data = $pos_day_data . "," . $item["pos_score"];
		$neg_day_data = $neg_day_data . "," . $item["neg_score"];
	}
	$dt_day_data = substr($dt_day_data, 1);
	$pos_day_data = substr($pos_day_data, 1);
	$neg_day_data = substr($neg_day_data, 1);
}

?>

<!DOCTYPE html>
<html>
  <head>
  	<link rel="stylesheet" type="text/css" href="style.css">
  	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>  	
    <script src="Chart.js"></script>
    <script>
	    //$(document).ready(function(){
	    	$('#submit').click(function(){
	    		$('#loading').show();
			    setTimeout(function() {
			      $('#loading').fadeOut('fast');
			    }, 1200);
			})
		//});

		function redraw(time) {

			switch(time) {
				case "minute":
					label_data = [<?php echo $dt_min_data ?>]
					pos_data =   [<?php echo $pos_min_data ?>]
					neg_data =   [<?php echo $neg_min_data ?>]
					break;
				case "hour":
					label_data = [<?php echo $dt_hour_data ?>]
					pos_data =   [<?php echo $pos_hour_data ?>]
					neg_data =   [<?php echo $neg_hour_data ?>]				
					break;
				case "day":
					label_data = [<?php echo $dt_day_data ?>]
					pos_data = [<?php echo $pos_day_data ?>]
					neg_data = [<?php echo $neg_day_data ?>]				
					break;
			}

	  		var data = {
				labels : label_data,
				datasets : [
					{
						fillColor : "rgba(124,252,0,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data : pos_data
					},
					{
						fillColor : "rgba(255,0,0,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data : neg_data
					}
				]
			}

			var options = { 
				pointDotRadius : 2,
				datasetFill : true
			};

	  		//Get the context of the canvas element we want to select
			var ctx = document.getElementById("chart").getContext("2d");
			var myNewChart = new Chart(ctx).Line(data, options);			
		}

		$(document).ready(function() {
			$("#text").load('data.php');
		});

		setInterval(appendTweet, 300);

		function appendTweet() {

			$.ajax({
  				url: "data.php",
  				context: document.body
			}).done(function(data) {

				var textAlign = Math.floor(Math.random() * 3 + 1);
				var textStyle = Math.floor(Math.random() * 2 + 1);


				var align;
				var style;

				switch (textAlign) {
					case 1:
						align = "center";
						break;
					case 2:
						align = "left";
						break;
					case 3:
						align = "right";
						break;

				}

				var text = "<div style=text-align:'" + align + "'>" + data + "</div><br>";

				switch (textStyle) {
					case 1:
						text = "<i>" + text + "</i>";
						break;
					case 2:
						style = "normal";
						break;
				}


				

				$('#text').prepend(text);
			});
		}

    </script>
  </head>
  
  <body>
  	<div id="container">
  		<div id="content">
	  	
	  	<!-- Search box -->
		<form class="form-wrapper cf" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="text" name="brand" placeholder="Search here..." required>
			<button type="submit" name="submit" id="submit">Search</button>
		</form>

		<!-- loading gear -->
		<div id="loading"><img class="centered" src="images/preloader_transparent.gif"/></div>

		<h1><?php echo "Sentiment Analysis for " . ucfirst($brand) ?></h1>

		<?php if(isset($_POST['submit'])) ?>
  	  	<!-- Chart -->
	  	<canvas id="chart" width="1200" height="300" style="margin-left:75px"></canvas>
	  	
	  	<!-- Time selector -->
		<div id="buttonContainer">
			<a href="#" onclick="redraw('day')" class="buttons">Day</a>
			<a href="#" onclick="redraw('hour')" class="buttons">Hour</a>
			<a href="#" onclick="redraw('minute')" class="buttons">Minute</a>
		</div>
	  	
	  	<script>

	  		var data = {
				labels : [<?php echo $dt_min_data; ?>],
				datasets : [
					{
						fillColor : "rgba(124,252,0,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data : [<?php echo $pos_min_data; ?>]
					},
					{
						fillColor : "rgba(255,0,0,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data : [<?php echo $neg_min_data; ?>]
					}
				]
			}

	  		//Get the context of the canvas element we want to select
			var ctx = document.getElementById("chart").getContext("2d");
			var myNewChart = new Chart(ctx).Line(data);
		
	  	</script>

	  	<!-- <div id="startTime"><div id="hour">7</div><div id="minute">:42:22</div><div id="ampm">pm</div><div id="start">start</div></div> -->
	  	<!-- <div id="endTime"></div> -->

	  	<div id="text">
	  		<?php 
	  			
	  			$random = rand(1,3);
	  			$textAlign = "";

	  			switch ($random) {
	  				case 1:
	  					$textAlign = "left";
	  					break;
	  				case 2:
	  					$textAlign = "center";
	  					break;
	  				case 3:
	  					$textAlign = "right";
	  					break;
	  			} 
	  		?>
	  		<div style="text-align: <?php echo $textAlign ?>">.some text here.</div>
	  	</div>

	  	</div>
	</div>
  <div id="footer">&copy; 2014 MongoDB Inc - Content posted by Twitter Users</div>
  <a href="6.html">Next</a>
  </body>
</html>