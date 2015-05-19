<?php

const POPULATION_SIZE = 10;
const CHROMOSOME_SIZE = 5;
const MUTATION_RATE = 0.2;
const RUN_ALGORITHM = 10;

// run algorithm 10 times
$max_generarion=0;
$ep=array();
for ($time=1; $time<=RUN_ALGORITHM; $time++) {
    
$person = array (
    0 => array(
        'chromosome' => 0,1,
        'fitness' => 0,
        'prob'  =>  0
        )
    );

$person_new = array (
    0 => array(
        'chromosome' => 0,1,
        'fitness' => 0,
        'prob'  =>  0
        )
    );
/* 
 * 1.) Initialize random population of candidate solutions
 */

// create random chromosome 
for ($i=0; $i<POPULATION_SIZE; $i++) {
    for ($j=0; $j<CHROMOSOME_SIZE; $j++)
        $person[$i][chromosome] = get_chromosome();
}

$run=0;
$optimal=0;
while ($optimal==0) {
    $run++;
    /*
    * 2.) Evaluate solutions on problem and assign a fitness score
    */

    // compute fitness value
    for ($i=0; $i<POPULATION_SIZE; $i++)
        $person[$i][fitness] = get_fitness($person[$i][chromosome]);

    /*
     * 3.) Select some solutions for mating - fitness proportional selection
     */

    // 3.1.)  Calculate a genotype’s probability of being selected in proportion to its fitness pi= fi/∑fj
    for ($i=0; $i<POPULATION_SIZE; $i++)
        $person[$i][prob] = get_prob($person,$person[$i][fitness]);

    // create new population, The selected individual is the first one 
    // whose accumulated normalized value is greater than random number.
    $p=0;
    while ($p < POPULATION_SIZE) {
        $rand = rand(1,POPULATION_SIZE-1)/10;
        $tmp=0; $i=0;
        //echo "rand: $rand";
        for ($i=0; $i<POPULATION_SIZE; $i++) {
            $tmp = $tmp + $person[$i][prob];
            if ($rand < $tmp) {
                $person_new[$p][chromosome] = $person[$i][chromosome];
                $from[$p] = $i;
                $p++; 
                $i=100; // exit for loop
            }
        }
    }

    // 3.2.) Then select some number of genotypes for mating according to probabilities pi
    // 1-point crossover
    $parents = get_parents($person_new);
    $person_new = crossover($person_new, $parents); 

    // 4.) Mutation
    // Randomly flip a bit with probability mutation rate
    for ($m=0; $m<POPULATION_SIZE; $m++) {
        for ($i=0; $i<CHROMOSOME_SIZE; $i++) {
            $rand = rand(0, 10000000) * 1.0 / 10000000;
            if ($rand < MUTATION_RATE) {
                if ($person_new[$m][chromosome][$i] == 0)
                    $person_new[$m][chromosome][$i] = 1;
                else
                    $person_new[$m][chromosome][$i] = 0;
            }
        }
    }
    
    $optimal = is_optimal($person_new);
    // compute fitness value
    for ($i=0; $i<POPULATION_SIZE; $i++)
        $person_new[$i][fitness] = get_fitness($person_new[$i][chromosome]);
    
   
    // avg fitness value
    $ep[$time][$run] = get_best_fitness($person_new);
    
    $person = $person_new;
    
} // end while loop


if (count($ep[$time]) > $max_generarion)
    $max_generarion = count($ep[$time]);

} // end for loop (run algorithm 10 times)

//echo "max generation: ".$max_generarion."<br>";
$avg = get_avg_fitness_10runs($ep, $max_generarion);
//print_r($avg);
for ($i=1;$i<=count($avg);$i++)
    echo "$avg[$i] \n<br>";

//
function get_avg_fitness_10runs($ep, $max) {

    for ($i=1;$i<=$max; $i++) {
         $element=0;
          $sum=0;
        for ($j=1; $j<=RUN_ALGORITHM; $j++){ 
                $sum = $sum + $ep[$j][$i];
                if(!is_null($ep[$j][$i]))
                    $element++;   
        }

        $avg[$i] = $sum/$element;     
    }

    return $avg;
    
}

// create a random chomosomes
function get_chromosome(){
    
    for ($i=0; $i<CHROMOSOME_SIZE; $i++) {
        $rand = rand(0,1);
        $chomo[$i] = $rand;  
    }
    
    return $chomo;
}

function get_best_fitness ($p) {
    
    for ($i=0; $i<POPULATION_SIZE; $i++) {
        $fitness[$i] = $p[$i][fitness];
    }
    
    $best_fitness = max($fitness)/CHROMOSOME_SIZE;
    
    return $best_fitness;
    
}

// calculate a fitness value for each person
function get_fitness($chromo) {
    
    $sum=0;
    
    for ($i=0; $i<CHROMOSOME_SIZE; $i++)
        $sum = $sum + $chromo[$i];
     
    return $sum;
   
}

function get_sum_fitness($p) {
    
    $sum=0;
    
    for ($i=0; $i<POPULATION_SIZE; $i++)
        $sum = $sum + $p[$i][fitness];
    
    return $sum;
    
}

// calculate a fitness probability for each person
function get_prob($person, $fitness) {

    $prob = $fitness/get_sum_fitness($person);
    
    return round($prob,4);

}

// select randomly two parents
function get_parents($person_new) {
    
    $parent[A] = rand(0,POPULATION_SIZE-1);
    $parent[B] = rand(0,POPULATION_SIZE-1);
    
    // make sure that the same person will not be selected
    while ($parent[A]==$parent[B]) {
        $parent = get_parents($person_new);
    }
    
    return $parent;
    
}

function crossover($person_new, $parents) {
    
    $tmp = array();
    
    $crossover_point = rand(0,CHROMOSOME_SIZE-1);
    for ($i=$crossover_point; $i<CHROMOSOME_SIZE; $i++) {
        $tmp[$parents[A]][chromosome][$i] = $person_new[$parents[B]][chromosome][$i]; 
        $tmp[$parents[B]][chromosome][$i] = $person_new[$parents[A]][chromosome][$i];
        
        $person_new[$parents[A]][chromosome][$i] = $tmp[$parents[A]][chromosome][$i];
        $person_new[$parents[B]][chromosome][$i] = $tmp[$parents[B]][chromosome][$i];
    }
    
    return $person_new;
    
}

function is_optimal($person_new) {
    
    $con = 0;

    for ($i=0; $i<POPULATION_SIZE; $i++) {
        
        $sum = 0;
        for($j=0;$j<CHROMOSOME_SIZE;$j++)
            $sum += $person_new[$i][chromosome][$j];
        
        if ($sum == CHROMOSOME_SIZE)
           $con = 1;
        
    }
    return $con;
}

?>
