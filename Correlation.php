<?php

class Correlation extends Stats{

  /**
   * Compute the covariance, the average of the products of deviations for each data point pair.
   *
   * @param array $x First list of numbers
   * @param array $y Second list of numbers
   *
   * @return float The covariance
   */
  function cov($x, $y) {
    $meanX = $this->mean($x);
    $meanY = $this->mean($y);

    $count = count($x);
    $total = 0;

    for ($i=0; $i<$count; $i++) {
      $total += ($x[$i] - $meanX) * ($y[$i] - $meanY);
    }

    $cov = (1 / ($count - 1)) * $total;

    return $cov;
  }

  /**
   * Compute the correlation coefficient. Use the correlation coefficient to determine the
   * relationship between two properties. It uses different measures of association, all
   * in the range [-1, 1] with 0 indicating no association.
   *
   * @param array $x First list of numbers
   * @param array $y Second list of numbers
   *
   * @return float The correlation coefficient
   */
  function cor($x, $y) {
    $cov = $this->cov($x, $y);
    $sdX = $this->sd($x);
    $sdY = $this->sd($y);

    $cor = $cov / ($sdX * $sdY);

    return $cor;
  }

}