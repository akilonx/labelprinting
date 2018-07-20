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
$barcodeType = 'QRCODE';
$promptPrint = false;


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'pt', PDF_PAGE_FORMAT, true, 'UTF-8', false);


$sql = "SELECT * FROM papersize where active='1'";
$result = $conn->query($sql);
$resultIndex = $result->num_rows;

if ($result->num_rows > 0) {
    $i = 0; //setting loop index
    while ($item = $result->fetch_assoc()) {
        $config = $item;
        $config['totalBlocks'] = $config['totalY'] * $config['totalX'];

        //$width1 = $pdf->pixelsToUnits($config['paperWidth']); 
        $config['blockWidth'] = $config['paperWidth'] / $config['totalX'];
        $config['blockHeight'] = $config['paperHeight'] / $config['totalY'];
    }
}

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
//$pdf->AddPage();
//$width = $pdf->pixelsToUnits($config['paperWidth']); 
//$height = $pdf->pixelsToUnits($config['paperHeight']);

$resolution= array($config['paperWidth'], $config['paperHeight']);
$pdf->AddPage('P', $resolution);

$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(0, 0, 0));
$linestyle_grey = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(211,211,211));

function addSpace($a, $pos){
    global $pdf; 
    global $config;
    global $linestyle;

    $a = ($pos == 1) ? $a : $a + 1;

    for ($i = $pos; $i <= $a; $i++) {

        $y = $pdf->getY();

        if ($i % $config['totalX'] == 0) {
            if ($i > 1 && $config['totalX'] == 1) {
                $pdf->Line(1, $y, 1110, $y, $linestyle); 
                $pdf->Image('images/scissor.png', 23, $y-5, 17, 10);
            }
            $border = ($config['totalX'] > 1) ? array('L' => array('width' => 0.2, 'dash'  => 5, 'color' => array(0, 0, 0))) : 0;
            $pdf->writeHTMLCell($config['blockWidth'], $config['blockHeight'], '', '', '', $border, 1, 1, true, 'J', true);
            $pdf->Ln(0);
        } else {
            if ($i > $config['totalX']) {
                $pdf->Line(1, $y, 1110, $y, $linestyle); 
                $pdf->Image('images/scissor.png', 23, $y-5, 17, 10);
            }
            $pdf->writeHTMLCell($config['blockWidth'], $config['blockHeight'], '', $y, '', $border = 0, 0, 1, true, 'J', true);
        }
    }
}


//Program start        
$sql = "SELECT * FROM orders where address is not null and qrcode is not null order by id asc limit 25";
$result = $conn->query($sql);
$resultIndex = $result->num_rows;

if ($result->num_rows > 0) {
    $i = 0; 
    while ($item = $result->fetch_assoc()) {
        $i++;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineStyle($linestyle);
        
        $y = $pdf->getY();

        $html = '
        <table width="100%" border="0" cellpadding="5" style="border-bottom: 1px dashed #888888">
            <tr>
                <td align="center" width="20%"> 
                    <div><br><br><br><br></div>
                    <div><span style="text-align:center; font-size:30px; font-weight:bold;">&nbsp;&nbsp;'.($item['qrcode2']? "B": "").'</span></div>
                </td>
                <td align="left" width="80%">
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
        
        ';

        if ($i % $config['totalX'] == 0 && $config['totalX'] > 1) {
            $pdf->write2DBarcode(($item['qrcode2'] ? $item['qrcode2'] : $item['qrcode']), $barcodeType, $config['blockWidth'] + 10, $y+19, 50, 50);
        } else {
            $pdf->write2DBarcode(($item['qrcode2'] ? $item['qrcode2'] : $item['qrcode']), $barcodeType, 10, $y+19, 50, 50);
        }

        if ($i % $config['totalX'] == 0) {
            if ($i > 1 && $config['totalX'] == 1) {
                $pdf->Line(1, $y, 1110, $y, $linestyle); 
                $pdf->Image('images/scissor.png', 23, $y-5, 17, 10);
            }
            $border = ($config['totalX'] > 1) ? array('L' => array('width' => 0.2, 'dash'  => 5, 'color' => array(0, 0, 0))) : 0;
            $pdf->writeHTMLCell($config['blockWidth'], $config['blockHeight'], '', '', $html, $border, 1, 1, true, 'J', true);
            $pdf->Ln(0);

        } else {
            if ($i > $config['totalX']) {
                $pdf->Line(1, $y, 1110, $y, $linestyle); 
                $pdf->Image('images/scissor.png', 23, $y-5, 17, 10);
            }
            $pdf->writeHTMLCell($config['blockWidth'], $config['blockHeight'], '', $y, $html, $border = 0, 0, 1, true, 'J', true);
        }

        if ($i == $config['totalBlocks']) {
            //max page
            //addSpace(8 - $itemLimit, 1);
            $i = 0;
            $pdf->AddPage();
        }

        if(--$resultIndex === 0){
            $pos = ($i % $config['totalX'] == 0) ? 1 : 2;
            addSpace($config['totalBlocks'] - $i, $pos);
        }

    }
}

// set javascript to auto display the print option
if($promptPrint){
    $js = 'print(true);';
    $pdf->IncludeJS($js);
}

$pdf->Output('PrintLabel.pdf', 'I'); 
/* 
$base = __DIR__;
if (file_exists($base.'\files\PrintLabel.pdf')) unlink($base.'\files\PrintLabel.pdf');
$pdf-> Output($base.'\files\PrintLabel.pdf', 'F'); 
*/

$conn->close();

?>
