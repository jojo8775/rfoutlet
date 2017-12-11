<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Edit these codes for each outlet
$codes = array(
    "1" => array(
        "on" => 5576131, 
        "off" => 5576140
    ),
    "2" => array(
        "on" => 5575987,
        "off" => 5575996
    ),
    "3" => array(
        "on" => 5576451,
        "off" => 5576460
    ),
    "4" => array(
        "on" => 5526613,
        "off" => 5526612
    ),
    "5" => array(
        "on" => 4539733,
        "off" => 4539732 
    ),
    "6" => array(
        "on" => 11111,
        "off" => 22222
    ),
);

// Path to the codesend binary (current directory is the default)
$codeSendPath = './codesend';

// This PIN is not the first PIN on the Raspberry Pi GPIO header!
// Consult https://projects.drogon.net/raspberry-pi/wiringpi/pins/
// for more information.
$codeSendPIN = "0";

// Pulse length depends on the RF outlets you are using. Use RFSniffer to see what pulse length your device uses.
$codeSendPulseLength = "193";

if (!file_exists($codeSendPath)) {
    error_log("$codeSendPath is missing, please edit the script", 0);
    die(json_encode(array('success' => false)));
}

$outletLight = $_POST['outletId'];
$outletStatus = $_POST['outletStatus'];

if ($outletLight == "7") {
    // 7 is all 5 outlets combined
    if (function_exists('array_column')) {
        // PHP >= 5.5
        $codesToToggle = array_column($codes, $outletStatus);
    } else {
        $codesToToggle = array();
        foreach ($codes as $outletCodes) {
            array_push($codesToToggle, $outletCodes[$outletStatus]);
        }
    }
} else {
    // One
    $codesToToggle = array($codes[$outletLight][$outletStatus]);
}

foreach ($codesToToggle as $codeSendCode) {
    shell_exec($codeSendPath . ' ' . $codeSendCode . ' -p ' . $codeSendPIN . ' -l ' . $codeSendPulseLength);
    sleep(1);
}

die(json_encode(array('success' => true)));
?>
