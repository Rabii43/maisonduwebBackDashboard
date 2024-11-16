<?php

namespace App\Controller\QrCode;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel as ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

trait QrCodeGeneratorController
{
    /**
     * @throws \Exception
     */
    public function generateQrCode(int $id): array
    {
        $writer = new PngWriter();
        $urlUser = $_ENV['APP_URL_FRONT'] . '/user/' . $this->codage($id);
        $qrCode = QrCode::create($urlUser)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(400)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $filePath = $_ENV['APP_URL_QR'] . '/qrImages/logo.jpg';
        $logo = null;
        if (file_exists($filePath)) {
            $logo = Logo::create($filePath)
                ->setResizeToWidth(60);
        } else {
            echo "File does not exist: " . $filePath;
        }
        // Generate the QR code image
        $result = $writer->write($qrCode, $logo);
        // Save the image to a file
        $imageName = $this->randomQrCodeId($id) . '.png'; // Set the file name
        $imagePath = $_ENV['APP_URL_QR'] . '/qrImages/' . $imageName; // Set the file path
        file_put_contents($imagePath, $result->getString()); // Save the image

        return ["imageName" => $imageName, "path" => $urlUser];
    }

    public function removeQrCode(string $imageName): void
    {
        $imagePath = $_ENV['APP_URL_QR'] . '/qrImages/' . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    /**
     * @throws \Exception
     */
    public function updateQrCode(int $id): array
    {
        $this->removeQrCode('qr_code' . $id . '.png');
        return $this->generateQrCode($id);
    }

    public function getQrCode(int $id): string
    {
        return $_ENV['APP_URL_QR'] . '/qrImages/qr_code' . $id . '.png';
    }

    public function randomQrCodeId(int $id): string
    {
        // Generate a random string
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $id; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function codage($id): string
    {
        return base64_encode($id);

    }

    public function unCodage(string $string): string
    {
        return base64_decode($string);
    }
}