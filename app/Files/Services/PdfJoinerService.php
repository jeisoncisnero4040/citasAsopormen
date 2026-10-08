<?php

namespace App\Files\Services;

use App\Files\Domain\PdfEngine;
use App\Files\Ports\PdfJoinerPort;
use App\Files\Domain\PdfCollection;
use App\Files\Domain\Pdf;
use App\Files\Domain\PdfExplitterIndexsCollection;
use App\Files\Domain\SubRangeIndex;

class PdfJoinerService implements PdfJoinerPort
{
    public function __construct(
        private PdfEngine $pdfEngine
    ) {}

    /**
     * @param PdfCollection $pdfCollection
     * @return Pdf
     */
    public function join(PdfCollection $pdfCollection): Pdf
    {
        $pdf = $this->pdfEngine->create();

        $tempFiles = [];

        try {

            foreach ($pdfCollection->getPdfs() as $index => $pdfItem) {

                if (empty($pdfItem->getContent())) {
                    continue;
                }

                $tempPath = storage_path(
                    'app/temp/' . uniqid("merge_{$index}_", true) . '.pdf'
                );

                if (!is_dir(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0777, true);
                }

                file_put_contents($tempPath, $pdfItem->getContent());
                unset($pdfItem); 

                $tempFiles[] = $tempPath;

                $pageCount = $pdf->setSourceFile($tempPath);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                    $template = $pdf->importPage($pageNo);

                    $size = $pdf->getTemplateSize($template);

                    $pdf->AddPage(
                        $size['orientation'],
                        [$size['width'], $size['height']]
                    );

                    $pdf->useTemplate($template);
                }
            }

            unset($pdfCollection);

            $output = $pdf->Output('', 'S');
            unset($pdf);
            return new Pdf($output);

        } finally {

            foreach ($tempFiles as $file) {

                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * @param Pdf $pdf
     * @return PdfCollection
     */
    public function split(Pdf $pdf, PdfExplitterIndexsCollection $config): PdfCollection
    {
        $collection = new PdfCollection();
        foreach ($config->getPdfSplitterIndexs() as $index) {
            $subRange = $index->getSubRangeIndexs();
            $splitPdf = $this->splitBySubRange($pdf, $subRange);
            $collection->addPdf($splitPdf);
            
        }
        return $collection;
    }
    public function splitBySubRange(Pdf $pdf, SubRangeIndex $range): Pdf
    {
        $tempPath = storage_path(
            'app/temp/' . uniqid('split_', true) . '.pdf'
        );

        $output = null;

        try {

            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }

            file_put_contents($tempPath, $pdf->getContent());

            $splitPdf = $this->pdfEngine->create();

            $pageCount = $splitPdf->setSourceFile($tempPath);

            $startPage = $range->getFirstPage();
            $endPage = $range->getLastPage();

            if ($startPage < 1 || $endPage < $startPage) {
                throw new \InvalidArgumentException(
                    "Rango de páginas inválido: {$startPage}-{$endPage}"
                );
            }

            if ($startPage > $pageCount) {
                throw new \InvalidArgumentException(
                    "La página inicial {$startPage} no existe. "
                    . "El PDF tiene {$pageCount} páginas."
                );
            }

            $endPage = min($endPage, $pageCount);

            for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {

                $template = $splitPdf->importPage($pageNo);

                $size = $splitPdf->getTemplateSize($template);

                $splitPdf->AddPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $splitPdf->useTemplate($template);
            }

            $output = $splitPdf->Output('', 'S');

            unset($splitPdf);

            return new Pdf($output);

        } finally {

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}