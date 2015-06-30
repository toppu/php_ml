<?php

/**
 * Class Analysis provides methods for data analysis.
 *
 * @author Suttipong Mungkala <suttipong.mungkala add gmail.com>
 *
 */

class Analysis extends Stats {

  /**
   * Simple moving average is the unweighted mean of the previous n data. In science and engineering
   * the mean is normally taken from an equal number of data on either side of a central value.
   *
   * @param array $x A list of number
   * @param int $window Number of datum points used to calculate moving average
   *
   * @return array List of float values representing the calculated moving average
   */
  //TODO added more options, check WIKI
  function moving_average($x, $window=5) {
    $n = count($x);
    $y = array();

    $y[$window - 1] = array_sum(array_slice($x, 0, $window)) / $window;

    for ($i=$window; $i<$n; $i++) {
      $y[$i] = $y[$i-1] + ($x[$i] - $x[$i-$window]) / $window;
    }

    $shift = floor($window / 2);

    // simple moving average
    $sma = array();

    for ($i=$window-1; $i<$n; $i++) {
      $sma[$i-$shift] = $y[$i];
    }

    return $sma;
  }

  /**
   * The statistical hypothesis test in which the test statistic follows a Student's t-distribution.
   * It can be used to determine if two sets of data are significantly different from each other,
   * and is most commonly applied when the test statistic would follow a normal distribution
   * if the value of a scaling term in the test statistic were known.
   *
   * @param array $x First list of numbers
   * @param array $y Second list of numbers
   * @param bool $paired Logical indicating whether you want a paired t-test, default value is FALSE for unpaired set of data
   *
   * @return float The associated with a Student's t-Test
   */
  function t_test($x, $y, $paired=FALSE) {
    $n_x = count($x);
    $n_y = count($y);
    $diff = array();

    if ($paired) {

      for ($i=0; $i<$n_x; $i++) {
        $diff[$i] = $x[$i] - $y[$i];
      }

      $t = $this->mean($diff) / sqrt($this->variance($diff) / $n_x);
    } else {
      $mean_x = $this->mean($x);
      $mean_y = $this->mean($y);
      $var_x = $this->variance($x);
      $var_y = $this->variance($y);

      $t = ($mean_x - $mean_y) / sqrt(($var_x / $n_x) +($var_y / $var_y));
    }

    return $t;
  }

}