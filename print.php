<?php

require_once('TCPDF\tcpdf.php');

$servername = "localhost";
$username = "root";
$password = "root@321";
$dbname = "label";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//defaults
//$showMerchant = (isset($_POST["showMerchant"]) ? $_POST["showMerchant"] : 0);
//$showSKU = (isset($_POST["showSKU"]) ? $_POST["showSKU"] : 0);
$showMobile = (isset($_POST["showMobile"]) ? $_POST["showMobile"] : 0);
$showAddress = (isset($_POST["showAddress"]) ? $_POST["showAddress"] : 0);
$showAmount = (isset($_POST["showAmount"]) ? $_POST["showAmount"] : 0);
$itemLimit = (isset($_POST["itemLimit"]) ? $_POST["itemLimit"] : 8);
$barcodeType = 'QRCODE';
$promptPrint = false;



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Label');
$pdf->SetTitle('Label');
$pdf->SetSubject('Label');
$pdf->SetKeywords('Label');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(0,0,0);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set fonts
$pdf->setFontSubsetting(true);
$pdf->SetFont('freeserif', '', 9);

//start adding page
$pdf->AddPage();
        
$sql = "SELECT * FROM orders where address_with_separator is not null and qrcode is not null";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $i = 0; //setting loop index
    while ($item = $result->fetch_assoc()) {
        $i++;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(0, 0, 0)));

        //$address = mb_convert_encoding($address[1],"windows-1251", "windows-1251")
        //$address = split('|', $item['address_with_separator']);

        
        $y = $pdf->getY();

        if ($i <= $itemLimit) {
            $html = '
            <table width="100%" border="0" cellpadding="5">
                <tr>
                    <td align="center" width="75"> 
                        <div style="height:100px"><br><br><br><br></div>
                        <div><span style="text-align:center; font-size:30px; font-weight:bold;">&nbsp;&nbsp;'.($item['qrcode2']? "B": "").'</span></div>
                    </td>
                    <td align="left" width="270">
                        <div>
                            <table width="100%" style="padding-top:10px" cellspacing="0" border="0" cellpadding="0">
                                <tr>
                                    <td width="30%"><span style="text-align:left;font-size:12;font-weight:bold;">'.(($showMobile == 1) ? $item['mobile'] : '').'</span></td>
                                    <td width="70%"><span style="text-align:right;font-size:12;font-weight:bold;">'.(($showAmount == 1) ? $item['cod'].' '.$item['amount'] : '').'</span></td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" border="0" cellpadding="0">
                                <tr>
                                    <td><span style="padding-left:15px;font-size:12;line-height:1.7">'.(($showAddress == 1) ? $item['address'] : '').'</span></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            <img src="images/dash.png">
            ';

            if ($i % 2 == 0) {
                $pdf->write2DBarcode(($item['qrcode2'] ? $item['qrcode2'] : $item['qrcode']), $barcodeType, 110.2, $y+6.5, 17, 17);
            } else {
                $pdf->write2DBarcode(($item['qrcode2'] ? $item['qrcode2'] : $item['qrcode']), $barcodeType, 4, $y+6.5, 17, 17);
            }

        } else {
            $html = '';
        }

        
        //prints content body
        /*
            $this->writeHTMLCell(
            $width = 0, // width of the cell, not the input
            $height = 0, // height of the cell..
            $x,
            $y,
            $html = '', // your input.
            $border = 0,
            $ln = 0,
            $fill = false,
            $reseth = true,
            $align = '',
            $autopadding = true 
        );
        */

        if ($i % 2 == 0) {
            if ($i > 2) $pdf->Image('images/dash-black.png', 107, $y-1.5, 106, 3);
            $border = array(
                    'L' => array(
                        'width' => 0.2, // careful, this is not px but the unit you declared
                        'dash'  => 5,
                        'color' => array(0, 0, 0)
                    )
                );
            $pdf->writeHTMLCell(106, 74.2, '', '', $html, $border, 1, 1, true, 'J', true);
            $pdf->Ln(0);

        } else {
            /* $border = array(
                'B' => array(
                    'width' => 1, // careful, this is not px but the unit you declared
                    'dash'  => 10,
                    'color' => array(0, 0, 0)
                )
            ); */
            if ($i > 2) $pdf->Image('images/dash-black-scissor.png', 0, $y-1.5, 104, 3);
            $pdf->writeHTMLCell(106, 74.2, '', $y, $html, $border = 0, 0, 1, true, 'J', true);
        }

        //max page
        if ($i == 8) {
            $i = 0;
            $pdf->AddPage();
        }
        
    }
}

// set javascript to auto display the print option
if($promptPrint){
    $js = 'print(true);';
    $pdf->IncludeJS($js);
}

// Close and output PDF document
/* 
if (file_exists('PrintLabel.pdf')) unlink('PrintLabel.pdf');
$pdf->Output('PrintLabel.pdf', 'I'); 
*/
$pdf->Output('PrintLabel.pdf', 'I'); 

/* 
$base = __DIR__;
if (file_exists($base.'\files\PrintLabel.pdf')) unlink($base.'\files\PrintLabel.pdf');
$pdf-> Output($base.'\files\PrintLabel.pdf', 'F'); 
*/


$conn->close();

?>
