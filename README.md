# php_ml
php_ml includes the followings:

1. [Basic Stats] (#basic-calculations)
2. [Analysis] (#analysis)
3. [Correlation] (#correlation)
4. [Regression] (#regression)
5. [Distributions] (#distributions) `to be implemented`
6. [Clustering] (#clustering) `to be implemented`
7. [Matrix] (#matrix) `to be implemented`

## Basic Stats

* `mean($x, $type="arithmetic")` - Compute the mean, a calculated "central" value of a set of numbers. `$type` The mean type [arithmetic|geometric|harmonic], `default` is arithmetic.
* `mode($x)` - Compute the mode, the number which appears most often in a set of numbers.
* `median($x)` - Compute the median, the middle number in a sorted list of numbers. If there are two middle numbers, average them.
* `variance($x)` - Compute the variance, the average of the squared differences from the Mean. The Variance is a measure of how spread out numbers are.
* `sd($x)` - Compute the Standard Deviation, the square root of the Variance. The Standard Deviation is a measure of how spread out numbers are.
* `standardize($x, $var=TRUE)` - Perform standardize transformation, variables are commonly standardized to zero mean and unit variance, and this will usually be necessary if they are measured in different units. `$var` Standardize variance to be one, default is `TRUE`.
* `skew($x)` - Compute the skewness of a distribution. Skewness characterizes the degree of asymmetry of a distribution around its mean.
* `kurt($x)` - Compute the Kurtosis of a distribution. Kurtosis characterizes the relative peakedness or flatness of a distribution compared with the normal distribution.

## Analysis
* `moving_average($x, $window=5)` - The unweighted mean of the previous n data. Simple moving average is available at the moment.
* `t_test($x, $y, $paired=FALSE)` - The statistical hypothesis test in which the test statistic follows a Student's t-distribution.

## Correlation
* `cov($x, $y)` - Compute the covariance, the average of the products of deviations for each data point pair.
* `cor($x, $y)` - Compute the correlation coefficient. Use the correlation coefficient to determine the relationship between two properties.

## Regression
* `lm($y, $x1, $x2=null, $origin=false)` - Compute the simple linear regression fits a linear model to represent the relationship between a response (or y-) variate, and an explanatory (or x-) variate. 
