<?php

class Regression extends Stats {

  /**
   * Compute the simple linear regression fits a linear model to represent
   * the relationship between a response (or y-) variate, and an explanatory
   * (or x-) variate.
   *
   * @param array   $y      List of float values of the response (or y-) variate.
   * @param array   $x1     List of float values of the first explanatory (or x1) variate.
   * @param array   $x2     List of float values of the second explanatory (or x2) variate (default is null).
   * @param boolean $origin If TRUE then Intercept value set to 0 (default is FALSE)
   *
   * @return array Returns [intercept], [slope], [r-square], [adj-r-square] as float
   *               values in addition to standard error of regression model parameters
   *               [intercept-se] and [slope-se] as well as confidence intervals
   *               at level 95% [intercept-2.5%], [intercept-97.5%], [slope-2.5%],
   *               and [slope-97.5%]
   */
  function lm($y, $x1, $x2=null, $origin=false) {
    if (is_null($x2)) {
      $multiple = FALSE;
      $k = 1;
    } else {
      $multiple = TRUE;
      $k = 2;
    }

    $n = count($y);

    $mx1 = $this->mean($x1);
    $my  = $this->mean($y);

    if (!$multiple) {
      $nominator   = 0;
      $denominator = 0;

      $x1_2 = 0;
      $y2   = 0;
      $x1y  = 0;

      for ($i=0; $i<$n; $i++) {
        $nominator   += ($x1[$i] - $mx1) * ($y[$i] - $my);
        $denominator += ($x1[$i] - $mx1) * ($x1[$i] - $mx1);

        $x1_2 += $x1[$i] * $x1[$i];
        $y2   += $y[$i]  * $y[$i];
        $x1y  += $x1[$i] * $y[$i];
      }

      if ($origin) {
        $b  = $x1y / $x1_2;
        $a  = 0;
        $df = $n - 1;
      } else {
        $b  = $nominator / $denominator;
        $a  = $my - $b * $mx1;
        $df = $n - 2;
      }
      $reg_df = 1;
    } else {
      $mx2 = $this->mean($x2);

      $ysum  = array_sum($y);
      $x1sum = array_sum($x1);
      $x2sum = array_sum($x2);

      $x1_2  = 0;
      $x2_2  = 0;
      $x1x2  = 0;
      $x1y   = 0;
      $x2y   = 0;

      for ($i=0; $i<$n; $i++) {
        $x1_2 += $x1[$i] * $x1[$i];
        $x2_2 += $x2[$i] * $x2[$i];
        $x1x2 += $x1[$i] * $x2[$i];
        $x1y  += $x1[$i] * $y[$i];
        $x2y  += $x2[$i] * $y[$i];
      }

      $mx1_2 = $x1_2 - ($x1sum * $x1sum / $n);
      $mx2_2 = $x2_2 - ($x2sum * $x2sum / $n);
      $mx1x2 = $x1x2 - ($x1sum * $x2sum / $n);
      $mx1y  = $x1y - ($ysum * $x1sum / $n);
      $mx2y  = $x2y - ($ysum * $x2sum / $n);

      $b1n = ($mx2_2 * $mx1y) - ($mx1x2 * $mx2y);
      $b1d = ($mx1_2 * $mx2_2) - ($mx1x2 * $mx1x2);
      $b1  = $b1n / $b1d;

      $b2n = ($mx1_2 * $mx2y) - ($mx1x2 * $mx1y);
      $b2d = ($mx1_2 * $mx2_2) - ($mx1x2 * $mx1x2);
      $b2  = $b2n / $b2d;

      $a = $my - ($b1 * $mx1) - ($b2 * $mx2);

      $df     = $n - 3;
      $reg_df = 2;
    }

    // Total sum of squares (ss) and Residual sum of squares (rss)
    $total_ss      = 0;
    $regression_ss = 0;
    $residual_ss   = 0;

    for ($i=0; $i<$n; $i++) {
      if ($multiple) {
        $est = $a + ($b1 * $x1[$i]) + ($b2 * $x2[$i]);
      } else {
        $est = $a + ($b * $x1[$i]);
      }

      $total_ss    += pow($y[$i] - $my, 2);
      $residual_ss += ($y[$i] - $est) * ($y[$i] - $est);

      if ($origin) {
        $regression_ss += $est * $est;
      } else {
        $regression_ss += ($est - $my) * ($est - $my);
      }
    }

    // R-square value and Standard error of regression intercept and slope
    if (!$multiple) {
      if ($origin) {
        $r2  = $regression_ss / $y2;

        $ase = 0;
        $bse = sqrt($residual_ss/$df) / sqrt($x1_2);
      } else {
        $r2 = 1 - ($residual_ss/$total_ss);

        $ase = sqrt($residual_ss/$df) * sqrt($x1_2/($n*$denominator));
        $bse = sqrt($residual_ss/$df) / sqrt($denominator);
      }
    } else {
      $r2 = 1 - ($residual_ss/$total_ss);

      $ase  = 0;
      $b1se = 0;
      $b2se = 0;
    }

    // Significance of regression
    $regression_ms = $regression_ss / $reg_df;
    $residual_ms   = $residual_ss / $df;

    $regression_f  = $regression_ms / $residual_ms;
    $regression_p  = $this->fDist($regression_f, $reg_df, $df);

    // Output
    if (!$multiple) {
      $result = array('intercept'=>$a, 'slope'=>$b);
    } else {
      $result = array('intercept'=>$a, 'b1'=>$b1, 'b2'=>$b2);
    }

    $residual_ms = $residual_ss / ($n - $k - 1);
    $total_ms    = $total_ss / ($n - 1);

    $result['r-square']     = $r2;
    $result['adj-r-square'] = 1 - ($residual_ms / $total_ms);

    $result['intercept-se']    = $ase;
    $result['intercept-2.5%']  = $a - $this->inverseTCDF(0.05, $df) * $ase;
    $result['intercept-97.5%'] = $a + $this->inverseTCDF(0.05, $df) * $ase;

    if (!$multiple) {
      $result['slope-se']    = $bse;
      $result['slope-2.5%']  = $b - $this->inverseTCDF(0.05, $df) * $bse;
      $result['slope-97.5%'] = $b + $this->inverseTCDF(0.05, $df) * $bse;
    } else {
      $result['b1-se']    = $b1se;
      $result['b1-2.5%']  = $b1 - $this->inverseTCDF(0.05, $df) * $b1se;
      $result['b1-97.5%'] = $b1 + $this->inverseTCDF(0.05, $df) * $b1se;

      $result['b2-se']    = $b2se;
      $result['b2-2.5%']  = $b2 - $this->inverseTCDF(0.05, $df) * $b2se;
      $result['b2-97.5%'] = $b2 + $this->inverseTCDF(0.05, $df) * $b2se;
    }

    $result['F-statistic'] = $regression_f;
    $result['p-value']     = $regression_p;

    return $result;
  }

