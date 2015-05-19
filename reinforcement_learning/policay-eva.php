<?php
$policy = 0.25;
$rewards = [0,10];
$gamma = 0.9;
$nrows = 5;
$ncols = 7;
$actions = ["N", "E", "W", "S"];
$directions = $actions;

// -1 is the walls
$V = array(   [-1,   -1, -1, -1, -1, -1, -1, -1,    -1],
                
              [-1,   0, 0, 0, 0, 0, 0, 0,           -1],
              [-1,   0, 0, -1, 0, 0, 0, 0,          -1],
              [-1,   0, -1, -1, 0, 0, 0, 0,         -1],
              [-1,   0, 0, 0, 0, 0, -1, 0,          -1],
              [-1,   0, 0, 0, 0, 0, -1, 0,          -1],
             
              [-1,   -1, -1, -1, -1, -1, -1, -1,    -1]);

// Some parameters for convergence
$CONV_TOL = 0.00001; $delta = 10000;

while ($delta > $CONV_TOL) {
    $delta = 0;
    for ($i=1; $i<=$nrows; $i++) {
        for ($j=1; $j<=$ncols; $j++) {
            
            $v_tmp = 0;
            
            // s is at the walls or goal state...skip
            if ($V[$i][$j] == -1) continue;
            if ($i==5 and $j==7) continue;
            
            /*
             * Loop over each possible action {N,E,W,S}
             */
           
            // Loop over each possible next direction {N,E,W,S}
            // check walls for each next state and update index
            foreach ($directions as $direction)
                $index[$direction] = checkWall($i, $j, $direction);               
          
            $v = $V[$i][$j];
                       
            // Action = N
            $reward = $rewards[0];
            $v_tmp = $v_tmp + 0.85 * ( $reward + ($gamma * $V[$index[N][0]][$index[N][1]]) ); 
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[E][0]][$index[E][1]]) );
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[W][0]][$index[W][1]]) ); 
            if ($i==4 and $j==7) $reward = $rewards[1];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[S][0]][$index[S][1]]) );          
            
            // Action = E
            $reward = $rewards[0];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[N][0]][$index[N][1]]) );
            $v_tmp = $v_tmp + 0.85 * ( $reward + ($gamma * $V[$index[E][0]][$index[E][1]]) );
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[W][0]][$index[W][1]]) );
            if ($i==4 and $j==7) $reward = $rewards[1];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[S][0]][$index[S][1]]) );
            
            // Action = W
            $reward = $rewards[0];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[N][0]][$index[N][1]]) );
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[E][0]][$index[E][1]]) );
            $v_tmp = $v_tmp + 0.85 * ( $reward + ($gamma * $V[$index[W][0]][$index[W][1]]) );
            if ($i==4 and $j==7) $reward = $rewards[1];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[S][0]][$index[S][1]]) );
            
            // Action = S
            $reward = $rewards[0];
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[N][0]][$index[N][1]]) );
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[E][0]][$index[E][1]]) );
            $v_tmp = $v_tmp + 0.05 * ( $reward + ($gamma * $V[$index[W][0]][$index[W][1]]) );
            if ($i==4 and $j==7) $reward = $rewards[1];
            $v_tmp = $v_tmp + 0.85 * ( $reward + ($gamma * $V[$index[S][0]][$index[S][1]]) ); 
            
            // V(s)
            $V[$i][$j] = $policy * $v_tmp;
            
            // delta = max( [ delta, abs( v-V(ii,jj) ) ] );
            $delta = max($delta, abs($v-$V[$i][$j]));
                    
        }
    }
   
   echo $delta;
   foreach($V as $k=>$v)
   {
       if($k!=0 && $k!=6)
       {
            foreach($v as $k1=>$v1)
            {
                if($k1!=0 && $k1!=8)
                echo round($v1,6) . "   ";         
            }
       }
       echo '<br/>';
   } 
}// end while 


function checkWall($i, $j, $direction)    
{
    global $V;
    $index = array();
    $i_new = 0;
    $j_new = 0;
                
                switch ($direction) {
                    case "N":                    
                        // there is the wall on North...can't go North
                        if ($V[$i-1][$j] == -1) { $i_new = $i; $j_new = $j;
                        } else { $i_new = $i-1; $j_new = $j; }
                        $index = [$i_new,$j_new];
                            
                    break;

                    case "E":                        
                        // there is the wall on East...can't go East
                        if ($V[$i][$j+1] == -1) { $i_new = $i; $j_new = $j;
                        } else { $i_new = $i;$j_new = $j+1; }
                        $index = [$i_new,$j_new];
                        
                    break;
                    
                    case "W":                        
                        // there is the wall on West...can't go West
                        if ($V[$i][$j-1] == -1) { $i_new = $i;$j_new = $j;
                        } else { $i_new = $i;$j_new = $j-1;}
                        $index = [$i_new,$j_new];
                        
                    break;
                    
                    case "S":                        
                        // there is the wall on South...can't go South
                        if ($V[$i+1][$j] == -1) { $i_new = $i; $j_new = $j;
                        } else { $i_new = $i+1; $j_new = $j; }
                        $index = [$i_new,$j_new];

                    break;
                }

    return $index;
}

?>
