<?php

// Modify this array to include all the domains you wish to update
// array keys are the domain name, array values are the password you setup for dynamic updates
$domains = array(
                'domain1.com'  => 'yourpassword',
                'domain2.com'  => 'yourpassword',
                'domain3.com' => 'yourpassword');

// DO NOT EDIT BELOW THIS LINE

$host = "dyndns.editdns.net";
$port = 80;

// setup syslogging
define_syslog_variables();
openlog("EditDNS", LOG_PID, LOG_LOCAL0);

// get the IP addres of the EditDNS server
$hostip = gethostbyname($host);

syslog(LOG_INFO,"Started EditDNS.net update...");

// loop through the domains, sending an update request for each one to the EditDNS server
foreach ($domains as $domain => $password)
{
    // define post data
    $post = "p=$password&r=$domain";
    $postlen = strlen($post);

    $buffer = "POST /api/dynLinux.php HTTP/1.0\r\n";
    $buffer .= "Host: $host:$port\r\n";
    $buffer .= "User-Agent: PHP EditDNS Updater 0.1\r\n";
    $buffer .= "Referer: http://www.editdns.net\r\n";
    $buffer .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $buffer .= "Content-Length: $postlen\r\n\r\n";
    $buffer .= "$post\n";

    // create network socket
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // connect the socket
    if (socket_connect($socket, $hostip, $port))
    {
        socket_write($socket, $buffer, strlen($buffer));
        socket_recv($socket, $recvbuffer, 1024, 0);
        syslog(LOG_INFO, "Updated $domain. Returned: $recvbuffer");
        socket_close($socket);
    }
}

syslog(LOG_INFO,"Completed EditDNS.net update.");

closelog();

?>