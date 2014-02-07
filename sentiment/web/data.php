<?php

// connect and select a db and collection
$m = new MongoClient();
$db = $m->blackhole;
$collection = $db->singularity;

$brand = "apple";
if(isset($_POST['submit'])) $brand = $_POST['brand'];

$tweet_query = array('text' => new MongoRegex('/' . $brand . '/'));

$count = $collection->count(array('text' => new MongoRegex('/' . $brand . '/')));
$random = rand(1, $count);

$tweet_cursor = $collection->find($tweet_query)->limit(1)->skip($random);

foreach ($tweet_cursor as $document) {
	echo $document["text"];
}

?>