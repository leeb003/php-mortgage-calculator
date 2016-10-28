<?php
/** 
 * Working script (handler for ajax requests from mortgage calculator).  
 * Calculates the Amortization schedule and returns the results.
 *
**/

    foreach ($_GET as $key => $value) {   // preliminary sanitize
        $key = trim(htmlentities($key));
        $value = trim(htmlentities($value));
        $value = preg_replace('/,/','',$value);
        $get[$key] = $value;
    }

    $jsonData = array();
    $jsonData['error'] = 0;

    if ($get['type'] == 'adminSubmit') {   // Submit administrative changes
        $dPrice = $get['dPrice'];
        $dInt = $get['dInt'];
        $ddp = $get['ddp'];
        $term = $get['term'];
        $hir = $get['hir'];
        $pmi = $get['pmi'];
        $layout = $get['layout'];
        $website = $get['website'];
        $url = $get['url'];
        $email = $get['email'];
        $fromEmail = $get['fromEmail'];
        $allowEmail = $get['allowEmail'];
        $blurb = $get['blurb'];
        $disclaimer = $get['disclaimer'];
        if (!empty($pmi)) {


            $data = '<?php
/** 
* Config file for Mortgage Calculator
* This file holds settings that affect the mortgage calculator tool.
*
**/

// Defaults for initial calculator
define(\'DPRICE\', \'' . $dPrice . '\');
define(\'DINT\', \'' . $dInt . '\');
define(\'DDP\', \'' . $ddp . '\');
define(\'DTERM\', \'' . $term . '\');


// Other factors (taxes, insurance, pmi)
define(\'HIR\', \'' . $hir . '\');   // Homeowners Insurance Rate
define(\'PMI\', \'' . $pmi . '\');   // Private Mortgage Insurance


// Settings
define(\'LAYOUT\', \'' . $layout . '\'); // amortization schedule modal, div or popup
define(\'WEBSITE\', \'' . $website . '\'); // Used in pdf emails to let your customers know where they came from 
define(\'URL\', \'' . $url . '\');  // Used for scripts and file reference locations (include http://), no trailing slashes
define(\'EMAIL\', \'' . $email . '\'); // Used for bcc to you so you can contact the customer 
define(\'FROMEMAIL\', \'' . $fromEmail . '\');  // The email address that shows in the from field
define(\'ALLOWEMAIL\', \'' . $allowEmail . '\');  // Allow email to send report to customer
define(\'SHOWBLURB\', \'yes\');   // Option to give information about other costs before schedule

// Disclaimer can say what you want
define(\'DISCLAIMER\', \'' . $disclaimer . '\');';

            $config = 'config.php';
            $fp = fopen($config, "w");
            fputs($fp, $data);
            fclose ($fp);
        }
        

    } elseif ($get['type'] == 'getCalc') {      // Proccess the request
        require_once 'config.php';         // import settings

        $price    = floatval($get['priceHome']);
        $interest = floatval($get['interest']);
        $down     = $get['downPay'];
        $term     = intval($get['term']);

        if ($price == 0) {
            $jsonData['error'] = 1;
            $jsonData['message'] = 'Please Enter a valid price.';
        } elseif ($interest == 0) {
            $jsonData['error'] = 1;
            $jsonData['message'] = 'Please Enter a valid interest rate.';
        } elseif (!is_numeric($down)) {
            $jsonData['error'] = 1;
            $jsonData['message'] = 'Please Enter a valid down payment percent (can be 0).';
        } elseif ( $term == 0) {
            $jsonData['error'] = 1;
            $jsonData['message'] = 'Term cannot be 0.';
        }

        if ($jsonData['error'] == 1) {          // If errors are detected return message and quit
            header("content-type: application/json"); 
            echo $_GET['callback'] . '('.json_encode($jsonData) .')';
            die();
        }

        $years = $term;


        //////////////////////////////////////////
        // P & I
        $moneydown = $price * ($down / 100);
        $moneydown2 = number_format ($moneydown, 2);
        $jsonData['blurb'] =  "<div class='blurb'><p>Down Payment: <b>\$$moneydown2</b><br>";
        $mortgage = $price - $moneydown;
        $mortgage2 = number_format ($mortgage, 2);
        $jsonData['blurb'] .=  "Mortgage amount after down payment: <b>\$$mortgage2</b><br>";
        $month_interest = ($interest / (12 * 100));


        //echo "Monthly Interest is (interest / (12 * 100)) = $month_interest<br>";
        $months = $term * 12;
        //echo "Total Months for loan is (term x 12) = $months<br>"; 

        $monthly_payment = $mortgage * ($month_interest / (1 - pow((1 + $month_interest), -$months) ));

        $monthly_payment2 = number_format ($monthly_payment, 2);
        $jsonData['blurb'] .=  "Monthly Payment: <b>\$$monthly_payment2</b> (Principal and Interest Only)<br>";
        ////////////////////////////////////
        // PMI and Taxes
        $jsonData['blurb'] .=  'Since Principal and Interest are not the only factors'
            .' of a loan we should include an estimate for PMI, Taxes and Insurance.  ';
        $jsonData['blurb'] .=  'An average tax figure for your home could be about'
                            .  ' $10 for every $1000 assessed value.  ';

        $assessed = ($price * 0.85);
        $taxes = (($assessed / 1000) * 10) / 12;
        $assessed2 = number_format ($assessed, 2);
        $taxes2 = number_format ($taxes, 2);
        $jsonData['blurb'] .=  "If the assessed value of your home is 85%, this would"
            ." make your home's assessed value \$$assessed2, and your monthly tax \$$taxes2.  ";

        if ($down < 20) {
            $jsonData['blurb'] .=  'Your down payment was less than 20% of the loan. Which means'
                .' you will be paying PMI. This averages around $' . PMI . ' for every $100,000 borrowed.';
            $pmi = ($mortgage / 100000) * 56;
            $pmi2 = number_format ($pmi, 2);
            $jsonData['blurb'] .=  "  An estimate for PMI will be around \$$pmi2 per month.";
            $total_payment = $monthly_payment + $pmi + $taxes + HIR;
            $total_payment2 = number_format ($total_payment, 2);
            $jsonData['blurb'] .=  '<br><br>With PMI, Taxes, and Homeowners Insurance ($' . HIR . ' average)'
                .' your payment would be close to <b>$' . $total_payment2 . '</b>.</p></div>  ';
        } else {
            $total_payment = $monthly_payment + $taxes + HIR;
            $total_payment2 = number_format ($total_payment, 2);
            $jsonData['blurb'] .=  'Since you are putting down 20% or greater, you will not have to'
                .' pay PMI.  So, your monthly payment with taxes and estimated homeowners insurance ($' . HIR . ')'
                .' would be around <b>$' . $total_payment2 . '</b>.</p></div>  ';

        }

        $lifetime_mortgage  = ($total_payment * 12) * $years;
        $lifetime_mortgage2 = number_format ($lifetime_mortgage, 2);

        $total_taxes        = $taxes * ($years * 12);
        $total_taxes2       = number_format ($total_taxes, 2);

        /*
        //tax savings
        $total_tax_savings  = ($total_taxes + $total_interest) * .355;
        $total_tax_savings2 = number_format ($total_tax_savings, 2);
        */

        /////////////////////////////////////
        // Amortization Schedule
        $years = $term;
        $i = 0;
        $total_interest = 0;
        $new_mortgage = $mortgage;
        $month_range = ($years * 12);
        $month_range2 = range (1, $month_range);

        foreach ($month_range2 as $value)  {
            $int_amt         = $month_interest * $new_mortgage;
            $principal       = $monthly_payment - $int_amt;
            $new_mortgage    = $new_mortgage - $principal;
            //$total_principal = $total_principal + $principal;
            $total_interest  = $total_interest + $int_amt;
            ///////////////////////////////////////////////
            //formatting
            $int_amt2        = number_format ($int_amt,2);
            $principal2      = number_format ($principal,2);
            $new_mortgage2   = number_format (abs($new_mortgage),2);
            $total_interest2 = number_format ($total_interest,2);
            $i++;
            /////////////////////
            $jsonData['payment'][$i]['value'] = $value;
            $jsonData['payment'][$i]['interest'] = $int_amt2;
            $jsonData['payment'][$i]['principal'] = $principal2;
            $jsonData['payment'][$i]['newMortgage'] = $new_mortgage2;
        }
        //$total_interest2 = number_format ($total_interest, 2);

        if ($get['email'] == 'yes') {
             if (isset($get['eAddress'])) {

                // test the email address given
                if (!filter_var($get['eAddress'], FILTER_VALIDATE_EMAIL)) {
                    $jsonData['error'] = 1;
                    $jsonData['message'] = 'You entered an invalid email address.';
                    header("content-type: application/json"); 
                    echo $_GET['callback'] . '('.json_encode($jsonData) .')';
                    die();
                }

                // Generate PDF and mail it
                $pdfResults = generatePDF($jsonData);

                // Send Email //
                $date = strtotime('now');
                $date = date("m-d-Y", $date);   // date for file printout
                $eol = PHP_EOL;
                $attachment = chunk_split( base64_encode($pdfResults) );
                $to = $get['eAddress'];

                $seperator = md5(time());
                $headers = '';

                if (EMAIL != '') { // bcc the owner (simple empty check
                    $headers .= "Bcc: " . EMAIL . $eol;
                }
                $headers .= 'From: ' . FROMEMAIL . $eol;
                $headers .= "MIME-Version: 1.0".$eol;
                $headers .= "Content-Type: multipart/mixed; boundary=\"".$seperator."\"";

                $subject = "Mortgage calculator results from " . WEBSITE;
                $message = "Attached is the mortgage results from " . WEBSITE . " you generated on $date.\n\n";

                // message
                $msg = "--".$seperator.$eol;
                $msg .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
                $msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
                $msg .= $message.$eol;

                // attachment
                $msg .= "--".$seperator.$eol;
                $msg .= "Content-Type: application/octet-stream; name=\"mortgage-".$date.".pdf\"".$eol;
                $msg .= "Content-Transfer-Encoding: base64".$eol;
                $msg .= "Content-Disposition: attachment".$eol.$eol;
                $msg .= $attachment.$eol;
                $msg .= "--".$seperator."--";
            
                mail($to, $subject, $msg, $headers);
                // $pdf->save('/var/www/html/amort/example.pdf');
            }
        }


    } else {
        $jsonData['error'] = 1;
        $jsonData['message'] = 'You entered a rediculous value!.';
    }

header("content-type: application/json"); 
echo $_GET['callback'] . '('.json_encode($jsonData) .')';

// PDF export of mortgage results using the tcpdf lib
function generatePDF($jsonData) {

    $today = date("l F j, Y g:i a");

    require_once 'tcpdf/config/lang/eng.php';
    require_once 'tcpdf/tcpdf.php';

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('scripthat.com');
    $pdf->SetTitle('Mortgage Schedule');
    $pdf->SetSubject("Loan Details");
    $pdf->SetKeywords('Loan, Mortgage, Amortization');

    // set default header data
    // set default header data
    $pdf->SetHeaderData("", 60, "Mortgage Information", "$today", array(0,64,255), array(0,64,128));

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('helvetica', '', 8, '', true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();


    $html = '<h3>brought to you by ' . WEBSITE . '</h3>';
    $html .= $jsonData['blurb'];

    $html .= '<p>Amortization Schedule (P & I)</p>'
           . '<table class="schedule scroll">'
           . '<tr><th><b>Payment</b></th><th><b>Interest</b></th><th><b>Principal</b></th>'
           . '<th><b>New Mortgage</b></th></tr>';

    foreach ($jsonData['payment'] as $key => $val) {
        $html .= '<tr><td>' . $val['value'] . '</td><td>' . $val['interest'] . '</td><td>'
              . $val['principal'] . '</td><td>' . $val['newMortgage'] . '</td></tr>';
    }
    $html .= '<p>' . DISCLAIMER . '</p>';

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdfString = $pdf->Output('dummy.pdf', 'S');

    return $pdfString;
}
