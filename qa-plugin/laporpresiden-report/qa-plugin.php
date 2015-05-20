<?php

/*
	Plugin Name: Report Lapor Presiden
	Plugin URI: 
	Plugin Update Check URI: 
	Plugin Description: Report Lapor Presiden
	Plugin Version: 1.0
	Plugin Date: 2015-05-09
	Plugin Author: Ajigile Team
	Plugin Author URI: http://laporpresiden.org
	Plugin Minimum Question2Answer Version: 1.4
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
include('mpdf/mpdf.php');
qa_register_plugin_module('module', 'qa-layout.php', 'qa_lappres_admin', 'Lapor Presiden Admin');

function downloadPDF($html,$title,$output){
	$mpdf=new mPDF('c','A4','','',32,25,27,25,16,13);

	$header = '
	<table width="100%" style="border-bottom: 1px solid #000000; vertical-align: bottom; font-family: serif; font-size:16px;">
	<tr>
	<td width="50%">'.$title.'</td>
	<td width="50%" style="text-align: right;">{DATE j-m-Y H:m}</td>
	</tr>
	</table>
	';
	$mpdf->mirrorMargins = 1;
	$mpdf->defaultheaderfontsize = 10;
	$mpdf->defaultheaderfontstyle = B;
	$mpdf->defaultheaderline = 1;
	$mpdf->defaultfooterfontsize = 12;
	$mpdf->defaultfooterfontstyle = B;
	$mpdf->defaultfooterline = 1;
	$mpdf->SetHTMLHeader($header);
	$mpdf->SetFooter('{PAGENO} of {nb}');

	$mpdf->AddPage('L');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html,2);
	$mpdf->Output($output,'I');
	exit;
}