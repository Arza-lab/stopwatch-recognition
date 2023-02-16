<?php

namespace App\Controller;

use App\Entity\StopwatchRecord;
use App\Repository\StopwatchRecordRepository;
use App\Service\ImageCropperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ImageController extends AbstractController
{
    #[Route('/api/_action/image', name: 'app_image_index', methods: ['POST'])]
    public function index(Request $request, StopwatchRecordRepository $stopwatchRecordRepository): Response
    {
        $files = $request->files;
        /** @var UploadedFile $file */
        $file = $files->get('image')['file'];

        $ocr = $this->getInterpreterWithImage($file->getPathname());

        try {
            $text = $ocr->run();
            if ($text) {
                $record = new StopwatchRecord();
                $record->setTime($text);
                $stopwatchRecordRepository->save($record, true);

                return $this->json([
                    'success' => true,
                    'text' => $text,
                ]);
            }
        } catch (TesseractOcrException $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->json([
            'success' => false,
        ]);
    }

    private function getInterpreter(): TesseractOCR
    {
        return (new TesseractOCR())
            ->psm(11)
            ->lang('lets');
    }

    private function getInterpreterWithImage(string $image): TesseractOCR
    {
        return $this->getInterpreter()->image($image);
    }

    #[Route('/api/_action/image/test', name: 'app_image_test')]
    public function test(ImageCropperService $imageCropperService): void
    {
        $filePath = 'testbilder/20221128_114836_gray.jpg';
        $filePath = $imageCropperService->processImage($filePath);
        // digitale-stopp-uhr-stoppuhr

        $interpreter = $this->getInterpreterWithImage($filePath);
        $text = $interpreter
            ->lang('lets')
            ->psm(11)
            ->digits()
            ->run();

        dd($text);
    }
}
