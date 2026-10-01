<?php

namespace App\Files\Services;

use App\Files\Domain\PdfEngine;
use App\Files\Ports\PdfJoinerPort;
use App\Files\Domain\PdfCollection;
use App\Files\Domain\Pdf;

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
    public function split(Pdf $pdf): PdfCollection
    {
        return new PdfCollection();
    }
}