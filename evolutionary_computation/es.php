<?php

const N = 5;  // number of dimensions
const OFFSPRING = 20; // number of offspring

$something = 0.5; 
$LR_overall=$something * 1/sqrt(2*N);
$LR_coordinate=$something * 1/sqrt((2*sqrt(N)));

$p = array (
        'problem' => 0,
        'strategy' => 1,
        'fitness' => 0
    );

$offspring = $p;

/*
 * 1. Initialize parents and evaluate them
 */

// Initialize parents's chromosomes
// randomly create x, where -5<=x<=10, init sigma = 1;
$parent = init_problem_param($p);

// evaluate - Uncorrelated mutation with n σ’
$B = N(0,$LR_overall);
for ($i=0; $i<N; $i++) {
    $parent[0][strategy][$i] = $parent[0][strategy][$i] * exp( $B + N(0,$LR_coordinate) );
    $parent[0][problem][$i] = $parent[0][problem][$i] + N(0,$parent[0][strategy][$i]);
}

// compute the fitness
$parent[0][fitness] = get_fitness($parent[0][problem]);

/*
 *  2. Create some offspring by perturbing parents with Gaussian noise 
 *  according to parent’s mutation parameters
 */

$run=0;
$parent_new[0] = $parent[0];
while($run<3000) {
$run++;

$parent[0] = $parent_new[0];

for ($o=0; $o<OFFSPRING; $o++) {
    $B = N(0,$LR_overall);
    for ($i=0; $i<N; $i++) {
        $offspring[$o][strategy][$i] = $parent[0][strategy][$i] * exp( $B + N(0,$LR_coordinate) );
        $offspring[$o][problem][$i] = $parent[0][problem][$i] + N(0,$offspring[$o][strategy][$i]);
    }
}

// compute the fitness
for($o=0; $o<OFFSPRING; $o++)
    $offspring[$o][fitness] = get_fitness ($offspring[$o][problem]);

/*
 * 3. Evaluate offspring
 */

// find the minimum fitness in offspring and parent
$pos = get_fitness_min_pos($offspring); 
if ($offspring[$pos][fitness] < $parent[0][fitness])
    $parent_new[0] = $offspring[$pos];
else
    $parent_new[0] = $parent[0];

display_normal($parent_new, $run);
//display_debug($parent, $offspring);

} //end while

function display_normal($parent, $run){
    
    //echo "run: $run fitness: ";
    echo round($parent[0][fitness],4)."\n";
    
}

function display_debug($parent, $offspring){
    
    // display
echo "<table border=1 cellpadding=8>";
echo "<tr><td>#</td><td colspan=".N.">Problem param: ".N."</td><td colspan=".N.">Strategy param</td><td>Fitness</td></tr>";

// parent
echo "<tr><td>parent 0</td>";
for ($i=0; $i<N; $i++)
    echo "<td>".round($parent[0][problem][$i],4)."</td>";

for ($i=0; $i<N; $i++)
    echo "<td>".round($parent[0][strategy][$i],4)."</td>";
echo "<td>".$parent[0][fitness]."</td></tr>";

// offspring
for ($o=0; $o<OFFSPRING; $o++) {
    echo "<tr><td>offspring $o</td>";
    for ($i=0; $i<N; $i++)
        echo "<td>".round($offspring[$o][problem][$i],4)."</td>"; 
    for ($i=0; $i<N; $i++)
        echo "<td>".round($offspring[$o][strategy][$i],4)."</td>";
    echo "<td>".$offspring[$o][fitness]."</td></tr>";
}

echo "</table></br>";
    
}

function get_fitness_min_pos($arr) {
    
    // assign values
    for ($i=0;$i<OFFSPRING;$i++)
        $fitness[$i] = $arr[$i][fitness];
    
    $min = min($fitness);
    for ($i=0;$i<OFFSPRING;$i++)
        if ($fitness[$i] == $min)  $min_pos = $i;
    
    return $min_pos;
    
}

// Rosenbrock function
function get_fitness($problem_param) {
    
    $fitness = 0;
   
    for ($i=0; $i<N-1; $i++) {
        $x = $problem_param[$i];
        $x_power2 = pow($x,2);
        $x_next = $problem_param[$i+1];
        $fitness = $fitness + ( pow(1.0-$x,2) + 100.0*pow(($x_next-$x_power2),2) );
    }

    return $fitness;
    
}

// create a random chomosomes
function init_problem_param($p){

        for ($i=0; $i<N; $i++) {
            $p[0][problem][$i] = 1.0 * rand(-5,10);
            $p[0][strategy][$i] = 1.0;
        }
    
    return $p;
}

function gauss() { // N(0,1)
    // returns random number with normal distribution:
    // mean=0
    // std dev=1

    // auxilary vars
    $x=random_0_1();
    $y=random_0_1();

    // two independent variables with normal distribution N(0,1)
    $u=sqrt(-2*log($x))*cos(2*pi()*$y);

    // i will return only one, couse only one needed
return $u;

}

function N($m=0.0,$s=1.0) { 
    //// N(m,s)
    // returns random number with normal distribution:
    // mean=m
    // std dev=s

    return gauss()*$s+$m;

}

function random_0_1() { 
//// auxiliary function
// returns random number with flat distribution from 0 to 1
    return (float)rand()/(float)getrandmax();
}

?>

