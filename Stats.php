<?php

/**
 * Class Stats provides methods for statistical calculations.
 *
 * @author Suttipong Mungkala <suttipong.mungkala add gmail.com>
 *
 */

class Stats {

  /**
   * Compute the mean, a calculated "central" value of a set of numbers.
   *
   * @param array $x List of numbers.
   * @param string $type The mean type [arithmetic|geometric|harmonic], default is arithmetic.
   *
   * @return float Mean
   * @link [ttp://en.wikipedia.org/wiki/Mean] Mean reference.
   */
  function mean($x, $type="arithmetic") {
    $type = strtolower(trim($type));
    $mean = 0;

    switch ($type) {
      case "arithmetic":
        $total = 0;

        foreach ($x as $v) {
          $total += $v;
        }

        $mean = $total/count($x);
        break;
      case "geometric":
        $total = 1;

        foreach ($x as $v) {
          $total *= $v;
        }

        $mean = pow($total, 1/count($x));
        break;

      case "harmonic":
        $total = 0;

        foreach ($x as $v) {
          $total += 1/$v;
        }

        $mean = count($x)/$total;
    }

    return $mean;
  }

  /**
   * Compute the mode, the number which appears most often in a set of numbers.
   *
   * @param array $x List of numbers.
   * @return array The most frequently occurring number.
   *
   * @link [http://www.mathsisfun.com/definitions/mode.html] Mode reference.
   */
  function mode($x) {
    $c = array();

    foreach ($x as $v) {
      if (isset($c[$v])) {
        $c[$v]++;
      } else {
        $c[$v] = 1;
      }
    }

    return array_keys($c, max($c));
  }

  /**
   * Compute the median, the middle number in a sorted list of numbers.
   * If there are two middle numbers, average them.
   *
   * @param array $x List of numbers
   * @return float The middle number
   *
   * @link [http://www.mathsisfun.com/definitions/median.html] Median reference.
   */
  function median($x) {
    sort($x);
    $c = count($x);
    $mid_val = (int)floor(($c-1)/2); // the middle value or the lowest middle value

    if ($c % 2) { // odd number, the middle value is the median
      $median = $x[$mid_val];
    } else { // even number, calculate the average of two medians
      $median = ($x[$mid_val]+$x[$mid_val+1])/2;
    }

    return $median;
  }

  /**
   * Compute the variance, the average of the squared differences from the Mean.
   * The Variance is a measure of how spread out numbers are.
   *
   * @param array $x List of numbers
   * @return float The variance
   *
   * @link [http://www.mathsisfun.com/definitions/variance.html] Variance reference.
   */
  function variance($x) {
    $mean = $this->mean($x);
    $variance = 0;

    foreach ($x as $v) {
      $variance += ($v-$mean) * ($v-$mean);
    }

    return $variance / (count($x)-1);
  }

  /**
   * Compute the Standard Deviation, the square root of the Variance.
   * The Standard Deviation is a measure of how spread out numbers are.
   *
   * @param array $x The variance of a set of numbers
   * @return float The standard deviation
   *
   * @link [http://www.mathsisfun.com/definitions/standard-deviation.html] Standard Deviation reference.
   */
  function sd($x) {
    return sqrt($this->variance($x));
  }


  /**
   * Perform standardize transformation, variables are commonly standardized to
   * zero mean and unit variance, and this will usually be necessary if they are
   * measured in different units.
   *
   * @param array $x List of number.
   * @param bool $var Standardize variance to be one, default is TRUE.
   * @return array the standardize list of numbers using the same keys.
   *
   * @link [http://en.wikipedia.org/wiki/Feature_scaling#Standardization] Reference.
   */
  function standardize($x, $var=TRUE) {

    foreach ($x as $k=>$v) {
      if($var) {
        $x[$k] = ($v - $this->mean($x)) / $this->sd($x);
      } else {
        $x[$k] = $v - 0.5 * (min($x) + max($x));
      }
    }

    return $x;
  }

  /**
   * Compute the skewness of a distribution.
   * Skewness characterizes the degree of asymmetry of a distribution around its mean.
   *
   * @param array $x List of number.
   * @return float The skewness of a distribution
   */
  function skew($x) {
    $n = count($x);
    $skew = 0;

    foreach ($x as $v) {
      $skew += pow(($v - $this->mean($x)) / $this->sd($x), 3);
    }

    $skew = ($skew*$n) / (($n - 1) * ($n - 2));

    return $skew;
  }

  /**
   * @param array $x List of number
   * @return float The Kurtosis of a distribution
   */
  function kurt($x) {
    $n = count($x);
    $kurt = 0;

    foreach ($x as $value) {
      $kurt += pow(($value - $this->mean($x)) / $this->sd($x), 4);
    }

    $kurt = ($kurt * $n * ($n + 1)) / (($n - 1) * ($n - 2) * ($n - 3));
    $kurt = $kurt - ((3 * ($n - 1) * ($n - 1)) / (($n - 2) * ($n - 3)));

    return $kurt;

  }

}

