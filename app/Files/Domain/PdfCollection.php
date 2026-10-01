<?php

namespace App\Files\Domain;
use App\Files\Domain\Pdf;
class PdfCollection
{
    /** 
     * @param Pdf[] $pdfs
     */
    public function __construct(
        private array $pdfs = []
    )
    { }

    /**
     * @return Pdf[]
     */
    public function getPdfs(): array
    {
        return $this->pdfs;
    }

    public function addPdf(Pdf $pdf): void
    {
        $this->pdfs[] = $pdf;
    }
    
}