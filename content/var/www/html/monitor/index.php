<?php
require 'flight/Flight.php';


# This function will perform the actual request to the rest api
function _process_check_results($requestURI="",$data=""){
        $headers = array();
        $headers[] = 'X-HTTP-Method-Override: POST';
        $headers[] = 'Accept: application/json';

        $options = array(
                CURLOPT_RETURNTRANSFER  => true,    
		CURLOPT_HEADER          => 1,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_ENCODING        => "",       
                CURLOPT_USERAGENT       => "rest", 
                CURLOPT_AUTOREFERER     => true,   
                CURLOPT_CONNECTTIMEOUT  => 120,   
                CURLOPT_TIMEOUT         => 120,
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_USERPWD         => 'root:9edaeb464aa3ad2f',
                CURLOPT_POST           => true,
        );

        $url ="https://NOSR5LFTP-01:5665/v1/actions/process-check-result?".$requestURI;
        #print $url;
        $post = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt_array( $ch, $options );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch); 
}

function index(){
    echo 'This is a rest interface, you should not see this normally';
}

function host($name="",$status="",$output=""){

	$data = array(
  		"exit_status" => $status,
		"plugin_output" => $output
	);

	$checkURI = sprintf("host=%s",$host);
 	_process_check_results($checkURI, $data);

}


function service($host="",$name="",$status="",$output=""){

        $data = array(
                "exit_status" => $status,
                "plugin_output" => $output
        );
	
        $checkURI = sprintf("service=%s!%s",$host,$name);
	_process_check_results($checkURI, $data);

}

function register_host($host=""){
        if(_check_if_exsist($host) == 1){
		print "Host exsist";
	}else{
		print "Host NOT exsist";
		$returnData=_put_web_page("https://NOSR5LFTP-01:5665/v1/objects/hosts/".$host);
		print_r($returnData);
	}
}

function _check_if_exsist($host=""){
	# curl -k -s -u 'root:9edaeb464aa3ad2f' -H 'X-HTTP-Method-Override: GET' -X POST 'https://localhost:5665/v1/objects/hosts'
	$exsist=0;
	
	$returnData=_get_web_page("https://NOSR5LFTP-01:5665/v1/objects/hosts");
	
	# Our Private function returns everything but we only user content here	
	$content=$returnData['content'];
	
	# Data is the result of a rest query returning json
	$contentArr=json_decode($content, true);

	# We are only interestd in the results part here
	$resultsArr=$contentArr['results'];	

	# walk over the attrs of the data
	foreach($resultsArr as $attr){
		$attrArr=$attr['attrs'];
		if (strcmp($attrArr['__name'],$host) == 0 ){
			#print $attrArr['__name']."<br>";
			$exsist=1;
		}
        }
	return $exsist;

}

function _put_web_page( $url ){
	$headers = array();
	$headers[] = 'X-HTTP-Method-Override: PUT';
	$headers[] = 'Accept: application/json';
        
	$options = array(
                CURLOPT_RETURNTRANSFER  => true,     // return web page
                CURLOPT_HEADER          => $headers,
                CURLOPT_ENCODING        => "",       // handle all encodings
                CURLOPT_USERAGENT       => "spider", // who am i
                CURLOPT_AUTOREFERER     => true,     // set referer on redirect
                CURLOPT_CONNECTTIMEOUT  => 120,      // timeout on connect
                CURLOPT_TIMEOUT         => 120,      // timeout on response
                CURLOPT_MAXREDIRS       => 10,       // stop after 10 redirects
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_USERPWD         => 'root:9edaeb464aa3ad2f',
                CURLOPT_POST           => true,
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
}


function _get_web_page( $url ){
	$options = array(
        	CURLOPT_RETURNTRANSFER 	=> true,     // return web page
        	CURLOPT_HEADER         	=> 'X-HTTP-Method-Override: GET',
        	CURLOPT_ENCODING       	=> "",       // handle all encodings
        	CURLOPT_USERAGENT      	=> "spider", // who am i
        	CURLOPT_AUTOREFERER    	=> true,     // set referer on redirect
        	CURLOPT_CONNECTTIMEOUT 	=> 120,      // timeout on connect
        	CURLOPT_TIMEOUT        	=> 120,      // timeout on response
        	CURLOPT_MAXREDIRS      	=> 10,       // stop after 10 redirects
		CURLOPT_SSL_VERIFYPEER 	=> false,
		CURLOPT_USERPWD		=> 'root:9edaeb464aa3ad2f',
		#CURLOPT_POST 		=> true,
    	);

    	$ch      = curl_init( $url );
    	curl_setopt_array( $ch, $options );
    	$content = curl_exec( $ch );
    	$err     = curl_errno( $ch );
    	$errmsg  = curl_error( $ch );
    	$header  = curl_getinfo( $ch );
    	curl_close( $ch );

    	$header['errno']   = $err;
    	$header['errmsg']  = $errmsg;
    	$header['content'] = $content;
    	return $header;
}




Flight::route('/service/@host/@service/@status/@data', 'service');
Flight::route('/host/@host/@status/@data', 'host');
Flight::route('/', 'index');
Flight::route('/register/@host', 'register_host');


Flight::start();

