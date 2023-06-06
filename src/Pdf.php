<?php


namespace ChrisTenday\PdfGenerate;


use Aspose\Words\Model\Requests\ConvertDocumentRequest;
use Aspose\Words\WordsApi;
use PhpOffice\PhpWord\TemplateProcessor;

define("DS",DIRECTORY_SEPARATOR);

class Pdf
{
    private $templateFilePath="";

    private $processor;

    private $destinationFolder;

    private $blocks=array();

    public function __construct($templateFilePath)
    {
        $this->templateFilePath=$templateFilePath;

        $this->processor=new TemplateProcessor($this->templateFilePath);

    }

    /**
     * Spécifier le dossier de destination où sera stocké le pdf généré.
     * @param $path
     */
    public function setDestinationFolder($path)
    {
        if(!is_dir($path))
        {
            /**
             * Créer le dossier.
             */
            mkdir($path);
        }
        $this->destinationFolder=$path;
    }

    /**
     * Entrer une donnée spécifique remplacant une clé définie dans le document template.
     * @param $key
     * @param $data
     */
    public function setData($key,$data)
    {
        $this->processor->setValue($key,$data);
    }

    public function generate()
    {
        //echo "Generate:\n";
        for($i=0; $i<count($this->blocks); $i++)
        {
          //  print_r($this->blocks[$i]->getData());
        }
        $this->buildBlocks();
        return $this->save();
    }


    /**
     * Method pour enregistrer le fichier généré.
     */
    private function save()
    {
        $this->buildSections();
        $l=array("a","T","U","p","d","E","h","f","G","c","B","A");
        shuffle($l);

        $filename=rand(1,9999)."_".$l[0].$l[1].$l[2].$l[3].$l[4];
        $ext=".docx";
        $folder="uploads";
        $path=$this->destinationFolder.DS.$filename.$ext;
        $this->processor->saveAs($path);

        try
        {
            /**
             * Convertir le document word en pdf.
             */
            $pdfDoc=$this->convertToPdf($path);

            $pdfDocDetails=pathinfo($pdfDoc);

            //$url=Request::$websiteDomain."/".$folder."/".$pdfDocDetails['basename'];

            /**
             * Supprimer le document word.
             */
            unlink($path);

            return $pdfDoc;

        }catch (Exception $e)
        {
            return null;
        }

    }

    /**
     * Method pour convertif un document word en pdf.
     * @param $wordDoc
     * @throws \Aspose\Words\ApiException
     */
    private function convertToPdf($wordDoc)
    {

        $fileDetails=pathinfo($wordDoc);

        $clientId="6780fa53-426f-440d-bc77-3789f8111bbf";
        $clientSecret="6b6f21740ce85e051137d8d7cd2b249c";

        $wordApi=new WordsApi($clientId,$clientSecret);

        $pdfDocPath=$fileDetails['dirname'].DS.$fileDetails['filename'].".pdf";

        $convertRequest=new ConvertDocumentRequest($wordDoc,"pdf",null);

        $result=$wordApi->convertDocument($convertRequest);

        copy($result->getPathName(),$pdfDocPath);

        return $pdfDocPath;

    }

    /**
     * Method pour dupliquer les sections dependamment des données.
     */
    private function buildSections()
    {
        $this->processor->cloneBlock('table', 3, true, true);
    }

    /**
     * Method pour remplir les données dans chaque tableau dupliqué
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    private function fillSectionData()
    {
        $i=1;
        foreach($this->sectionsData as $head=>$data)
        {
            $section="section#".$i;
            $this->template->setValue($section,strtoupper($head));
            $d=0;
            $k=1;
            foreach($data as $key=>$val)
            {
                /**
                 * Dupliquer les rows.
                 */
                if($d==0)
                {
                    $this->template->cloneRow("detail#".$i,count($data));
                    $d++;
                }

                $detail="detail#".$i."#".$k;
                $detailData="detailData#".$i."#".$k;

                $this->template->setValue($detail,ucfirst($key));
                $this->template->setValue($detailData,ucfirst($val));
                $k++;
            }

            $i++;
        }
        //print_r($this->sectionsData); exit();
    }

    /**
     * @param $blockName
     * @return Block
     */
    public function newBlock($blockName)
    {
        $block=new Block($blockName,$this->processor);

        $this->blocks[]=$block;

        return $block;
    }

    public function saveBlock(Block $block)
    {
        $this->blocks[]=$block;
    }

    public function buildBlocks()
    {
        $usedBlocks=[];
        $blockName="";
        $blockToFill=array();
        $blockToFill['name']="";
        $blockToFill['data']=array();

        for($i=0; $i<count($this->blocks); $i++)
        {
            $block=$this->blocks[$i];
            $blockName=$block->getName();

            $blockToFill=array();
            $blockToFill['name']=$block->getName();
            $blockToFill['blocks']=array();

            if(in_array($blockToFill['name'],$usedBlocks))
            {
                //continue;
            }

            $cloneBlock=0;
            for($k=0; $k<count($this->blocks); $k++)
            {
                if($blockToFill['name']==$block->getName())
                {
                    $usedBlocks[]=$blockToFill['name'];
                    /**
                     * Count clone.
                     */
                    $cloneBlock++;

                    /**
                     * Save block to be filled in.
                     */
                    $blockToFill['blocks'][]=$block;
                }
            }
            /**
             * Clone block.
             */
            $this->processor->cloneBlock($blockName,$cloneBlock,true,true);

            /**
             * Fill in data.
             */
            $this->fillBlockData($blockToFill['name'],$blockToFill['blocks']);

        }
    }

    /**
     * Method pour remplir les données d'un block.
     * @param $blockName
     * @param array $blocks
     */
    private function fillBlockData($blockName,array $blocks)
    {
        static $fill=1;

        for($i=0; $i<count($blocks); $i++)
        {
            $block=$blocks[$i];

            foreach($block->getData() as $key=>$val)
            {
                $k=$key."#".$fill;
                $this->processor->setValue($k,$val);
            }

        }
        $fill++;

    }

}