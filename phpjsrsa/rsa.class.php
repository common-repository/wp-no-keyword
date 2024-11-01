<?
/* 
* Rivest/Shamir/Adelman (RSA) compatible functions
* to generate keys and encode/decode 
*
*With a great thanks to:
*Ilya Rudev <www@polar-lights.com>
*Glenn Haecker <ghaecker@idworld.net>
*Segey Semenov <sergei2002@mail.ru>
*Suivan <ssuuii@gmx.net>
*
*Prime-Numbers.org provide small prime numbers list. 
*You can browse all small prime numbers(small than 10,000,000,000) there. 
*There's totally 455042511 prime numbers.
*http://www.prime-numbers.org/
*/

class RSA{
    /*
    * Function for generating keys. Return array where
    * $array[0] -> modulo N
    * $array[1] -> public key E
    * $array[2] -> private key D
    * Public key pair is N and E
    * Private key pair is N and D
    */
    function generate_keys ($p, $q, $show_debug=0){
        $n = bcmul($p, $q);
      
        //m (we need it to calculate D and E) 
        $m = bcmul(bcsub($p, 1), bcsub($q, 1));
      
        // Public key  E 
        $e = $this->findE($m);
      
        // Private key D
        #$d = $this->extend($e,$m);
        $d = $this->findD($e,$m);
      
        $keys = array ($n, $e, $d);

        if ($show_debug) {
                        echo "P = $p<br>Q = $q<br><b>N = $n</b> - modulo<br>M = $m<br><b>E = $e</b> - public key<br><b>D = $d</b> - private key<p>";
        }
        return $keys;
    }
    function findD($e,$m)
    {
        $n = 0;
        while(bccomp(bcmod(bcadd(1,bcmul($n,$m)),$e),0)!=0){
            $n++; 
        }
        $d=bcdiv(bcadd(1,bcmul($n,$m)),$e);
        return $d;

    }
    /* 
    * Standard method of calculating D
    * D = E-1 (mod N)
    * It's presumed D will be found in less then 16 iterations 
    */
    /* 
    * This function return Greatest Common Divisor for $e and $m numbers 
    */
    function GCD($e,$m) {
        $y = $e;
        $x = $m;

        while (bccomp($y, 0) != 0) {
                        // modulus function
            $w = bcsub($x, bcmul($y, bcdiv($x, $y, 0)));;
                        $x = $y;
                        $y = $w;
        }

        return $x;
    }

    /*
    * Calculating E under conditions:
    * GCD(N,E) = 1 and 1<E<N
    */
    function findE($m){
        $e = '3';
        if(bccomp($this->GCD($e, $m), '1') != 0){
            $e = '5';
            $step = '2';

            while(bccomp($this->GCD($e, $m), '1') != 0){
                $e = bcadd($e, $step);

                if($step == '2'){
                    $step = '4';
                }else{
                    $step = '2';
                }
            }
        }

        return $e;
    }

    /*
    * ENCRYPT function returns
    * X = M^E (mod N)
    */
    function encrypt ($m, $e, $n, $s=3) {
        $coded   = '';
        $max     = strlen($m);
        $packets = ceil($max/$s);
        
        for($i=0; $i<$packets; $i++){
            $packet = substr($m, $i*$s, $s);
            $code   = '0';

            for($j=0; $j<$s; $j++){
                $code = bcadd($code, bcmul(ord($packet[$j]), bcpow('256',$j)));
            }

            $code   = bcpowmod($code, $e, $n);
            $coded .= $code.' ';
        }

        return trim($coded);
    }

    /*
    ENCRYPT function returns
    M = X^D (mod N)
    */
    function decrypt ($c, $d, $n) {
        $coded   = split(' ', $c);
        $message = '';
        $max     = count($coded);

        for($i=0; $i<$max; $i++){
            $code = bcpowmod($coded[$i], $d, $n);
            print "<br>$coded[$i] $n $d ,$code<br>";

            while(bccomp($code, '0') != 0){
                $ascii    = bcmod($code, '256');
                $code     = bcdiv($code, '256', 0);
                $message .= chr($ascii);
            }
        }
        return $message;
    }
    
}
?>