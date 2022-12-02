<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use \ImageCropper;

class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_frontend')]
    public function index(): Response
    {
        $imageCropper = new ImageCropper();
        $imageCropper -> setImageData('digitale-stopp-uhr-stoppuhr.jpeg') -> cropImage();
        $imageCropper -> showInBrowser();
        $imageCropper -> showInConsole();
        
        exit;

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }
}
