# php_ml
Provides methods for statistical calculations. php_ml includes the followings:

1. [Basic calculations] (#basic-calculations)
2. [Analysis] (#analysis)
3. [Correlation and Regression] (#correlation-and-regression)
4. [Distributions] (#distributions)
5. [Clustering] (#clustering)
6. [Matrix] (#matrix)

## Basic calculations
* `mean($x, $type="arithmetic")` - Compute the mean, a calculated "central" value of a set of numbers. `$type` The mean type [arithmetic|geometric|harmonic], `default` is arithmetic.
* `mode($x)` - Compute the mode, the number which appears most often in a set of numbers.
* `median($x)` - Compute the median, the middle number in a sorted list of numbers. If there are two middle numbers, average them.
* `variance($x)` - Compute the variance, the average of the squared differences from the Mean. The Variance is a measure of how spread out numbers are.
* `sd($x)` - Compute the Standard Deviation, the square root of the Variance. The Standard Deviation is a measure of how spread out numbers are.
* `standardize($x, $var=TRUE)` - Perform standardize transformation, variables are commonly standardized to zero mean and unit variance, and this will usually be necessary if they are measured in different units. `$var` Standardize variance to be one, default is `TRUE`.
* `skew($x)` - Compute the skewness of a distribution. Skewness characterizes the degree of asymmetry of a distribution around its mean.
* `kurt($x)` - Compute the Kurtosis of a distribution. Kurtosis characterizes the relative peakedness or flatness of a distribution compared with the normal distribution.