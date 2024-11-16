<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PdfService
{
    private $domPdf;
    /**
     * @var ParameterBagInterface
     */
    private $dataDirectory;

    public function __construct(ParameterBagInterface $dataDirectory) {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Garamond');
        $this->domPdf->setOptions($pdfOptions);
        $this->dataDirectory = $dataDirectory;
    }

    public function showPdfFile($html,$nameFile) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $output =  $this->domPdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->dataDirectory->get('files') . '/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory .$nameFile.'.pdf';
        $str = strchr($pdfFilepath, "uploads");
        $url = $_SERVER["HTTP_HOST"].'/'.$str;

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Send some text response
        return $url;
    }

    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }

}
