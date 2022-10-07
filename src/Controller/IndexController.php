<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class IndexController extends AbstractController
{
    /**
     * @throws TesseractOcrException
     */
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // $image = 'stoppuhr.jpeg';
        $image = 'digitale-stopp-uhr-stoppuhr_2.jpeg';

        $interpreter = (new TesseractOCR($image))->psm(11);

        dd($interpreter->run());
    }
}
