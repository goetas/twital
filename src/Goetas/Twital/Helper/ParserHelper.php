<?php
namespace Goetas\Twital\Helper;

use Exception;

class ParserHelper
{

    private static $closing = array(
        '}' => '{',
        ')' => '(',
        ']' => '['
    );

    public static function staticSplitExpression($str, $splitter, $limit = 0)
    {
        $in = array();
        $inApex = false;
        $parts = array();
        $prev = 0;

        for ($i = 0, $l = strlen($str); $i < $l; $i ++) {
            $chr = $str[$i];

            if ($chr == "'" || $chr == '"') {
                $j = 1;
                while ($i>=$j && $str[$i - $j] === '\\') {
                    $j ++;
                }

                if ($j % 2 !== 0) {
                    if (! $inApex) {
                        $inApex = $chr;
                    } elseif ($inApex === $chr) {
                        $inApex = false;
                    }
                }
            }

            if (! $inApex) {
                if (in_array($chr, self::$closing)) {
                    array_push($in, $chr);
                } elseif (isset(self::$closing[$chr]) && self::$closing[$chr] === end($in)) {
                    array_pop($in);
                } elseif (isset(self::$closing[$chr]) && ! count($in)) {
                    throw new Exception(sprintf('Unexpected "%s" next to "%s"', $chr, substr($str, 0, $i + 1)));
                }

                if (! count($in) && $chr === $splitter) {
                    $parts[] = substr($str, $prev, $i - $prev);
                    $prev = $i + 1;
                    if($limit>1 && count($parts)==($limit-1)){
                    	break;
                    }
                }
            }
        }
        if ($inApex) {
            throw new Exception(sprintf('Can\'t find the closing "%s" in "%s" expression', $inApex, $str));
        } elseif (count($in)) {
            throw new Exception(sprintf('Can\'t find the closing braces for "%s" in "%s" expression', implode(',', $in), $str));
        }

        $parts[] = substr($str, $prev);

        return array_map('trim', $parts);
    }

    public static function implodeKeyedDouble($glue, array $array, $quoteKeys = false)
    {
        $a = array();
        foreach ($array as $key => $val) {
            $a[] = ($quoteKeys?"'$key'":$key).":[" . implode(",", $val) . "]";
        }

        return implode($glue, $a);
    }

    public static function implodeKeyed($glue, array $array, $quoteKeys = false)
    {
        $a = array();
        foreach ($array as $key => $val) {
            $a[] = ($quoteKeys?"'$key'":$key).":$val";
        }

        return implode($glue, $a);
    }
}
