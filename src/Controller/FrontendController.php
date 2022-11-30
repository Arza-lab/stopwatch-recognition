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

        $imgWidth = 100;
        $imgHeight = 100;
        $gradientImg = imagecreatetruecolor($imgWidth,$imgHeight);

        $this -> image_gradientrect($gradientImg,0,0,$imgWidth,$imgHeight,'94a69d','0c3721');
        imagepng($gradientImg,'gradient.png');


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
        $a = "<span style='font-size: 15px;'>$text</span>";
        
        $b = "<span style='flex: 1'><img src='Unbenannt.png' style='width: 100%;'></img></span>";
        $c = "<span style='flex: 1'><img src='gradient.png' style='width: 100%;'></img></span>";

        echo "<div style='display:flex;'>$a $b $c</div>";
        exit;

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }


    public function image_gradientrect($img,$x,$y,$x1,$y1,$start,$end) {
        if($x > $x1 || $y > $y1) {
           return false;
        }
        $s = array(
           hexdec(substr($start,0,2)),
           hexdec(substr($start,2,2)),
           hexdec(substr($start,4,2))
        );
        $e = array(
           hexdec(substr($end,0,2)),
           hexdec(substr($end,2,2)),
           hexdec(substr($end,4,2))
        );
        $steps = $y1 - $y;
        for($i = 0; $i < $steps; $i++) {
           $r = $s[0] - ((($s[0]-$e[0])/$steps)*$i);
           $g = $s[1] - ((($s[1]-$e[1])/$steps)*$i);
           $b = $s[2] - ((($s[2]-$e[2])/$steps)*$i);
           $color = imagecolorallocate($img,$r,$g,$b);
           imagefilledrectangle($img,$x,$y+$i,$x1,$y+$i+1,$color);
        }
        return true;
     }

}
