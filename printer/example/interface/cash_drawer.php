<?php
require __DIR__ . '/../../autoload.php';
use Mike42\Escpos\Printer;
//use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
//$connector = new FilePrintConnector(CUSTOMER_COPY);
$connector = new WindowsPrintConnector(CUSTOMER_COPY);
$printer = new Printer($connector);

// Regular pulse
$printer->text("Regular pulse...\n");
$printer->pulse();
sleep(1);
$printer->pulse(1);
sleep(1);
$printer->pulse(0, 100, 100);
sleep(1);
$printer->pulse(0, 300, 300);
sleep(1);
$printer->pulse(1, 100, 100);
sleep(1);
$printer->pulse(1, 300, 300);
sleep(1);

// See what 'real-time pulse' does??
$printer->text("Real-time pulse!\n");
foreach(range(0, 1) as $m) {
    foreach(range(1,8) as $t) {
        $n = 1;
        $printer->text("\$m: $m, \$t: $t\n");
        $connector->write(Printer::DLE . "\x14" . chr($n) . chr($m) . chr($t));
        sleep(1);
    }
}

$printer->text("Done\n");
$printer->close();
?>