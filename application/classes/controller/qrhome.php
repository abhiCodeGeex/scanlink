<?php

defined('SYSPATH') or die('No direct script access.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/phpwkhtmltopdf/vendor/autoload.php';
use mikehaertl\wkhtmlto\Pdf;

class Controller_Qrhome extends Controller_Template_Master
{

    public function before()
    {

        parent::before();

        $this->template->head = View::factory('template/head');

        // Set the default Page Title
        $this->template->head->title = 'Welcome to ScanLink';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen') /* , array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen') */, array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'));
        $actionnm = $this->request->action();
        // All JS Libraries and Scripts
        if ($actionnm != 'changepwd'):
            if ($actionnm == "scanalytics") {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
                $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
            } else {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
            } else :
            $this->template->head->javascripts = array('js/dashboard/jquery-1.4.2.js', 'js/jquery.validate.min.js', "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
        endif;
        // Default Header view file
        $this->template->headers = View::factory('template/headers');

        // Default menu
        $this->template->headers->menu = View::factory('template/menu');
        if ($this->session->get('clientuserid')):
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;
        $this->template->headers->menu->menu = $this->menu;
        /* WP_MS START  */
        $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
        /* WP_MS END  */
        $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
        // Default Content template
        $this->template->footer = View::factory('template/footer');

        if ($this->template->footer):
            // All JS Libraries and Scripts
            $this->template->footer->scripts = array();
        endif;

        $this->types = Model::factory('dashboard')->getTypes();
        $this->userid = $this->session->get('clientuserid');
    }

    public function action_createQRcode()
    {
        $url = "";
        $img_name = "";
        $session_id = $_REQUEST["session_id"];

        if (isset($_REQUEST["url"])) {
            $url = strtolower($_REQUEST["url"]);
        }

        $url1 = str_replace("http://", "", $url);
        $url1 = str_replace("www.", "", $url1);

        if (isset($_REQUEST["code_type"])) //0=qr code , 1= dm code
        {
            $code_type = $_REQUEST["code_type"];
        } else {
            $code_type = 0;
        }

        if ($url != "" && strlen($url1) > 3) {
            require_once "classes/shortURL.php";
            $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      		$short_url = shortenUrl($url, $api_token);
            //$short_url = shortenUrl($url); //create_short_url($url);

            $short_url = $short_url['shortLink'];
            if ($code_type == 0) // QR Code
            {

                //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=" . $short_url . "&chld=M|0";
		$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url . "&size=250x250&ecc=M";

                $QRCode = file_get_contents($QRURL);

                $img_name = $session_id . '.png';
                file_put_contents('images/home_qrcode/' . $session_id . '.png', $QRCode);

            } elseif ($code_type == 1) { // Data matrix
                $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url . "&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");

                $img_name = $session_id . '_dm.png';
                $QRImage = 'images/home_qrcode/' . $session_id . '_dm.png';

                $file = fopen(DOCROOT . $QRImage, "w");
                fwrite($file, $QRCode);
                fclose($file);

            }
        }

        if ($img_name != "") {
            $img_name = $img_name . "?" . rand(11111111, 99999999);
        }

        echo $img_name;
        exit;
    }

    public function action_downloadQR()
    {
        $session_id = "";
        if (isset($_REQUEST["session_id"])) {
            $session_id = $_REQUEST["session_id"];
        }

        if ($session_id != "") {
            if (isset($_REQUEST["download_as"])) {
                $download_as = $_REQUEST["download_as"];
            } else {
                $download_as = "pdf";
            }

            if (isset($_REQUEST["code_type"])) //0=qr code , 1= dm code
            {
                $code_type = $_REQUEST["code_type"];
            } else {
                $code_type = 0;
            }

            if ($code_type == 1) //dm code
            {
                $qr_path = 'images/home_qrcode/' . $session_id . '_dm.png';
            } else //qr code
            {
                $qr_path = 'images/home_qrcode/' . $session_id . '.png';
            }

            if ($download_as != "pdf") {
                switch ($download_as) {
                    case "tiff":

                        $download_filename = $session_id . ".tiff";
                        $tifffile = "images/home_qrcode/" . $session_id . ".tiff";

                        exec('convert ' . $qr_path . ' ' . $tifffile);
                        $download_file_path = $tifffile;

                        break;
                    case "eps":

                        $download_filename = $session_id . ".eps";
                        $espfile = "images/home_qrcode/" . $session_id . ".eps";

                        exec('convert ' . $qr_path . ' ' . $espfile);
                        $download_file_path = $espfile;

                        break;
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $download_filename);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($download_file_path));
                ob_clean();
                flush();
                readfile($download_file_path);

                @unlink($tifffile);
                @unlink($epsfile);

                exit;

            } else {

                //to generate pdf and download code start
                require_once 'pdf.php';

                $content1 = '<div align="center"><img src="' . URL::base() . 'images/logo.png" width="288" /></div><br>';
                $content1 .= 'Thank you for visiting ScanLink - Here\'s your code...<br>Generate \'dynamic\' codes with powerful tracking and data collection functions.';

                $content1 .= '<br><b><a href="' . URL::base() . 'register/index" style="text-decoration:none;color:#000000;">Account registration and your first code is FREE</a></b>';
                $content1 .= '<br><br><br><div align="center"><img src="' . URL::base() . $qr_path . '" width="150" /></div> ';

                // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
                // require "PHPExcel/PHPExcel/Shared/PDF/tcpdf_6/tcpdf.php";

                // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // $pdf->SetPrintHeader(false);
                // $pdf->SetPrintFooter(false);
                // // set margins
                // $pdf->SetMargins(25, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // // set auto page breaks
                // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

                // // set image scale factor
                // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // // set font
                // $fontname = $pdf->addTTFfont('calibri.ttf', 'TrueTypeUnicode', '', 32);
                // $pdf->SetFont($fontname, '', 12);

                // //$pdf->SetFont('helvetica', '', 12);

                // // add a page
                // $pdf->AddPage();

                // // output the HTML content
                // $html_content = str_replace("'", "&#039;", $content1);

                // $pdf->writeHTML($html_content, true, false, true, false, '');

                // $pdf->Output('code_' . $session_id . '.pdf', 'D');

                $this->download_formsubmission_log_pdf($content1, 'code_' . $session_id . '.pdf');
            }
        } //end of if($session_id!="")
        exit;
    } //function action_downloadQR end

    /**
     * Download Pdf using wkpdf plugin
     * 
     */
    public function download_formsubmission_log_pdf($pdf_content, $pdf_name)
    {
        $pdf = new Pdf(array(
            'binary' => '/usr/local/bin/wkhtmltopdf',
            'margin-top'    => 25,
            'margin-right'  => 10,
            'margin-bottom' => 10,
            'margin-left'   => 10,
            'zoom' => 1.25
        ));
        $pdf->addPage($pdf_content);

        if (!$pdf->send($pdf_name)) {
            $error = $pdf->getError();
            mail('shaileshk@webplanex.com', 'QR Code download error ', $error);
        }
    }

}

// End Welcome
