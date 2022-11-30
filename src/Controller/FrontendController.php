<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_frontend')]
    public function index(): Response
    {
        $image = imageCreateFromPng('Unbenannt.png');
  
        $width = imagesx($image);
        $height = imagesy($image);
        $arr = array();
      
        $index = 0;
        $text = "";
        for($x = 0; $x < $width; $x++) {
            for($y = 0; $y < $height; $y++) {
                // pixel color at (x, y)
                $color = imagecolorat($image, $x, $y);
                $col = imagecolorsforindex($image,$color);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;
                array_push($arr, "$r, $g, $b");
                // print_r($col);
                $text .= "$x / $y : $col[red], $col[green], $col[blue], $col[alpha] </br>";
                $index = $index + 1;
            }
        }
        $a = "<span style='font-size: 5px;'>$text</span>";
        
        $b = "<span style='flex: 1'><img src='Unbenannt.png' style='width: 100%;'></img></span>";

        echo "<div style='display:flex;'>$a $b</div>";
        exit;

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }

}
