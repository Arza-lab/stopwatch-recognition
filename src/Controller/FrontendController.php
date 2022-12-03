<?php

namespace App\Controller;

use App\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_frontend')]
    public function index(ImageCropper $imageCropper): Response
    {
        $imageCropper->setImageData('digitale-stopp-uhr-stoppuhr.jpeg')->cropImage();
        $imageCropper->showInBrowser();
        $imageCropper->showInConsole();

        exit;

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }

    #[Route('/form', name: 'app_frontend_form')]
    public function form(): Response
    {
        $form = $this->createForm(ImageType::class);

        return $this->render('frontend/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
