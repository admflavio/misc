<?php

// binary netmask to wildcard mask
function binnmtowm($binin){
	$binin=rtrim($binin, "0");
	if (!ereg("0",$binin) ){
		return str_pad(str_replace("1","0",$binin), 32, "1");
	} else return "1010101010101010101010101010101010101010";
}

// binary to CIDR
function bintocdr ($binin){
	return strlen(rtrim($binin,"0"));
}

// binary to dotted-quad
function bintodq ($binin) {
	if ($binin=="N/A") return $binin;
	$binin=explode(".", chunk_split($binin,8,"."));
	for ($i=0; $i<4 ; $i++) {
		$dq[$i]=bindec($binin[$i]);
	}
        return implode(".",$dq) ;
}

// binary to integer
function bintoint ($binin){
        return bindec($binin);
}

// binary wildcard mask to netmask
function binwmtonm($binin){
	$binin=rtrim($binin, "1");
	if (!ereg("1",$binin)){
		return str_pad(str_replace("0","1",$binin), 32, "0");
	} else return "1010101010101010101010101010101010101010";
}

// CIDR to binary
function cdrtobin ($cdrin){
	return str_pad(str_pad("", $cdrin, "1"), 32, "0");
}

// binary to dotted binary
function dotbin($binin,$cdr_nmask){
	// splits 32 bit bin into dotted bin octets
	if ($binin=="N/A") return $binin;
	$oct=rtrim(chunk_split($binin,8,"."),".");
	if ($cdr_nmask > 0){
		$offset=sprintf("%u",$cdr_nmask/8) + $cdr_nmask ;
		return substr($oct,0,$offset ) . "&nbsp;&nbsp;&nbsp;" . substr($oct,$offset) ;
	} else {
	return $oct;
	}
}

// dotted-quad to binary
function dqtobin($dqin) {
        $dq = explode(".",$dqin);
        for ($i=0; $i<4 ; $i++) {
           $bin[$i]=str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
        }
        return implode("",$bin);
}

// integer to binary
function inttobin ($intin) {
        return str_pad(decbin($intin), 32, "0", STR_PAD_LEFT);
}

// calculate broadcast address
function broadcast($host, $mask)
{
	$bin_host = dqtobin($host);
	$cdr_mask = bintocdr(dqtobin($mask));
	
	return bintodq(str_pad(substr($bin_host,0,$cdr_mask),32,1));
}

function num_hosts($mask)
{
	$cdr_mask = bintocdr(dqtobin($mask));

	return bindec(str_pad("",(32-$cdr_mask),1)) - 1;
}

echo broadcast("192.168.1.15", "255.255.255.0") ."\n";
echo num_hosts("255.255.240.0") ."\n";
