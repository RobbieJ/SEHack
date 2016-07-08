<?php
header('Content-Type: text/html');



$input_function=$_GET["function"];
$input_value=$_GET["value"];
$input_type=$_GET["type"];

//db connections
$servername = $_SERVER["dbserver"];
$username = $_SERVER["dbuser"];
$password = $_SERVER["dbpass"];
$dbname=$_SERVER["dbname"];

if(!$servername)
{
//db connections
$servername = "temperature.clmv89mfjqjm.eu-west-1.rds.amazonaws.com:3306 ";
$username = "hackathon";
$password = "hackathon123";
$dbname="hackathon";

}

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    $response="ERROR - no connection to $servername with $username / $dbname";
	echo "$response"; exit;
}


echo "<h1>Remote Sensor Home Page</h1>";

switch ($input_function) {
	
	case "viewReadings":
	viewReading($input_type,$input_value,$conn);
	break; 
	
	
	default:
	homePage($input_type,$input_value,$conn);
	
}





	function homePage($type,$value,$conn)
	{
        ?>
<form action=? method=get>
    <input type=hidden name=function value=viewReadings>
    Number of readings: <input type=text name=value size=3 value="1"> <BR>
    Type: <input type=text name=type size=10 value="temperature"> (temperature and pressure so far)<BR>
    <input type=submit value=Get>
    <BR>
    </form>
        <?php
    }
	
	function viewReading($type,$value,$conn)
	{
        echo "<TABLE width=500>
        <TR>
        <TD><B>Date/Time</B></TD>
        <TD><B>Type</B></TD>
        <TD><B>Value</B></TD>
        </TR>
        ";
        
		$sql = "SELECT datetime, type, value FROM weather WHERE `type`='$type' ORDER BY `id` DESC LIMIT 0,$value";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
		    while($row = mysqli_fetch_assoc($result)) {
                
                echo "
                        <TR>
        <TD>$row[datetime]</TD>
        <TD>$row[type]</TD>
        <TD>$row[value]</TD>

        </TR>"; 
	       $labels.="\"$row[datetime]\","; 
           $datapoints.="\"$row[value]\",";       
	       }
	   }
	   else {echo "<TR><TD>Null</TD></TR>"; }
	   echo "</TABLE>";
        
        ?>
    
 
<script src="Chart.js"></script>
<canvas id="myChart" width="200" height="200"></canvas>

<script>
    
    var data = {
    labels: [ <?php echo "$labels"; ?> ],
    datasets: [
        {
            label: "<?php echo "$type" ?> ",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [ <?php echo "$datapoints"; ?>],
        }
    ]
};
    
var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
    type: 'line',
    data: data
});
</script>


    
    <?php
    }
?>
