<?php
$DF = 0.9;
$LR = 0.4;
$E = 0.8;

$maze = array([-1,   -1, -1, -1, -1, -1, -1, -1,    -1],
                
              [-1,   1, 6, 10,  13, 18, 23, 26,           -1],
              [-1,   2, 7, -1,  14, 19, 24, 27,          -1],
              [-1,   3, -1, -1, 15, 20, 25, 28,         -1],
              [-1,   4, 8, 11,  16, 21, -1, 29,          -1],
              [-1,   5, 9, 12,  17, 22, -1, 30,          -1],
             
              [-1,   -1, -1, -1, -1, -1, -1, -1,    -1]);

// init Q values
for ($i=1;$i<=30;$i++) {
       $Q[$i][N] = 0;
       $Q[$i][E] = 0;
       $Q[$i][W] = 0;
       $Q[$i][S] = 0;   
}

// accumulated reward 
$sum_reward=0;

// Some parameters for convergence
$sum=0;
$sum_avg=0;
$delta=100; $CONV_TOL = 0.00001;
$episode=0;

//while ($episode<1000) {
while ($delta > $CONV_TOL) {
$episode++;
$sum_avg_before = $sum_avg;

$x=0;
$steps = 100;
$i_new = 2; $j_new=2;
$a="";
while ($x<$steps) {
    
    $x++;
    $i = $i_new; $j = $j_new;
    $s = $maze[$i][$j];
    $q = $Q[$s][$a];
    
    // if any value in Q(s,a) is not 0
    if ( $Q[$s][N] !=0 || $Q[$s][E] !=0 || $Q[$s][W] != 0 || $Q[$s][S] !=0 ) {
        
         /*
         * e-greedy policy
         */
        $rand = rand(1, 10)/10;
        
        // compute max in state s
        $Q_max = max($Q[$s][N], $Q[$s][E], $Q[$s][W], $Q[$s][S]); //{N,E,W,S}
        
        if ($rand < $E) {
        
            // Move next state and update index
            // N
            if ($Q[$s][N] == $Q_max) {
                    $r=0;
                    $i_new = $i-1; $j_new = $j;
                    $a = "N";
            // E
            } elseif ($Q[$s][E] == $Q_max) {

                    $r=0;
                    $i_new = $i; $j_new = $j+1;
                    $a = "E";

            // W
            } elseif ($Q[$s][W] == $Q_max) {

                    $r=0;
                    $i_new = $i; $j_new = $j-1;
                    $a = "W";

            // S
            } else {
          
                    if (($i==4) and $j==7) $r = 10;
                    else $r=0;
                    $i_new = $i+1; $j_new = $j;
                    $a = "S";   
            }
            
        
        } else {
            
            $action = rand(1,4);
            
            // "N"=>1, "E"=>2, "W"=>3, "S"=>4
            // select next action, but not within the Q_max
            
            // N
            if ( ($action == 1) and ($Q[$s][N] != $Q_max) ) {
                // wall, then skip
                if ($maze[$i-1][$j] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i-1; $j_new = $j;  
                    $a = "N";
                }
            // E
            } elseif ( ($action == 2) and ($Q[$s][E] != $Q_max) ) {
                // wall, then skip
                if ($maze[$i][$j+1] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i; $j_new = $j+1;
                    $a = "E";
            }
            // W    
            } elseif ( ($action == 3) and ($Q[$s][E] != $Q_max) ) {
                // wall, then skip
                if ($maze[$i][$j-1] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i; $j_new = $j-1;
                    $a = "W";
                }
            // S
            } else {
                if ($maze[$i+1][$j] == -1) continue;
                else {            
                    if (($i==4) and $j==7) $r = 10;
                    else $r=0;
                    $i_new = $i+1; $j_new = $j;
                    $a = "S";
                }            
            }

        }
    
    // all values in Q(s,a) are 0   
    } else {

        $action = rand(1,4);
        
        // "N"=>1, "E"=>2, "W"=>3, "S"=>4
        // select next action
            
            // N
            if ( $action == 1) {
                // wall, then skip
                if ($maze[$i-1][$j] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i-1; $j_new = $j;  
                    $a = "N";
                }
            // E
            } elseif ($action == 2) {
                // wall, then skip
                if ($maze[$i][$j+1] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i; $j_new = $j+1;
                    $a = "E";
            }
            // W    
            } elseif ( $action == 3 ) {
                // wall, then skip
                if ($maze[$i][$j-1] == -1) continue;
                else {
                    $r=0;
                    $i_new = $i; $j_new = $j-1;
                    $a = "W";
            }
            // S
            } else {
                if ($maze[$i+1][$j] == -1) continue;
                else {            
                    if (($i==4) and $j==7) $r = 10;
                    else $r=0;
                    $i_new = $i+1; $j_new = $j;
                    $a = "S";
                }            
            }
     
    }
        
    // compute max in the next state s"
    $s_next = $maze[$i_new][$j_new];
    $Q_max_next = max($Q[$s_next][N], $Q[$s_next][E], $Q[$s_next][W], $Q[$s_next][S]);
            
    // Compute Q(s,a)
    $Q[$s][$a] = $Q[$s][$a] + $LR * ( $r + $DF * $Q_max_next - $Q[$s][$a] );
        
    // if the goal state is reached, then start new epode
    if ($i_new == 5 and $j_new == 7) $x=200;
    
    $sum = $sum + abs($q-$Q[$s][$a]);
    
} // end while

    // check convergence
    if ($episode > 300) {
        $sum_avg = $sum/$episode;
        $delta = abs($sum_avg-$sum_avg_before);
    }

    // accumulated reward 
    $sum_reward = $sum_reward + $r;
   
    echo "<table border='1'>
            <tr>
                <td>State</td> <td>N</td> <td>E</td> <td>W</td> <td>S</td>
            </tr>";
                echo "<br>episode:".$episode."<br>";
                    for ($i=1;$i<=30;$i++) {
                        echo "<tr>";
                        echo "<td>$i</td><td>".round($Q[$i][N],2)."</td><td>".round($Q[$i][E],2)."</td><td>".round($Q[$i][W],2)."</td><td>".round($Q[$i][S],2)."</td>";
                        echo "</tr>";
                    }
    echo "</table>";


} // end while


?>