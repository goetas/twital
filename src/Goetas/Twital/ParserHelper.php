<?php
namespace Goetas\Twital;

class ParserHelper
{

    public static function staticSplitExpression($str, $splitrer)
    {
        $str = str_split($str, 1);
        $str[] = " ";
        $str_len = count($str);

        $splitrer = str_split($splitrer, 1);
        $splitrer_len = count($splitrer);

        $parts = array();
        $inApex = false;
        $next = 0;
        $pcount = 0;
        for ($i = 0; $i < $str_len; $i ++) {
            if ($inApex === false && ($i === 0 || $str[$i - 1] !== "\\") && ($str[$i] === "\"" || $str[$i] === "'")) { // ingresso
                $inApex = $str[$i];
            } elseif ($inApex === $str[$i] && $str[$i - 1] !== "\\") { // uscita
                $inApex = false;
            }
            if ($inApex === false && $str[$i] === "(") {
                $pcount ++;
            } elseif ($inApex === false && $str[$i] === ")") {
                $pcount --;
            }
            if ($inApex === false && $pcount === 0 && (array_slice($str, $i, $splitrer_len) == $splitrer || $i == ($str_len - 1))) {
                $val = trim(implode('', array_slice($str, $next, $i - $next)));
                if (strlen($val)) {
                    $parts[] = $val;
                }
                $next = $i + $splitrer_len;
            }
        }
        if ($pcount != 0) {
            throw new Exception("Perentesi non bilanciate nell'espressione '" . implode("", $str) . "'");
        } elseif ($inApex !== false) {
            throw new Exception("Apici non bilanciati nell'espressione '" . implode("", $str) . "'");
        }

        return $parts;
    }

    public static function implodeKeyedDouble($glue, array $array)
    {
        $a = array();
        foreach ($array as $key => $val) {
            $a[] = "$key:[".implode(",", $val)."]";
        }

        return implode($glue, $a);
    }
    public static function implodeKeyed($glue, array $array)
    {
        $a = array();
        foreach ($array as $key => $val) {
            $a[] = "$key:$val";
        }

        return implode($glue, $a);
    }
}
