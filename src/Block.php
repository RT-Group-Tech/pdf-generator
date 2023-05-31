<?php


namespace ChrisTenday\PdfGenerate;


use PhpOffice\PhpWord\TemplateProcessor;

class Block
{
    private $blockName;

    private $data=array();

    private TemplateProcessor $processor;

    public function __construct($blockName,TemplateProcessor $processor)
    {
        $this->blockName=$blockName;
        $this->processor=$processor;

    }

    public function setData($key,$val)
    {
        $this->data[$key]=$val;
    }

    public function __clone()
    {
        $this->processor->cloneBlock($this->blockName,1,true,true);
    }

    public function getName()
    {
        return $this->blockName;
    }

    public function getData()
    {
        return $this->data;
    }

}