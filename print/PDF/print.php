<?php
	include('../../includes/inc.php');
// Include the main TCPDF library (search for installation path).
	require_once('tcpdf_include.php');
	
	$textIN = array("\"","\n");
  	$textOUT = array("\\\"","<br/>");

	if(isset($_GET["pg"])) {
		$pgid=$_GET["pg"];
	}

	$pgQuery=mysqli_query($con,"SELECT * FROM pages WHERE id = ".$pgid);
	while($pg=mysqli_fetch_assoc($pgQuery)){
		$dir=ABSDIR.'proofs/IMG/';
		$img=$dir.$pg["id"].'.jpg';
		$date=timestamp(strtotime("now"),$_COOKIE["tz"]);
		$size=getimagesize($img);
		$w=100/$size[0];
		$h=249/$size[1];
		$factor=min($w,$h);
		$xDim=$factor*$size[0];
		$yDim=$factor*$size[1];
		$rotate='P';

// create new PDF document
		$pdf = new TCPDF($rotate, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Proof Express');
		$pdf->SetTitle(adName($pg["ad"]).'/'.$pg["name"].' (Rev. '.$pg["rev"].')');
		$pdf->SetSubject('SpartanNash Annotated Proof Express');
		$pdf->SetKeywords('SpartanNash, Proof Express');

// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.adName($pg["ad"]).' / '.$pg["name"].' (Rev. '.$pg["rev"].')', PDF_HEADER_STRING.' '.$date, array(0,0,0), array(0,0,0));
		$pdf->setPrintFooter(false);

// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l); 
		}

// -------------------------------------------------------------------

// add a page
		$pdf->AddPage();

// set JPEG quality
		$pdf->setJPEGQuality(75);

// Image method signature:
// Image($file, $x='', $y='', $aid=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Image example with resizing
		$pdf->Image($img, 10, 20, 100, 249, 'JPG', '', '', true, 300, '', false, false, 1, true, false, false);

// Collect markups
		$nString="SELECT * FROM annotation WHERE project = ".$pg["project"]." AND wk = ".$pg["ad"]." AND page = '".$pg["name"]."' AND rev = ".$pg["rev"]." ORDER BY hly";
		$nQuery=mysqli_query($con,$nString) or die(mysqli_errno($nQuery));
		$i = 0;
		$m = 0;
		if(mysqli_num_rows($nQuery)>0) {
			while($note = mysqli_fetch_assoc($nQuery)){
				$iend = $i;
				$mend = $m;
				$hlx[$i] = round($note["hlx"]*$factor)+10;
  				$hly[$i] = round($note["hly"]*$factor)+20;
  				$hlw[$i] = round($note["hlw"]*$factor);
  				$hlh[$i] = round($note["hlh"]*$factor);
  				$hlRGB = explode(",",$note["hl"]);
  				$hlR[$i] = $hlRGB[0];
  				$hlG[$i] = $hlRGB[1];
  				$hlB[$i] = $hlRGB[2];
  		
  				$q[$i] = $note["hlx"];
  				$r[$i] = $note["hly"];
  				$u[$i] = $note["hlw"];
  				$j[$i] = $note["hlh"];
  				$hlText[$i] = '<p style="font-size:11px">'.str_replace($textIN,$textOUT,$note["text"]).'<br/><small><i>'.$note["user"].' - '.timestamp($note["modified"],$_COOKIE["tz"]).'</p>';
  		
  				$pdf->SetFillColor($hlR[$i],$hlG[$i],$hlB[$i]);
				$pdf->SetDrawColor(0, 0, 0);
				$pdf->SetAlpha(0.5);
				$pdf->Rect($hlx[$i], $hly[$i], $hlw[$i], $hlh[$i], 'DF');
		
				if($i==0) $textTop=$hly[$i];
		
				$i++;
				$m++;
			}
		}
// Collect notes
		$nString="SELECT * FROM notes WHERE project = ".$pg["project"]." AND wk = ".$pg["ad"]." AND page = '".$pg["name"]."' AND rev = ".$pg["rev"]." ORDER BY modified";
		$nQuery=mysqli_query($con,$nString) or die(mysqli_errno($nQuery));
		if(mysqli_num_rows($nQuery)>0) {
			while($note = mysqli_fetch_assoc($nQuery)){
				$iend = $i;
				$hlRGB = explode(",",$note["hl"]);
  				$hlR[$i] = $hlRGB[0];
  				$hlG[$i] = $hlRGB[1];
  				$hlB[$i] = $hlRGB[2];
  				$hlText[$i] = '<p style="font-size:11px">'.str_replace($textIN,$textOUT,$note["notes"]).'<br/><small><i>'.$note["user"].' - '.timestamp($note["modified"],$_COOKIE["tz"]).'</p>';
  		
  				$pdf->SetFillColor($hlR[$i],$hlG[$i],$hlB[$i]);
				$pdf->SetDrawColor(0, 0, 0);
				$pdf->SetAlpha(0.5);
		
				if($i==0) $textTop=20;
		
				$i++;
			}
		}

		if($i>0){
	//	Markup text boxes
			$pdf->SetXY(110, $textTop);
			for($i=0;$i<=$iend;$i++){
				$R = str_replace("0","128",$hlR[$i]);
				$G = str_replace("0","128",$hlG[$i]);
				$B = str_replace("0","128",$hlB[$i]);
				$textLinkY[$i] = $pdf->GetY();
				$textLinkX[$i] = $pdf->GetX();
				$pdf->SetFillColor($R,$G,$B);
				$pdf->SetAlpha(1);
				$pdf->MultiCell(80, 5, $hlText[$i], 0, 'L', 1, 1, '120', '', true, 0, true, true, 0);
				$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
			}

	//	Connecting arrows
			if($m>0){
				for($m=0;$m<=$mend;$m++){
					$pdf->SetAlpha(1);
					$arrowStyle = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(30, 30, 30));
					$linkMarkX = $hlx[$m] + ($hlw[$m]/2);
					$linkMarkY = $hly[$m] + ($hlh[$m]/2);
					$linkTextX = $textLinkX[$m];
					$linkTextY = $textLinkY[$m] + 2;
	
					$pdf->SetLineStyle($arrowStyle);
					$pdf->SetFillColor($hlR[$m], $hlG[$m], $hlB[$m]);
					$pdf->Arrow(120, $linkTextY, $linkMarkX, $linkMarkY, 2, 5, 15);
				}
			}
		} else {
			$pdf->SetXY(110, 40);
			$pdf->SetFillColor(255,255,0);
			$pdf->SetAlpha(1);
			$pdf->MultiCell(80, 5, 'There are no markups to display.', 0, 'L', 1, 1, '120', '', true);
			$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/*
// test fitbox with all alignment combinations

$horizontal_alignments = array('L', 'C', 'R');
$vertical_alignments = array('T', 'M', 'B');

$x = 15;
$y = 35;
$aid = 30;
$h = 30;
// test all combinations of alignments
for ($i = 0; $i < 3; ++$i) {
	$fitbox = $horizontal_alignments[$i].' ';
	$x = 15;
	for ($j = 0; $j < 3; ++$j) {
		$fitbox[1] = $vertical_alignments[$j];
		$pdf->Rect($x, $y, $aid, $h, 'F', array(), array(128,255,128));
		$pdf->Image('images/image_demo.jpg', $x, $y, $aid, $h, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
		$x += 32; // new column
	}
	$y += 32; // new row
}

$x = 115;
$y = 35;
$aid = 25;
$h = 50;
for ($i = 0; $i < 3; ++$i) {
	$fitbox = $horizontal_alignments[$i].' ';
	$x = 115;
	for ($j = 0; $j < 3; ++$j) {
		$fitbox[1] = $vertical_alignments[$j];
		$pdf->Rect($x, $y, $aid, $h, 'F', array(), array(128,255,255));
		$pdf->Image('images/image_demo.jpg', $x, $y, $aid, $h, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
		$x += 27; // new column
	}
	$y += 52; // new row
}
*/
// -------------------------------------------------------------------

//Close and output PDF document
		$pdf->Output(adName($pg["ad"]).'_'.$pg["name"].'_v'.$pg["rev"].'.pdf', 'I');
	}

//============================================================+
// END OF FILE
//============================================================+
?>