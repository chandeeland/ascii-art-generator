<?php

Class Ascii {
    
    /**
     * Yiq Formula used by b&w televisions to help
     * us convert to greyscale.
     *
     * 0 = black
     * 255 = white
     */
    protected function yiq($r,$g,$b) {
        return (($r*0.299)+($g*0.587)+($b*0.114));
    //    return (($r + $g + $b) /3);
    }
    
    /**
     * Examine a 4x4 pixel area of image to determine
     * which ascii char is the most appropriate.
     *
     * @param $x int horizontal location
     * @param $y int vertical location
     * @param $image object image
     */
    protected function findchar($sample, $sample_mean, $mean, $sigma) {
     
        switch ($sample) {
            case '1111':
                if ($sample_mean <= $mean - ($sigma * 1) || $sample_mean <= 0) {
                    return '@';
                } else if ($sample_mean <= $mean - ( .3 * $sigma)) {
                    return 'O';
                } else if ($sample_mean <= $mean ) {
                    return 'o';
                }
                break;
            case '0000':
                if ($sample_mean >= $mean + ($sigma * 1 ) || $sample_mean >= 255) {
                    return ' ';
                } else if ($sample_mean >= $mean + ( .3 * $sigma)) {
                    return '.';
                } else if ($sample_mean >= $mean ) {
                    return '*';
                }
                break;
            case '1000':
            case '0010':
                return '`';
                break;
            case '0100':
            case '0001':
                return ',';
                break;
            case '1010':
                return '^';
                break;
            case '1100':
                return '[';
                break;
            case '0011':
                return ']';
                break;
            case '0101':
                return '=';
                break;
            case '1001':
            case '1011':
            case '1101':
                return '\\';
                break;
            case '0110':
            case '0111':
            case '1110':
                return '/';
                break;
            }
    }
    
    
    /**
     * Loop thru an image and creates an ascii art string
     *
     * Best results with high contrast images.
     * 
     * @param $filename string filesname of image
     * @return string 
     */
    public function render($image) {
        $result = '';
        $width = imagesx($image);
        $height = imagesy($image);
        // $image = imagecreatefromjpeg($filename);
    
        $sum = 0;
        $count = 0;
        $yiq = array();
        for ($y=0;$y < $height;$y++) {
            for ($x=0;$x < $width;$x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xff;
                $g = ($rgb >> 8) & 0xff;
                $b = $rgb & 0xff;
                
                $offset = $y * $width + $x;
                $yiq[$offset] = $this->yiq($r,$g,$b);
                $sum += $yiq[$offset];
                $count++;
            }
        }
        // avg
        $mean = $sum / $count;
    
        // standard deviation
        $sigma = 0;
        foreach ($yiq as $k => $curr) {
            $sigma += pow($curr - $mean, 2);
        }
    
        $sigma = sqrt($sigma / $count);
               
        // Reads the origonal colors pixel by pixel
        // y compensates a little because ascii is not square.
       $debug_div = 1;
        for ($y=0; $y < ($height/$debug_div) -2; $y+=4) {
            for ($x=0; $x < ($width/$debug_div) -2; $x+=2) {
    
                $sample = '';
                $sample_mean = 0;
                for ($i = $y; $i < $y + 2; $i++) {
                    for ($j = $x; $j < $x + 2; $j++) {
    
                $rgb = imagecolorat($image, $j, $i);
                $r = ($rgb >> 16) & 0xff;
                $g = ($rgb >> 8) & 0xff;
                $b = $rgb & 0xff;
                
                $yyy = $yiq[$i * $width + $j];
                        if ($yyy < $mean) {
                            $sample .= '1';  // black
                        } else {
                            $sample .= '0';  // white
                        }
                        $sample_mean += $yyy;
                    }
                }
                $sample_mean /= 4;
                $result .= $this->findchar($sample, $sample_mean, $mean, $sigma);
            }
            $result .= "\n";
        } 
    
        return $result;
    }

}

