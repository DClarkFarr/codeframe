<?php 

function extract_domain($domain){
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)){
        $domain = $matches['domain'];
    }
    $arr = explode('.', $domain);
    $last = array_pop($arr);
    if(strpos($last, ':') !== false){
    	return $last;
    }
    return $domain;
}

function extract_subdomains($domain){
    $subdomains = $domain;
    $domain = extract_domain($subdomains);

    $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

    return $subdomains;
}