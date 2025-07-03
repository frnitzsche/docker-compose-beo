<?php

############################# General settings ################################
$root_location="d:/files/";
// ini_set("session.name","sid");
$statistics_max_size=3000;          // In bytes
$passwords_location="/etc/system.csp";
$attacks_log="/logs/detected_attacks.log";
$black_list_file=ini_get("session.save_path")."/black_list.txt";
$users_IPs_file=ini_get("session.save_path")."/statistics.txt";
$black_list_duration=1200;
$report_attack=1;
$investigate_ip=1;
$resolve_DNS=0;

$automatic_attack_count=3;
$automatic_attack_duration=4;     // In seconds.

$lamers_attack_count=7;
$lamers_attack_duration=1200;     // In seconds.


########################### main.php configurations #############################
$start_year_data=2002;
$NO_previous_days=2;
$NO2_previous_days=2;
$O3_previous_days=2;
$gamma_previous_days=5;
$neutrons_previous_days=5;



########################### graphics.php configurations ###########################
$previous_days_radiation=5;           // for neutron and gamma visualization
$previous_days_gasses=3;             // for gasses
$space_between_bar=1;                // in pixels
$minimal_bar_with=2;                   // in pixels
$dot_radius=1;
$dot_radius_combined=1;



#############################  Mail Configurations  ##############################
$mail_to_sms_address="359123456@mtel.net";        //  Admins mail-to-sms address for reportting attacks.
$mail_address="alex@inrne.bas.bg";                       //  Admins e-mail address for reportting attacks.
$mail_from="PHP Guard";                                     //  "From:" header argument.
ini_set("sendmail_from","PHP_Guard@server.com");   //   "Mail From:" negotiation command argument.
$sms_smtp_server="213.226.6.3";                        //   For M-tel - 213.226.6.3, for Globul - 212.39.88.12.
$mail_smtp_server="localhost";                            //   DNS name or IP address of your SMTP server.

?>