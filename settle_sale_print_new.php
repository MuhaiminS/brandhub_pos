<?php
/* Change to the correct path if you copy this example! */

require __DIR__ . '/printer/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
/**
 * Install the printer using USB printing support, and the "Generic / Text Only" driver,
 * then share it (you can use a firewall so that it can only be seen locally).
 *
 * Use a WindowsPrintConnector with the share name to print.
 *
 * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
 * "Receipt Printer), the following commands work:
 *
 *  echo "Hello World" > testfile
 *  copy testfile "\\%COMPUTERNAME%\Receipt Printer"
 *  del testfile
 */
//echo '<pre>'; print_r($_GET); die;
session_start();
function character_limiter($str, $n = 500, $end_char = '&#8230;')
    {
        if (strlen($str) < $n)
        {
            return $str;
        }

        $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

        if (strlen($str) <= $n)
        {
            return $str;
        }

        $out = "";
        foreach (explode(' ', trim($str)) as $val)
        {
            $out .= $val.' ';

            if (strlen($out) >= $n)
            {
                $out = trim($out);
                return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
            }
        }
    }
   require_once 'db_functions.php';
   $function = New DB_Functions(); 

   $inputs['shop_id'] = $_SESSION['shop_id'];
   $inputs['user_id'] = $_SESSION['user_id'];
   $inputs['discount_type'] = 'amount';
   $inputs['to_date'] = date("Y-m-d H:i:s");
   $settle_sale = $function->getAllSettle($inputs);
   // echo '<pre>'; print_r($settle_sale); die;
   $set = (isset($_GET['set']) && $_GET['set'] !='') ? $_GET['set'] : '';
   $print = (isset($_GET['print']) && $_GET['print'] !='') ? $_GET['print'] : '';
    /*if($set == 'yes'){
       include('excel_export_settle_sale.php');
    }*/
   $last_settle_sale = $function->getSettleSale($inputs);
  /* $inputs1['from_date'] = $last_settle_sale[0]['settle_date'];
   $inputs1['to_date'] = date('Y-m-d 23:59:59');
   $inputs1['shop_id'] = $_SESSION['shop_id'];
   $inputs1['user_id'] = $_SESSION['user_id'];
   $inputs1['order_type'] = 'delivery';
   $inputs1['payment_type'] = '';
   $inputs1['payment_status'] = 'unpaid';
   $inputs1['status'] = 'pending';
   $sale_orders = $function->getSaleOrderItemDetailsList($inputs1);
   $redirect = "settle_sale.php";*/

   // $inputs1['from_date'] = date('Y-m-d 00:00:00');
   $inputs1['from_date'] = $last_settle_sale[0]['settle_date'];
   $inputs1['to_date'] = date('Y-m-d 23:59:59');
   $inputs1['shop_id'] = $_SESSION['shop_id'];
   $inputs1['user_id'] = $_SESSION['user_id'];
   $inputs1['order_type'] = 'delivery';
   $inputs1['payment_type'] = '';
   $inputs1['payment_status'] = 'unpaid';
   $inputs1['status'] = 'pending,hold';
//   $inputs1['status'] = 'hold';
   $sale_orders = $function->getSaleOrderItemDetailsList($inputs1);
    $redirect = "settle_sale.php";

// die;
 if((count($sale_orders) > 0) && ($set == 'yes')) { ///echo '123'; die;
    echo "<script>alert('COD items still pending. So cant able to settle');</script>";
    //$function->redirect($redirect);
    if(($settle_sale['hold_pending'] == 1) && ($set == 'yes')) {

    }else{
      $function->redirect($redirect);
      exit;
    }
}

if(($settle_sale['hold_pending'] == 1) && ($set == 'yes')) {
    echo "<script>alert('Hold items still pending. So cant able to settle');</script>";
    $function->redirect($redirect);
    exit;
 }

// echo "<pre>"; print_r($settle_sale['sale_summary_details']); die;

