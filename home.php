<?php

    require_once 'connection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="font/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Ecommerce Website</title>
</head>
<body>
    <?php
      include_once 'header.php';

   ?>

   <main>
       <?php
        
        $latitude = $_COOKIE['latitude'];
        $longitude = $_COOKIE['longitude'];
        
        
    
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
        $query = $conn->query($sql); 
        
        if($query->num_rows > 0){ 
            while($row = $query->fetch_assoc()){ 
                $place = $row['id'];
                $mysql = "SELECT * FROM product where place_id = $place";
                $all_product = $conn->query($mysql);
                $place = $row['id'];
                $mysql = "SELECT * FROM product where place_id = $place";
                $all_product = $conn->query($mysql);
                
                while($rows = mysqli_fetch_assoc($all_product)){
                    ?>
                    <div class="card">
                        <div class="image">
                            <img src="<?php echo $rows["product_image"]; ?>" alt="">
                        </div>
                        <div class="caption">
                            <p class="rate">
                                 <i class="fas fa-star"></i>
                                 <i class="fas fa-star"></i>
                                 <i class="fas fa-star"></i>
                                 <i class="fas fa-star"></i>
                                 <i class="fas fa-star"></i>
                            </p>
                            <p class="product_name"><?php echo $rows["product_name"];  ?></p>
                            <p class="price"><b>$<?php echo $rows["price"]; ?></b></p>
                            <p class="discount"><b><del>$<?php echo $rows["discount"]; ?></del></b></p>
                        </div>
                        <button class="add" data-id="<?php echo $rows["product_id"];  ?>">Add to cart</button>
                    </div>
                    <?php
             
                    }
                
    
        ?> 
            <div class="pbox"> 
                <h4><?php echo $row['title']; ?></h4> 
                <p><?php echo $row['address']; ?></p> 
                <?php if(empty(!$row['distance'])){ ?> 
                <p>Distance: <?php ($row['distance'])*1000; ?> Metres<p> 
                <?php } ?> 
            </div> 
        <?php 
            } 
        }else{ 
            echo '<h5>Place(s) not found...</h5>'; 
        } 
        
        $place = $row['id'];
        $mysql = "SELECT * FROM product where place_id = $place";
        $all_product = $conn->query($mysql);
    
      
    ?>
     
   </main>
   <script>
       var product_id = document.getElementsByClassName("add");
       for(var i = 0; i<product_id.length; i++){
           product_id[i].addEventListener("click",function(event){
               var target = event.target;
               var id = target.getAttribute("data-id");
               var xml = new XMLHttpRequest();
               xml.onreadystatechange = function(){
                   if(this.readyState == 4 && this.status == 200){
                       var data = JSON.parse(this.responseText);
                       target.innerHTML = data.in_cart;
                       document.getElementById("badge").innerHTML = data.num_cart + 1;
                   }
               }

               xml.open("GET","connection.php?id="+id,true);
               xml.send();
            
           })
       }

   </script>
</body>
</html>