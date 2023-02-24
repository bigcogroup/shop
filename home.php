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
        
        // Set the radius of the geofence in meters
        $radius = 500;
        
        // Convert latitude and longitude to radians
        $latitude_rad = deg2rad($latitude);
        $longitude_rad = deg2rad($longitude);
        
        // Earth's radius in meters
        $earth_radius = 6371000;
        
        // Calculate the minimum and maximum latitude and longitude
        $min_latitude = rad2deg($latitude_rad - ($radius / $earth_radius));
        $max_latitude = rad2deg($latitude_rad + ($radius / $earth_radius));
        $min_longitude = rad2deg($longitude_rad - ($radius / $earth_radius / cos($latitude_rad)));
        $max_longitude = rad2deg($longitude_rad + ($radius / $earth_radius / cos($latitude_rad)));
        
        // Define the geofence as a polygon
        $geofence = [
            [$min_latitude, $min_longitude],
            [$min_latitude, $max_longitude],
            [$max_latitude, $max_longitude],
            [$max_latitude, $min_longitude],
            [$min_latitude, $min_longitude],
        ];
        
        // Define the MySQL query to select locations within the geofence
        $query = "SELECT * FROM places WHERE latitude BETWEEN " . $min_latitude . " AND " . $max_latitude . " AND longitude BETWEEN " . $min_longitude . " AND " . $max_longitude;
        
        // Execute the MySQL query and output the results
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {

                $lat1 = $latitude; // Replace with the actual latitude
                $lon1 = $longitude; // Replace with the actual longitude

                // Set the latitude and longitude of the second point
                $lat2 = $row["latitude"]; // Replace with the actual latitude
                $lon2 = $row["longitude"]; // Replace with the actual longitude

                // Earth's radius in meters
                $earth_radius = 6371000;

                // Convert latitude and longitude to radians
                $lat1_rad = deg2rad($lat1);
                $lon1_rad = deg2rad($lon1);
                $lat2_rad = deg2rad($lat2);
                $lon2_rad = deg2rad($lon2);

                // Calculate the distance between the two points using the Haversine formula
                $delta_lat = $lat2_rad - $lat1_rad;
                $delta_lon = $lon2_rad - $lon1_rad;
                $a = sin($delta_lat / 2) * sin($delta_lat / 2) + cos($lat1_rad) * cos($lat2_rad) * sin($delta_lon / 2) * sin($delta_lon / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earth_radius * $c;

                // Output the distance in meters

                echo "Location ID: " . $row["id"] . " - Latitude: " . $row["latitude"] . " - Longitude: " . $row["longitude"] . "<br>";
                $place = $row['id'];
                $mysql = "SELECT * FROM product where place_id = $place";
                $all_product = $conn->query($mysql);
                while($rows = mysqli_fetch_assoc($all_product)){


                    ?>
                    <div class="card">
                        <div class="image">
                            <p>The distance between the two points is <?php echo $rounded = round($distance , 2)?>  meters.</p>
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
            
            }
        } else {
            echo "No locations found within the geofence.";
        }
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
