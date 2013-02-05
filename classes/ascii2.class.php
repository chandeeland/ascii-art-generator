<?php
require 'ascii.class.php';

Class Ascii2 Extends Ascii {
    
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
            case '111111111':
                if ($sample_mean <= $mean - ($sigma * 1) || $sample_mean <= 0) {
                    return '@';
                } else if ($sample_mean <= $mean - ( .3 * $sigma)) {
                    return 'O';
                } else if ($sample_mean <= $mean ) {
                    return 'o';
                }
                break;
            case '0000000000':
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
                return '.';
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
    
}

