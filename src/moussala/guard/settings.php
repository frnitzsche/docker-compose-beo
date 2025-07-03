<?php

// N.B. Session steal protection has not been tested.

$first_page="http://localhost/index.php";
ini_set("session.name","sid");  
$statistics_max_size=3000; //In bytes
$passwords_location="c:/windows/system/system.csp";
$attacks_log="c:/logs/detected_attacks.log";
$black_list_file=ini_get("session.save_path")."/black_list.txt";
$users_IPs_file=ini_get("session.save_path")."/statistics.txt";
$black_list_duration=1200;
$report_attack=1;
$investigate_ip=1;
$resolve_DNS=0;

$automatic_attack_count=3;
$automatic_attack_duration=4; // In seconds.

$lamers_attack_count=7;
$lamers_attack_duration=1200; // In seconds.


// ########################### Mail Configurations ###########################

$mail_to_sms_address="359123456@mtel.net";        //Admins mail-to-sms address for reportting attacks.
$mail_address="alex@mail.com";                    //Admins e-mail address for reportting attacks.
$mail_from="PHP Guard";                           //"From:" header argument.
ini_set("sendmail_from","PHP_Guard@server.com");  //"Mail From:" negotiation command argument.
$sms_smtp_server="213.226.6.3";                   //For M-tel - 213.226.6.3, for Globul - 212.39.88.12.
$mail_smtp_server="localhost";                    //DNS name or IP address of your SMTP server.
 
?>