  /**
   * Returns the F probability distribution. You can use this function to determine
   * whether two data sets have different degrees of diversity.
   *
   * @param float   $f   Is the value at which to evaluate the function.
   * @param integer $df1 Is the numerator degrees of freedom.
   * @param integer $df2 Is the denominator degrees of freedom.
   *
   * @return float the F probability distribution
   */
  function fDist ($f, $df1, $df2) {
    $pj2 = pi() / 2;

    $x = $df2 / ($df1 * $f + $df2);

    if (($df1 % 2) == 0) {
      return $this->_zip(1 - $x, $df2, $df1 + $df2 - 4, $df2 - 2) * pow($x, $df2 / 2);
    }

    if (($df2 % 2) == 0) {
      return 1 - $this->_zip($x, $df1, $df1 + $df2 - 4, $df1 - 2) * pow(1 - $x, $df1 / 2);
    }

    $tan = atan(sqrt($df1 * $f / $df2));
    $a   = $tan / $pj2;
    $sat = sin($tan);
    $cot = cos($tan);

    if ($df2 > 1) {
      $a = $a + $sat * $cot * $this->_zip($cot * $cot, 2, $df2 - 3, -1) / $pj2;
    }

    if ($df1 == 1) {
      return 1 - $a;
    }

    $c = 4 * $this->_zip($sat * $sat, $df2 + 1, $df1 + $df2 - 4, $df2 - 2) * $sat * pow($cot, $df2) / pi();

    if ($df2 == 1) {
      return 1 - $a + $c / 2;
    }

    $k = 2;

    while ($k <= ($df2 - 1) / 2) {
      $c *= $k / ($k - 0.5);
      $k++;
    }

    return 1 - $a + $c;
  }

  function _zip ($q, $i, $j, $b) {
    $zz = 1;
    $z  = $zz;
    $k  = $i;

    while ($k <= $j) {
      $zz *= $q * $k / ($k - $b);
      $z  += $zz;
      $k  += 2;
    }

    return $z;
  }

