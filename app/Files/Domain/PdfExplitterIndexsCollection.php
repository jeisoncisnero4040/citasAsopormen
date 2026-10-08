<?php

namespace App\Files\Domain;

class PdfExplitterIndexsCollection
{
    /** 
     * @param PdfSplitterIndexs[] $pdfSplitterIndexs
     */
    public function __construct(
        private array $pdfSplitterIndexs = []
    )
    { }

    /**
     * @return PdfSplitterIndexs[]
     */
    public function getPdfSplitterIndexs(): array
    {
        return $this->pdfSplitterIndexs;
    }

    public function addPdfSplitterIndexs(PdfSplitterIndexs $pdfSplitterIndexs): void
    {
        $this->pdfSplitterIndexs[] = $pdfSplitterIndexs;
    }
    
    
}