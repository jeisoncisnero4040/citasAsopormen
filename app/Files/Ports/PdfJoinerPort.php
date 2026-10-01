<?php

namespace App\Files\Ports;
use App\Files\Domain\PdfCollection;
use App\Files\Domain\Pdf;

interface PdfJoinerPort
{
    /**
     * Une múltiples PDFs.
     *
     * @param PdfCollection 
     * @return Pdf 
     */
    public function join(PdfCollection $pdfCollection): Pdf;

    /**
     * Divide un PDF en múltiples PDFs.
     *
     * @param Pdf $pdf PDF a dividir
     * @return PdfCollection Colección de PDFs resultantes
     */
    public function split(Pdf $pdf): PdfCollection;
}