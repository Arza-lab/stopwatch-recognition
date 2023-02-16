<?php

namespace App\Controller;

use App\Form\ImageType;
use App\Service\ImageCropperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_frontend')]
    public function index(ImageCropperService $imageCropperService): Response
    {
        // $imageCropperService->processImage('digitale-stopp-uhr-stoppuhr.jpeg');

        $form = $this->createForm(ImageType::class, null,
            [
                'action' => $this->generateUrl('app_image_index'),
            ]
        );

        return $this->render('frontend/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
