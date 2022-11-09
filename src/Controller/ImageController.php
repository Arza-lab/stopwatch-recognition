<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ImageController extends AbstractController
{
    /**
     * @throws TesseractOcrException
     */
    #[Route('/api/_action/image', name: 'app_image_index')]
    public function index(): Response
    {
        // $image = 'stoppuhr.jpeg';
        $image = 'digitale-stopp-uhr-stoppuhr_2.jpeg';

        $interpreter = (new TesseractOCR($image))->psm(11);

        dd($interpreter->run());
    }
}