/*if((count($sale_orders) > 0) && ($set == 'yes')) { 
    echo "<script>alert('Cod items still pending. So cant able to settle');</script>"; 
    mysqli_query($GLOBALS['conn'],"DELETE FROM bank_deposit WHERE 1 ORDER BY id DESC LIMIT 1");
    $function->redirect($redirect);
    exit;
}*/
/*
$inputs1['order_type'] = 'dine_in';
$sale_orders_din_in = $function->getSaleOrderItemDetailsListDinin($inputs1);
//echo '<pre>'; print_r($sale_orders); die;
if((count($sale_orders_din_in) > 0) && ($set == 'yes')) { 
    echo "<script>alert('Dine_in items still Running. So cant able to settle');</script>";
    mysqli_query($GLOBALS['conn'],"DELETE FROM bank_deposit WHERE 1 ORDER BY id DESC LIMIT 1");
    $function->redirect($redirect);
    exit;
}*/


 /*   $inputs3['shop_id'] = $_SESSION['shop_id'];
   $inputs3['user_id'] = $_SESSION['user_id'];
   $inputs3['order_type'] = 'counter_sale';
   $inputs3['payment_status'] = 'unpaid';
$sale_orders_take_away = $function->getSaleOrderItemDetailsListTakeAway($inputs3);
//echo '<pre>'; print_r($sale_orders); die;
if((count($sale_orders_take_away) > 0) && ($set == 'yes')) { 
    echo "<script>alert('Take Away items still Running. So cant able to settle');</script>";
    mysqli_query($GLOBALS['conn'],"DELETE FROM bank_deposit WHERE 1 ORDER BY id DESC LIMIT 1");
    $function->redirect($redirect);
    exit;
}*/
$res = $settle_sale['sale_summary_details'];

// echo"<pre>"; print_r($res); die;

