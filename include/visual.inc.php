<?php

function htmlReady ($what, $trim = TRUE, $br = FALSE) {
        if ($trim) $what = trim(htmlentities($what,ENT_QUOTES));
        else $what = htmlentities($what,ENT_QUOTES);
        // workaround zur Darstellung von Zeichen in der Form &#x268F oder &#283;
        $what = preg_replace('/&amp;#(x[0-9a-f]+|[0-9]+);/i', '&#$1;', $what);
        if ($br) $what = preg_replace("/(\n\r|\r\n|\n|\r)/", "<br>", $what); // newline fixen
        return $what;
}

function mila ($titel, $size = 60) {
        if (strlen ($titel) >$size)
               $titel=substr($titel, 0, $size)."... ";
        return $titel;
}

//kill the forbidden characters, shorten filename to 31 Characters
function prepareFilename($filename, $shorten = FALSE) {
        $bad_characters = array (":", chr(92), "/", "\"", ">", "<", "*", "|", "?", " ", "(", ")", "&", "[", "]", "#", chr(36), "'", "*", ";", "^", "`", "{", "}", "|", "~", chr(255));
        $replacements = array ("", "", "", "", "", "", "", "", "", "_", "", "", "+", "", "", "", "", "", "", "-", "", "", "", "", "-", "", "");

        $filename=str_replace($bad_characters, $replacements, $filename);

        if ($filename{0} == ".")
                $filename = substr($filename, 1, strlen($filename));

        if ($shorten) {
                $ext = getFileExtension ($filename);
                $filename = substr(substr($filename, 0, strrpos($filename,$ext)-1), 0, (30 - strlen($ext))).".".$ext;
        }
        return ($filename);
}

/**
 * Splits a string by space characters and returns these words as an array.
 *
 * @param  string       the string to split
 *
 * @return array        the words of the string as array
 */
function words($string) {
  return preg_split('/ /', $string, -1, PREG_SPLIT_NO_EMPTY);
}

?>
