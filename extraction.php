<?php
# HTTP URL is constructed accordingly with JSON query results format in mind.

#HTTP request with result in rdf (turtle)
function sparqlQuery($query, $baseURL, $format="application/rdf")

  {
	$params=array(
		"default-graph" =>  "",
		"should-sponge" =>  "soft",
		"query" =>  $query,
		"debug" =>  "on",
		"timeout" =>  "",
		"format" =>  $format,
		"save" =>  "display",
		"fname" =>  ""
	);

	$querypart="?";	
	foreach($params as $name => $value) 
  {
		$querypart=$querypart . $name . '=' . urlencode($value) . "&";
	}
	
	$sparqlURL=$baseURL . $querypart;
	
	return file_get_contents($sparqlURL);
};

function saveFile($file, $output){

$fp = fopen($file, 'a');
fwrite($fp, $output);
fclose($fp);
};

function readQuery($fileQuery){
$fp = fopen($fileQuery, 'r');
$query = fread($fp,filesize($fileQuery));
return $query;
};


# DBPedia sparql endpoint
#Virtuoso pragmas for instructing SPARQL engine to perform an HTTP GET
#using the IRI in FROM clause as Data Source URL
$dsn="http://dbpedia.org/resource/DBpedia";


# chunk is used for perform a movement  in the final result
$chunk = 0;
# stop is used for terminate the script
$stop = false;

//to modify according to the output's format
$nullPoint = "# Empty TURTLE
";

# read query in a generic file (query.txt)
$toExec = readQuery("query.txt");
while(!$stop){
	# max limit of dbpedia is 40000 but i used 10000 
	$value = 10000 * $chunk;
	$chunk++;
$query = $toExec.$value;
# to debug
echo $query;
$data=sparqlQuery($query, "http://dbpedia.org/sparql");
echo $data;

# if there isn't result then stop and finish 
if((strcmp($data,$nullPoint))==0){ 
	$stop = true; 
	break;
	}

	$file = "outputFile".$chunk.".ttl";
saveFile($file, $data);

}
print "Finish";

?>
