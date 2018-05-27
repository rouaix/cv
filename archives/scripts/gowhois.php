<?php

/**
    Program to perform ip whois
    Silver Moon
    m00n.silv3r@gmail.com
*/
$host = 'rouaix.com';
$dns = '8.8.8.8';  // Google Public DNS

$ip = `nslookup $host $dns`; // the backticks execute the command in the shell

$ips = array();
if(preg_match_all('/Address: ((?:\d{1,3}\.){3}\d{1,3})/', $ip, $match) > 0){
    $ips = $match[1];
}


$ip = $ips;
 
$whois = get_whois($ip);
 
echo "<pre>$whois</pre>";
 
/**
    Get the whois content of an ip by selecting the correct server
*/
function get_whois($ip) 
{
    $w = get_whois_from_server('whois.iana.org' , $ip);
     
    preg_match('@whois.[w.]*@si' , $w , $data);
 
    $whois_server = $data[0];
     
    //echo $whois_server;
 
    //now get actual whois data
    $whois_data = get_whois_from_server($whois_server , $ip);
     
    return $whois_data;
}
 
/**
    Get the whois result from a whois server
    return text
*/
function get_whois_from_server($server , $ip) 
{
    $data = '';
     
    #Before connecting lets check whether server alive or not
     
    #Create the socket and connect
    $f = fsockopen($server, 43, $errno, $errstr, 3);    //Open a new connection
    if(!$f)
    {
        return '';
    }
     
    #Set the timeout limit for read
    if(!stream_set_timeout($f , 3))
    {
        die('Unable to set set_timeout');   #Did this solve the problem ?
    }
     
    #Send the IP to the whois server    
    if($f)
    {
        fputs($f, "$iprn");
    }
     
    /*
        Theory : stream_set_timeout must be set after a write and before a read for it to take effect on the read operation
        If it is set before the write then it will have no effect : http://in.php.net/stream_set_timeout
    */
     
    //Set the timeout limit for read
    if(!stream_set_timeout($f , 3))
    {
        die('Unable to stream_set_timeout');    #Did this solve the problem ?
    }
     
    //Set socket in non-blocking mode
    stream_set_blocking ($f, 0 );
     
    //If connection still valid
    if($f) 
    {
        while (!feof($f)) 
        {
            $data .= fread($f , 128);
        }
    }
     
    //Now return the data
    return $data;
} 
?>