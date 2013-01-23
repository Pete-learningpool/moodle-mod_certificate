<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from view.php in mod/tracker
}

  // Date formatting - can be customized if necessary
$certificatedate = certificate_get_date($certificate,$certrecord, $course);
//Grade formatting
$grade =  certificate_get_grade($certificate, $course);
//Print the outcome
$outcome = certificate_get_outcome($certificate, $course);

// Print the code number
$code = '';
if ($certificate->printnumber) {
    $code = $certrecord->code;
}

  // Print the code number
  $code = '';
  if($certificate->printnumber) {
  $code = $certrecord->code;
  }

//Print the student name
$studentname = '';
$studentname = fullname($USER);
$classname = '';
$classname = $course->fullname;
//Print the credit hours
if ($certificate->printhours) {
    $credithours = $strcredithours . ': ' . $certificate->printhours;
} else
    $credithours = '';

$customtext = $certificate->customtext;
$orientation = $certificate->orientation;
$pdf = new TCPDF($orientation, 'mm', 'A4', true, 'UTF-8', false);
#$pdf->SetProtection(array('print'));
$pdf->SetTitle($certificate->name);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

//Define variables
//Landscape
if ($certificate->orientation == 'L') {
    $x = 10;
    $y = 50;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 140;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else {
//Portrait
    $x = 10;
    $y = 60;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 200;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
}

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$certificatetitle = (!empty($certificate->title)) ? $certificate->title : get_string('titledefault', 'certificate');
$classname = (!empty($certificate->coursename)) ? $classname = $certificate->coursename : $course->fullname;

$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y, 'C', 'Helvetica', 'B', 26, $certificatetitle);
certificate_print_text($pdf, $x, $y + 20, 'C', 'Helvetica', '', 16, get_string('certify', 'certificate'));
certificate_print_text($pdf, $x, $y + 36, 'C', 'Helvetica', 'B', 26, $studentname);
certificate_print_text($pdf, $x, $y + 55, 'C', 'Helvetica', '', 16, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 65, 'C', 'Helvetica', 'B', 16, $classname);
certificate_print_text($pdf, $x, $y + 77, 'C', 'Helvetica', '', 13, get_string('onthisdate', 'certificate'));
certificate_print_text($pdf, $x, $y + 85, 'C', 'Helvetica', 'B', 14, $certificatedate);
certificate_print_text($pdf, $x, $y + 95, 'C', 'Helvetica', '', 10, $grade);
certificate_print_text($pdf, $x, $y + 105, 'C', 'Helvetica', '', 10, $outcome);
certificate_print_text($pdf, $x, $y + 115, 'C', 'Helvetica', '', 10, $credithours);
certificate_print_text($pdf, $x, $codey, 'C', 'Helvetica', '', 10, $code);
$i = 0;
if ($certificate->printteacher) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, ($sigy + 10) + ($i * 4), 'L', 'Helvetica', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', '', '', '', $customtext);
