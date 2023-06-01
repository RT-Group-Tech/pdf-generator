# pdf-generate
Composant pour générer un pdf à partir d'un document word template.

#Example code:

use ChrisTenday\PdfGenerate\Pdf;

require_once "vendor/autoload.php";

Path du document word modèle à partir duquel générer un pdf.<br>
$temp=__DIR__.DIRECTORY_SEPARATOR."template-mensuel.docx";

<span>//Initilialisation de l'objet pdf</span><br>
$pdf=new Pdf($temp); 

<span>//Path du dossier de destination où sera enregistré le document pdf généré.</span><br>
$dest=__DIR__.DIRECTORY_SEPARATOR."uploads"; 
 
$pdf->setDestinationFolder($dest); </span>//set dossier de destination </span><br>

$pdf->setData("populationTotale","1.000.000"); //Set data à remplacer dans le document word en key-value pair<br>
$pdf->setData("province","Katanga");

//Créer un nouveau block<br>
$block1=$pdf->newBlock("table");
$block1->setData("culture","Tenday"); //Set data à remplacer dans le block<br>
$block1->setData("nbr",120);
$block1->setData("superficie",300000);
$block1->setData("production",200);

$block2=$pdf->newBlock("table");
$block2->setData("culture","Chris");
$block2->setData("nbr",500);
$block2->setData("superficie",240000);
$block2->setData("production",400);

$block3=$pdf->newBlock("table");
$block3->setData("culture","Mesa");

//Générer le document pdf<br>
$pdf->generate();
