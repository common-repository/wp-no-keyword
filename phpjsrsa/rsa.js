function decrypt(s,d,n)
{
  //x = new BigInteger("4382");
  //y = new BigInteger("101");
  //d = new BigInteger("5187");
  //z = x.modPow(y,d);
          var result='';
          var z=s.split(" ");
          _d= new BigInteger(d+"");
          _n= new BigInteger(n+"");
          _bit=new BigInteger("256");

          for (var i in z)
          {
                c=z[i];

                _c = new BigInteger(c+"");
                var a=_c.modPow(_d,_n);
                //var a= BigInteger(c).modPow(BigInteger(d),BigInteger(n));
                //while (BigInteger.divide(a,256)>0)

                while ( a.divide(_bit)>0)
                {
                       //result=result+String.fromCharCode(BigInteger.remainder(a,256));
                       result=result+String.fromCharCode(a.remainder(_bit).toString());
                       //a=BigInteger.divide(a,256);
                       //a=a.divide(a,_bit);
                       a=a.divide(_bit);
                }
                result=result+String.fromCharCode(a.toString()); 
          }
          str = _from_utf8(result);
          return str;
}

function _from_utf8(s) {
  var c, d = "", flag = 0, tmp;
  for (var i = 0; i < s.length; i++) {
    c = s.charCodeAt(i);
    if (flag == 0) {
      if ((c & 0xe0) == 0xe0) {
        flag = 2;
        tmp = (c & 0x0f) << 12;
      } else if ((c & 0xc0) == 0xc0) {
        flag = 1;
        tmp = (c & 0x1f) << 6;
      } else if ((c & 0x80) == 0) {
        d += s.charAt(i);
      } else {
        flag = 0;
      }
    } else if (flag == 1) {
      flag = 0;
      d += String.fromCharCode(tmp | (c & 0x3f));
    } else if (flag == 2) {
      flag = 3;
      tmp |= (c & 0x3f) << 6;
    } else if (flag == 3) {
      flag = 0;
      d += String.fromCharCode(tmp | (c & 0x3f));
    } else {
      flag = 0;
    }
  }
  return d;
}