<?php
use ChrisTenday\PdfGenerate\Pdf;

require_once "vendor/autoload.php";
$temp=__DIR__.DIRECTORY_SEPARATOR."template-mensuel.docx";

$pdf=new Pdf($temp);
$dest=__DIR__.DIRECTORY_SEPARATOR."uploads";
$pdf->setDestinationFolder($dest);
$pdf->setData("populationTotale","1.000.000");
$pdf->setData("province","Katanga");
//$pdf->setData("age","27");
$block1=$pdf->newBlock("table");
$block1->setData("culture","Tenday");
$block2=$pdf->newBlock("table");
$block2->setData("culture","Chris");
$block3=$pdf->newBlock("table");
$block3->setData("culture","Mesa");
/*$pdf->saveBlock($block1);
$pdf->saveBlock($block2);
$pdf->saveBlock($block3);*/

$pdf->generate();

?>
