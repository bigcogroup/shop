<?php $latitude = $_COOKIE['latitude'];
        $longitude = $_COOKIE['longitude'];
        
        // use the latitude and longitude values in your PHP code
        //echo "Latitude: " . $latitude . "<br>";
        //echo "Longitude: " . $longitude . "<br>";
        
        $sql_distance = $having = ''; 
        if(!empty($distance_km) && !empty($latitude) && !empty($longitude)){ 
            $radius_km = 0.5; 
            $sql_distance = " ,(((acos(sin((".$latitude."*pi()/180)) * sin((`p`.`latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`p`.`latitude`*pi()/180)) * cos(((".$longitude."-`p`.`longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance "; 
             
            $having = " HAVING (distance <= $radius_km) "; 
             
            $order_by = ' distance ASC '; 
        }else{ 
            $order_by = ' p.id DESC '; 
        } 
         
        // Fetch places from the database 
        $sql = "SELECT p.*".$sql_distance." FROM places p $having ORDER BY $order_by"; 
        $query = $db->query($sql); 
        
        if($query->num_rows > 0){ 
            while($row = $query->fetch_assoc()){ 
        ?> 
            <div class="pbox"> 
                <p><? echo $row['id']; ?></p>
                <h4><?php echo $row['title']; ?></h4> 
                <p><?php echo $row['address']; ?></p> 
                <?php if(!empty($row['distance'])){ ?> 
                <p>Distance: <?php echo round($row['distance'], 2); ?> KM<p> 
                <?php } ?> 
            </div> 
        <?php 
            } 
        }else{ 
            echo '<h5>Place(s) not found...</h5>'; 
        } 
        $place = $row['id'];
        $sql = "SELECT * FROM product where place_id = $place";
        $all_product = $conn->query($sql);   

        
    $sql_distance = $having = ''; 
    if(!empty($distance_km) && !empty($latitude) && !empty($longitude)){ 
        $radius_km = 0.5; 
        $sql_distance = " ,(((acos(sin((".$latitude."*pi()/180)) * sin((`p`.`latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`p`.`latitude`*pi()/180)) * cos(((".$longitude."-`p`.`longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance "; 
        
        $having = " HAVING (distance <= $radius_km) "; 
        
        $order_by = ' distance ASC '; 
    }else{ 
        $order_by = ' p.id DESC '; 
    } 
    
    // Fetch places from the database 
    $sql = "SELECT p.*".$sql_distance." FROM places p $having ORDER BY $order_by"; 
    $query = $db->query($sql); 
    
    if($query->num_rows > 0){ 
        while($row = $query->fetch_assoc()){ 
    ?> 
        <div class="pbox"> 
            <h4><?php echo $row['title']; ?></h4> 
            <p><?php echo $row['address']; ?></p> 
            <?php if(!empty($row['distance'])){ ?> 
            <p>Distance: <?php echo round($row['distance'], 2); ?> KM<p> 
            <?php } ?> 
        </div> 
    <?php 
        } 
    }else{ 
        echo '<h5>Place(s) not found...</h5>'; 
    } 
    


    $place = $row['id'];
    $mysql = "SELECT * FROM product where place_id = $place";
    