  /**
   * Returns the t-value of the Student's t-distribution as a function of
   * the probability and the degrees of freedom.
   *
   * @param float   $p Is the probability associated with the two-tailed Student's t-distribution between 0 and 1.
   * @param integer $n Is the number of degrees of freedom with which to characterize the distribution.
   *
   * @return float t-value of the Student's t-distribution for the terms above (i.e. $p and $n).
   */
  function inverseTCDF($p, $n)
  {
    if ($n == 1) {
      $p     *= M_PI_2;
      $result = cos($p) / sin($p);
    } else {
      $a = 1 / ($n - 0.5);
      $b = 48 / ($a * $a);
      $c = ((20700 * $a / $b - 98) * $a - 16) * $a + 96.36;
      $d = ((94.5 / ($b + $c) - 3) / $b + 1) * sqrt($a * M_PI_2) * $n;
      $y = pow(2 * $d * $p, 2 / $n);

      if ($y > (0.05 + $a)) {
        /* asymptotic inverse expansion about the normal */
        $x = $this->inverseNormCDF($p * 0.5);
        $y = $x * $x;

        if ($n < 5) {
          $c += 0.3 * ($n - 4.5) * ($x + 0.6);
        }

        $c  = (((0.05 * $d * $x - 5) * $x - 7) * $x - 2) * $x + $b + $c;
        $y  = (((((0.4 * $y + 6.3) * $y + 36) * $y + 94.5) / $c - $y - 3) / $b + 1) * $x;
        $y *= $a * $y;

        if ($y > 0.002) {
          $y = exp($y) - 1;
        } else {
          $y += 0.5 * $y * $y;
        }
      } else {
        $y = ((1 / ((($n + 6) / ($n * $y) - 0.089 * $d - 0.822) * ($n + 2) * 3) + 0.5 / ($n + 4)) * $y - 1) * ($n + 1) / ($n + 2) + 1 / $y;
      }

      $result = sqrt($n * $y);
    }

    return $result;
  }

  /**
   * Returns the inverse of the standard normal cumulative distribution.
   * The distribution has a mean of zero and a standard deviation of one.
   * This is an implementation of the algorithm published at:
   * http://home.online.no/~pjacklam/notes/invnorm/
   *
   * @param float $p Is a probability corresponding to the normal distribution between 0 and 1.
   *
   * @return float Inverse of the standard normal cumulative distribution, with a probability of $p
   */
  function inverseNormCDF($p)
  {
    /* coefficients for the rational approximants for the normal probit: */
    $a1	= -3.969683028665376e+01;
    $a2	=  2.209460984245205e+02;
    $a3	= -2.759285104469687e+02;
    $a4	=  1.383577518672690e+02;
    $a5	= -3.066479806614716e+01;
    $a6	=  2.506628277459239e+00;
    $b1	= -5.447609879822406e+01;
    $b2	=  1.615858368580409e+02;
    $b3	= -1.556989798598866e+02;
    $b4	=  6.680131188771972e+01;
    $b5	= -1.328068155288572e+01;
    $c1	= -7.784894002430293e-03;
    $c2	= -3.223964580411365e-01;
    $c3	= -2.400758277161838e+00;
    $c4	= -2.549732539343734e+00;
    $c5	=  4.374664141464968e+00;
    $c6	=  2.938163982698783e+00;
    $d1	=  7.784695709041462e-03;
    $d2	=  3.224671290700398e-01;
    $d3	=  2.445134137142996e+00;
    $d4	=  3.754408661907416e+00;

    $p_low  = 0.02425;
    $p_high	= 1.0 - $p_low;

    if (0 < $p && $p < $p_low) {
      /* rational approximation for the lower region */
      $q = sqrt(-2 * log($p));
      $x = ((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
    } elseif ($p_low <= $p && $p <= $p_high) {
      /* rational approximation for the central region */
      $q = $p - 0.5;
      $r = $q * $q;
      $x = ((((($a1 * $r + $a2) * $r + $a3) * $r + $a4) * $r + $a5) * $r + $a6) * $q / ((((($b1 * $r + $b2) * $r + $b3) * $r + $b4) * $r + $b5) * $r + 1);
    } else {
      /* rational approximation for the upper region */
      $q = sqrt(-2 * log(1 - $p));
      $x = -((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
    }

    return $x;
  }


}