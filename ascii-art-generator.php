<?php

/**
 * Yiq Formula used by b&w televisions to help
 * us convert to greyscale.
 */
function yiq($r,$g,$b) {
    return (($r*0.299)+($g*0.587)+($b*0.114));
}


/**
 * Examine a 4x4 pixel area of image to determine
 * which ascii char is the most appropriate.
 *
 * @param $x int horizontal location
 * @param $y int vertical location
 * @param $image object image
 */
function findchar($x,$y,$image) {
    $result = '';
    
    /**
     * the evaluation loop converts the colors to greyscale.
     * $result one stores the orientation of dark pixels
     */
    for ($i = $x; $i < $x + 2; $i++) {
        for ($j = $y; $j < $y + 2; $j++) {
            $rgb = imagecolorat($image,$i,$j);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // greyscale goes to 256, 128 is half, but still too dark.
            $black = (yiq($r,$g,$b) < 126); 
            $result .= $black ? 1 : 0;
        }
    }

    switch ($result) {
        case '1111':
            return 'X';
            break;
        case '0000':
        case '1000':
        case '0100':
        case '0010':
        case '0001':
            return ' ';
            break;
        case '1010':
            return '^';
            break;
        case '1100':
            return '(';
            break;
        case '0011':
            return ')';
            break;
        case '0101':
            return '_';
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
function asciiart($filename) {
    $result = '';
    list($width, $height) = getimagesize($filename);
    $image = imagecreatefrompng($filename);
    // $image = imagecreatefromjpeg($filename);

    // Reads the origonal colors pixel by pixel
    // y compensates a little because ascii is not square.
    for ($y=0;$y < $height;$y+=3) {
        for ($x=0;$x < $width;$x+=2) {
            $result .= findchar($x,$y,$image);
        }
        $result .= "\n";
    } 

    return $result;
}

echo asciiart('Lenna.png');
?>