if($print == 'yes') {

//Settle sale report
        try {
            // Enter the share name for your USB printer here
            //$connector = null;
            // $connector = new WindowsPrintConnector(CUSTOMER_COPY);
            $connector = new WindowsPrintConnector(COUNTER_PRINTER);

            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);
            //$printer -> text($print_content);
            //$printer -> cut();

            //New Lines Added
            
            $date = date("d-m-Y H:i:s");
            /* Start the printer */
            $printer = new Printer($connector);

            // Regular pulse
            $printer->pulse();
            sleep(1);

            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            
            //$logo = EscposImage::load("resources/escpos-php.png", false);
            if(CLIENT_LOGO) {
                $logo = EscposImage::load(CLIENT_LOGO, false);
                $printer -> bitImage($logo);
            }

            /* Name of shop */
            $printer -> feed();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer -> text(CLIENT_NAME."\n");
            $printer -> feed();
            $printer -> selectPrintMode();
            $printer -> text(CLIENT_ADDRESS."\n".CLIENT_ADDRESS1."\n".CLIENT_NUMBER."\n\n".CLIENT_WEBSITE);
            $printer -> feed(1);

            /* Title of receipt */
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer -> text("SETTLE SALE REPORT\n");
            $printer -> setEmphasis(false);

            /* Date */
            $printer -> feed();
            $printer -> selectPrintMode();
            $printer -> text("Date : ".$date."\n");
            $printer -> feed(1);
             if($set == 'yes')
             {
             	  $printer -> text("SETTLE PRINT\n");
             }
             else
             {
                $printer -> text("PRINT COPY\n");
             }
            /* Description */
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer -> text(str_pad("Cash at Starting", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['cash_at_starting'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Cash Sale", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['cash_sale'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Card Sale", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['card_sale'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Credit Recovery", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['credit_recover'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Paytm", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['paytm_sale'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Sodexo", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['sodexo_sale'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            //$printer -> text(str_pad("Credit Sale", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['credit_sale'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            //$printer -> text(str_pad("Credit Recovery", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['credit_recover'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Delivery Recovery", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['delivery_recover'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));

            // $printer -> text(str_pad("Delivery Cash Recover", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['delivery_cash_recover'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
              // $printer -> text(str_pad("Delivery Card Recover", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['delivery_card_recover'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));            
            //$printer -> text(str_pad("Pay Back", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['pay_back'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Expense", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['expense'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') { 
            $printer -> text(str_pad("Total Vat", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['total_vat'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
        	}else if(BILL_TAX_TYPE == 'GST'){
            $printer -> text(str_pad("Total GST", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['total_sgst']+$settle_sale['total_cgst'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
       		 }	}
            // $printer -> text(str_pad("Bank Deposit", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['deposit_amount'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Amount In Cash Drawer", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['cash_drawer'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
            $printer -> text(str_pad("Gross Total", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['gross_total'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Discount", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['discount'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Redeem Discount", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['redeem_discount'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Net Total", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$settle_sale['net_total'], 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> setEmphasis(false);
            $printer -> feed(2);            
            
            /* Close printer */
            $printer -> cut();
            $printer -> close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

// Items wise sale report
        if(count($settle_sale['sale_summary_details']) > 0)
        {
    try {
            // Enter the share name for your USB printer here
            //$connector = null;
            // $connector = new WindowsPrintConnector(CUSTOMER_COPY);
            $connector = new WindowsPrintConnector(COUNTER_PRINTER);

            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);
            //$printer -> text($print_content);
            //$printer -> cut();

            //New Lines Added
            
            $date = date("d-m-Y H:i:s");
            /* Start the printer */
            $printer = new Printer($connector);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            
            //$logo = EscposImage::load("resources/escpos-php.png", false);
            if(CLIENT_LOGO) {
                $logo = EscposImage::load(CLIENT_LOGO, false);
                $printer -> bitImage($logo);
            }

            /* Name of shop */
            $printer -> feed();
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer -> text(CLIENT_NAME."\n");
            $printer -> feed();
            $printer -> selectPrintMode();
            $printer -> text(CLIENT_ADDRESS."\n".CLIENT_ADDRESS1."\n".CLIENT_NUMBER."\n\n".CLIENT_WEBSITE);
            $printer -> feed(1);

            /* Title of receipt */
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer -> text("ITEM WISE REPORT\n");
            $printer -> setEmphasis(false);

            /* Date */
            $printer -> feed();
            $printer -> selectPrintMode();
            $printer -> text("Date : ".$date."\n");
            $printer -> feed(1);

            /* Description */
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer -> text(str_pad("Item Name", 28, ' ', STR_PAD_RIGHT).str_pad("Qty", 8, ' ', STR_PAD_LEFT).str_pad("Value", 12, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
            $tax_value = $total_price = $discount_value = 0;
            foreach($settle_sale['sale_summary_details'] as $key => $sum_det) {

            $printer -> text(str_pad(strtoupper(str_replace('_',' ',$key)), 48, ' ', STR_PAD_BOTH));
            $printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));

                foreach ($sum_det as $key => $sum_det) 
                {               
                    $printer -> text(str_pad(character_limiter($sum_det['name'], 15, '...'), 28, ' ', STR_PAD_RIGHT).str_pad($sum_det['qty'], 8, ' ', STR_PAD_LEFT).str_pad(number_format((float)$sum_det['price'], 2, '.', ''), 12, ' ', STR_PAD_LEFT));
                    $tax_value += $sum_det['tax_value'];
                    $total_price += $sum_det['price'];
                    $discount_value = $sum_det['discount_value'];
                    $redeem_value = $sum_det['redeem_value'];
                }
                // $printer -> text(str_pad("", 48, ' ', STR_PAD_RIGHT));
            $printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
            }
            $printer -> text(str_pad("Gross Total", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$total_price, 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Discount", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$discount_value, 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> text(str_pad("Reedem Discount", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$redeem_value, 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            if(BILL_TAX == 'yes' && BILL_TAX_TYPE == 'VAT'){
            $printer -> text(str_pad("Total VAT", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$tax_value, 2, '.', ''), 15, ' ', STR_PAD_LEFT));
        	}else {
        		$tax_value = 0;
        	}
            $printer -> text(str_pad("Net Total", 33, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($total_price - $discount_value - $redeem_discount) + $tax_value, 2, '.', ''), 15, ' ', STR_PAD_LEFT));
            $printer -> setEmphasis(false);
            $printer -> feed(2);            
            
            /* Close printer */
            $printer -> cut();
            $printer -> close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }
    }

// Bill wise sale report
    

}

if($set == 'yes'){//echo '<pre>'; print_r($settle_sale);
    // $settle_sale = $function->setSettleSale($settle_sale);   
    //if($settle_sale){
    //die;
        //include('excel_export_settle_sale.php');
       // $function->redirect('excel_export_settle_sale.php');
        $settle_sale['settle_date'] = date("Y-m-d H:i:s");
        $settle_sale['shop_id'] = $_SESSION['shop_id'];
        $settle_sale['user_id'] = $_SESSION['user_id'];
        $settle_sale['discount_type'] = 'amount';
        $settle_sale['to_date'] = date("Y-m-d H:i:s");
        $settle_sale = $function->setSettleSale($settle_sale);  
    //  $function->redirect('excel_export_settle_sale.php');
         $function->redirect($redirect); 
        //header( "refresh:3;url=settle_sale.php");
    //}
} else {//die;
    $function->redirect($redirect);
}