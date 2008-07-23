#!/usr/bin/perl
 
undef %domains;

# the key (inside the curly braces) of the hash below is the domain name
# the value (after the =) of the hash is the password for dynamic updates
$domains{"domain1.com"} = "password1";
$domains{"domain2.com"} = "password2";
$domains{"domain3.com"} = "password3";

# DO NOT EDIT BELOW THIS LINE

use Sys::Syslog qw( :DEFAULT setlogsock);

setlogsock('unix');
openlog('EditDNS','pid','local0');

my $host = "dyndns.editdns.net";
my $port = 80;

syslog('info', "Started EditDNS.net update...");

foreach $domain (keys %domains) {
    my $recvbuffer = "";
    my $post = "p=" .$domains{$domain} ."&r=$domain";
    my $buffer = join("",
        "POST /api/dynLinux.php HTTP/1.0\r\n",
        "Host: $host:$port\r\n",
        "User-Agent: Perl EditDNS Updater 0.2\r\n",
        "Referer: http://www.editdns.net\r\n",
        "Content-Type: application/x-www-form-urlencoded\r\n",
        "Content-Length: " .length($post) ."\r\n\r\n",
        "$post\n");

    my $hostip = (gethostbyname($host))[4] || &error("Couldn't get IP for $host");
    my $remotehost = pack('S n a4 x8', 2, $port, $hostip);

    socket(S, 2, 1, 6) || &error("Couldn't create socket");
    connect(S, $remotehost) || &error("Couldn't connect to $host:$port");
    select((select(S), $| = 1)[0]);
    print S $buffer;
    vec(my $rin='', fileno(S), 1) = 1 ;
    select($rin, undef, undef, 60) || &error("No response from $host:$port");

    read(S, $recvbuffer, 1024);
    
    syslog('info', "Updated $domain. Returned: $recvbuffer");

    undef($/);
    close(S);
}

syslog('info', "Completed EditDNS.net update.");

closelog;
exit;
 
sub error {
        print "[ERROR] $_[0]\n";
        exit;
}