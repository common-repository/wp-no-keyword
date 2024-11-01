<?php

if(!function_exists('bcpow')) {
function bcpow($x,$y) {
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        $t=new Math_BigInteger(1);
            while ($_y->compare(new Math_BigInteger(0) )) {
                    $t = $_x->multiply($t, $x);
                    $_y = $_y->subtract(new Math_BigInteger(1));
                }
        return $t->toString();
}
}

if(!function_exists('bcsub')) {
function bcsub($x,$y) {
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        $t=$_x->subtract($_y);
return $t->toString();
}
}
if(!function_exists('bcadd')) {
function bcadd($x,$y) {
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        $t=$_x->add($_y);
return $t->toString();
}
}

if(!function_exists('bcmul')) {
function bcmul($x,$y)
{
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        $t=$_x->multiply($_y);
return $t->toString();
}
}

if(!function_exists('bccomp')) {
function bccomp($x,$y)
{
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        return $_x->compare($_y);
}
}

if(!function_exists('bcmod')) {
function bcmod($x,$y)
{
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        list(,$_result) = $_x->divide($_y);
        return $_result->toString();
}
}
if(!function_exists('bcdiv')) {
function bcdiv($x,$y)
{
        $_x = new Math_BigInteger($x);
        $_y = new Math_BigInteger($y);
        list($_result,) = $_x->divide($_y);
        return $_result->toString();
}
}



#if(!function_exists('str_split')) {
#    function str_split($string,$string_length=1) {
#        if(strlen($string)>$string_length || !$string_length) {
#            do {
#                $c = strlen($string);
#                $parts[] = substr($string,0,$string_length);
#                $string = substr($string,$string_length);
#            } while($string !== false);
#        } else {
#            $parts = array($string);
#        }
#        return $parts;
#    }
#}
?>