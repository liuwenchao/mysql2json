<?php

/**
 * a script to dump sql result as json.
 * same arguments as mysql.
 * Example: php mysql2json.php -hlocalhost -Dtest -uroot -proot --execute="select * from feeds" 
 * @author Louis<louis.wenchao.liu@gmail.com>
 * @licence MIT
 * 
 */


$args = array(
  array("short"=>"e", "required"=>true,  "long"=>"execute",  "variable"=>"sql"),
  array("short"=>"h", "required"=>false, "long"=>"host",     "variable"=>"host", 		 "default"=>"localhost"),
  array("short"=>"D", "required"=>false, "long"=>"database", "variable"=>"database", "default"=>"test"),
  array("short"=>"u", "required"=>false, "long"=>"user", 		 "variable"=>"username", "default"=>"root"),
  array("short"=>"p", "required"=>false, "long"=>"password", "variable"=>"password"),
  //TODO add more arguments.
);

function getShortOptString($option) {
  return $option["short"] . ($option["required"] ? ":" : "::");
}


function getLongOptString($option) {
  return $option["long"] . ($option["required"] ? ":" : "::");
}

$options = getopt(implode(array_map("getShortOptString", $args)), array_map("getLongOptString", $args));

foreach($args as $arg) {
	$$arg["variable"] = array_key_exists($arg["short"], $options)
	// feach from short option first
	? $options[$arg["short"]] 
	: (array_key_exists($arg["long"], $options)
	  // then try long options 
		? $options[$arg["long"]] 
		: (array_key_exists("default", $arg)
			// then try default value.
			? $arg["default"]
		  : ($arg["required"]
		  	// error if required but null.
		    ? die("\r\nvalue of ".$arg["long"]." is required and not found. \r\n") 
		    : null)));
}

die("\r\nconnecting to: $username@$host:$database with sql: $sql\r\n");

$encodable = array();

@mysql_connect($host, $username, $password);
mysql_select_db($database) or die("Unable to select database: ".$database);
$result = mysql_query($sql);
while($obj = mysql_fetch_object($result)) {
  $encodable[] = $obj;
}

echo json_encode($encodable);

mysql_close();
?>
