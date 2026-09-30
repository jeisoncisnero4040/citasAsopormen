<?php

namespace App\Files\Services;

use App\Files\Domain\JoinerPdfEngine;
use App\Files\Ports\PdfJoinerPort;

class PdfJoinerService implements PdfJoinerPort
{
    public function __construct(
        private JoinerPdfEngine $joinerPdfEngine
    ) {}

    /**
     * @param string[] $pdfContents
     * @return string
     */
    public function join(array $pdfContents): string
    {
        $pdf = $this->joinerPdfEngine->create();

        $tempFiles = [];

        try {

            foreach ($pdfContents as $index => &$content) {

                if (empty($content)) {
                    continue;
                }

                $tempPath = storage_path(
                    'app/temp/' . uniqid("merge_{$index}_", true) . '.pdf'
                );

                if (!is_dir(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0777, true);
                }

                file_put_contents($tempPath, $content);
                unset($content); 

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

            unset($content);

            $output = $pdf->Output('', 'S');
            unset($pdf);
            return $output;

        } finally {

            foreach ($tempFiles as $file) {

                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}