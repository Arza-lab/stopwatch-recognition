<?php
namespace App\Controller;

class ImageCropper {
    private $image;
    private int $width;
    private int $height;
    private string $imagePath;
    private string $croppedImagePath;
    private Array $colorRange = array(
        'min' => array('r'=> 12, 'g'=> 55, 'b'=> 33),
        'max' => array('r'=> 148, 'g'=> 166, 'b'=> 157),
    );

    public function setImageData($imagepath){
        $this -> image = imageCreateFromJpeg($imagepath);
        $this -> width = imagesx($this -> image);
        $this -> height = imagesy($this -> image);
        $this -> imagePath = $imagepath;
        $this -> croppedImagePath = 'cropped_'.$imagepath;
        return $this;
    }

    public function cropImage() {

        $pixelColorArray = $this -> getPixelColorArray();
        $cropRectangle = $this -> getCropRectangleArray($pixelColorArray);
        $croppedImage = imageCrop($this -> image, $cropRectangle);

        imageJpeg($croppedImage,$this->croppedImagePath);  
    }

    // Returns an array which is containing all pixels within a given colorrange
    private function getPixelColorArray(){
        $width = imagesx($this -> image);
        $height = imagesy($this -> image);
        
        $arr = array();

        ['min' => $min, 'max' => $max] = $this -> colorRange;

        $rmax = $max['r'];
        $rmin = $min['r'];
        $gmax = $max['g'];
        $gmin = $min['g'];
        $bmax = $max['b'];
        $bmin = $min['b'];

        for($x = 0; $x < $this -> width; $x++) {
            for($y = 0; $y < $this -> height; $y++) {
                $colorIndex = imagecolorat($this -> image, $x, $y);
                $color = imagecolorsforindex($this -> image,$colorIndex);
                $r = $color["red"];
                $g = $color["green"];
                $b = $color["blue"];


                if ((($rmin <= $r) && ($r <= $rmax)) && (($gmin <= $g) && ($g <= $gmax)) && (($bmin <= $b) && ($b <= $bmax)) && (($r < $g) && ($b < $g))){
                    array_push($arr, "$x/$y");
                }
            }
        }

        $arr = $this -> getFilteredPixelColorArray($arr);
        return $arr;
    }

    // Removes separated pixels for more accuracy
    private function getFilteredPixelColorArray($arr){
        $checkedArr = array_filter($arr, function($value) use ($arr) {
            $val = explode("/", $value, 2);
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
        return $checkedArr;
    }

    // Returns an array to crop the image by. [x,y,width,height]
    private function getCropRectangleArray($arr){
        $minx = $this -> width;
        $maxx = 0;

        $miny = $this -> height;
        $maxy = 0;

        foreach ($arr as $value) {
            $val = explode("/", $value,2);
            $x = $val[0];
            $y = $val[1];

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

        return ['x' => $minx, 'y' => $miny, 'width' => ($maxx - $minx) + 5, 'height' =>  ($maxy - $miny) + 10];
    }


    // output to browser for testing purposes
    public function showInBrowser(){
        $originalImage = "<span style='flex: 1'>Original Image: $this->imagePath <img src='$this->imagePath' style='width: 100%;'></img></span>";
        $croppedImage = "<span style='flex: 1'>Cropped Image: $this->croppedImagePath <img src='$this->croppedImagePath' style='width: 100%;'></img></span>";
        echo "<div style='display:flex;'>$originalImage $croppedImage</div>";
    }

    public function showInConsole(){
        //$js_code = 'console.log(' . json_encode(func_get_args(), JSON_HEX_TAG) .');';
        $js_code = 'console.log(' . json_encode(get_object_vars($this), JSON_HEX_TAG) .');';
        $js_code = '<script>' . $js_code . '</script>';
        echo $js_code;
    }
}