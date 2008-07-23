#!/usr/bin/perl
 
use strict;
 
## Configure ONLY this 2 variables
my $editdns_pass   = ""; # put your password
my $editdns_record = ""; # put the record you wish to update
 
## ###############
## Nothing else should be changed unless you know what to do
## ###############
 
my $host = "DynDNS.EditDNS.net";
my $port = 80;
my $editdns_post = "p=$editdns_pass&r=$editdns_record";
 
my $editdns_req = join("",
  "POST /api/dynLinux.php HTTP/1.0\r\n",
  "Host: $host:$port\r\n",
  "User-Agent: EditDNS Browser 0.1\r\n",
  "Referer: http://www.editdns.net\r\n",
  "Content-Type: application/x-www-form-urlencoded\r\n",
  "Content-Length: ".length($editdns_post)."\r\n\r\n",
  "$editdns_post\n"
);
 
my $hostaddr = (gethostbyname($host))[4] || &error("Couldn't get IP for $host");
my $remotehost= pack('S n a4 x8',2,$port,$hostaddr);
socket(S,2,1,6) || &error("Couldn't create socket");
connect(S,$remotehost) || &error("Couldn't connect to $host:$port");
select((select(S),$|=1)[0]);
print S $editdns_req;
vec(my $rin='',fileno(S),1)= 1 ;
select($rin,undef,undef,60) || &error("No response from $host:$port");
undef($/);
close(S);
print "[DONE]\n";
exit;
 
sub error {
        print "[ERROR] $_[0]\n";
        exit;
}