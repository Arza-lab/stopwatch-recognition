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
        
        $image = imageCreateFromJpeg('digitale-stopp-uhr-stoppuhr.jpeg');
        $width = imagesx($image);
        $height = imagesy($image);

        $arr = array();
        $text = "";

        $rmax = 148;
        $rmin = 12;
        $gmax = 166;
        $gmin = 55;
        $bmax = 157;
        $bmin = 33;

        for($x = 0; $x < $width; $x++) {
            for($y = 0; $y < $height; $y++) {
                $color = imagecolorat($image, $x, $y);
                $col = imagecolorsforindex($image,$color);
                $r = $col["red"];
                $g = $col["green"];
                $b = $col["blue"];


                if ((($rmin <= $r) && ($r <= $rmax)) && (($gmin <= $g) && ($g <= $gmax)) && (($bmin <= $b) && ($b <= $bmax)) && (($r < $g) && ($b <= $g))){
                    array_push($arr, "$x/$y");
                }
                else {
                    $red = imagecolorallocate($image, 0, 0, 0); 
                }
            }
        }
    
        $checkedArr = array_filter($arr, function($value) use ($arr) {
            $val = explode("/", $value,2);
            $x = $val[0];
            $y = $val[1];
            $valuesToCheck = array(($x + 1)."/".($y),($x + 2)."/".($y),($x)."/".($y+1),($x)."/".($y+2),($x + 1)."/".($y+1),($x + 2)."/".($y+2),
                             ($x - 1)."/".($y),($x - 2)."/".($y),($x)."/".($y-1),($x)."/".($y-2),($x - 1)."/".($y-1),($x - 2)."/".($y-2));


            $checks = [];
            foreach ($valuesToCheck as $checkvalue) {
                if (in_array($checkvalue, $arr)){
                    array_push($checks, $checkvalue);
                }            
                if (count($checks) >= 3) {
                    break;
                }
            }

            return count($checks) >= 3;    
        });

        $minx = $width;
        $maxx = 0;

        $miny = $height;
        $maxy = 0;

        foreach ($checkedArr as $value) {
            $val = explode("/", $value,2);
            //dd($val);
            $x = $val[0];
            $y = $val[1];

            //dd($arr,$x,$y);
            if ($x < $minx){
                $minx = $x;
            }
            if ($x > $maxx){
                $maxx = $x;
            }
            if ($y < $miny){
                $miny = $y;
            }
            if ($y > $maxy){
                $maxy = $y;
            }
        }

        $im2 = imageCrop($image, ['x' => $minx, 'y' => $miny, 'width' => ($maxx - $minx) + 5, 'height' =>  ($maxy - $miny) + 10]);
        imageJpeg($im2,'digitale-stopp-uhr-stoppuhr_copy.jpeg');

        $a = "<span style='font-size: 15px;'>$text</span>";
        $b = "<span style='flex: 1'><img src='digitale-stopp-uhr-stoppuhr_copy.jpeg' style='width: 100%;'></img></span>";
        $c = "<span style='flex: 1'><img src='digitale-stopp-uhr-stoppuhr.jpeg' style='width: 100%;'></img></span>";

        echo "<div style='display:flex;'>$a $b $c</div>";

        dd($arr,['x' => $minx, 'y' => $miny,'maxx' => $maxx, 'maxy' => $maxy, 'width' => $maxx - $minx, 'height' =>  $maxy - $miny]);


        imageDestroy($im2);
        imageDestroy($image);
        exit;

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }

}