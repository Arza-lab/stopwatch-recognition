<?php

namespace App\Controller;

use App\Entity\StopwatchRecord;
use App\Repository\StopwatchRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ImageController extends AbstractController
{
    #[Route('/api/_action/image', name: 'app_image_index', methods: ['POST'])]
    public function index(FileBag $fileBag, ParameterBag $parameterBag, StopwatchRecordRepository $stopwatchRecordRepository): Response
    {
        $file = $fileBag->get('file');
        $lang = $parameterBag->get('lang', 'eng');

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
        return (new TesseractOCR())->psm(11);
    }

    private function getInterpreterWithImage(string $image): TesseractOCR
    {
        return $this->getInterpreter()->image($image);
    }

    #[Route('/api/_action/image/test', name: 'app_image_test')]
    public function test(): void
    {
        $interpreter = $this->getInterpreterWithImage('stoppuhr.jpeg');
        try {
            $interpreter->run();
        } catch (TesseractOcrException $e) {
        }
    }
}
