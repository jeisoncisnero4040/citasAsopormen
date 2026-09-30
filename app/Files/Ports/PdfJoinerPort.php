<?php

namespace App\Files\Ports;

interface PdfJoinerPort
{
    /**
     * Une múltiples PDFs.
     *
     * @param string[] $pdfContents Binarios raw de PDFs
     * @return string Binario raw del PDF final
     */
    public function join(array $pdfContents): string;
}