<?php

namespace App\Files\Ports;

use App\Files\Domain\PdfCollection;
use App\Files\Domain\Pdf;
use App\Files\Domain\PdfExplitterIndexsCollection;
use App\Files\Domain\SubRangeIndex;

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
    public function split(Pdf $pdf, PdfExplitterIndexsCollection $config): PdfCollection;

    public function splitBySubRange(Pdf $pdf, SubRangeIndex $subRangeIndex): Pdf;
}