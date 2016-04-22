<?php

  /* Include the `fusioncharts.php` file that contains functions  to embed the charts. */
  include("includes/fusioncharts.php");

  /* The following 4 code lines contain the database connection information. Alternatively, you can move these code lines to a separate file and include the file here. You can also modify this code based on your database connection. */
  $hostdb = "localhost";  // MySQl host
  $userdb = "root";  // MySQL username
  $passdb = "";  // MySQL password
  $namedb = "Netflix_data";  // MySQL database name

  // Establish a connection to the database
  $dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

  // Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect
  if ($dbhandle->connect_error) {
    exit("There was an error with your connection: ".$dbhandle->connect_error);
  }

?>

<html>
<head>
  
  <title>Using PHP to Connect Your Charts to MySQL Database</title>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="style.css">

  <!-- You need to include the following JS files to render the chart.
  When you make your own charts, make sure that the path to these JS files is correct.
  Else, you will get JavaScript errors. -->
  <script src="js/fusioncharts.js"></script>
  <script src="js/fusioncharts.charts.js"></script>

</head>

<body>
  <?php
    //Create common chart attributes array
    $commonChartAttributesArray = array(
      "subCaption" => "From 2011 to 2015",
      "captionFontBold" => "0",
      "captionFontSize" => "25",
      "captionPadding" => "50",
      "subCaptionFontBold" => "0",
      "subCaptionFontSize" => "18",
      "formatNumberScale" => "0",
      "numberPrefix" => "$",
      "baseFont" => "Open Sans",
      "baseFontSize" => "15",
      "baseFontColor" => "#FFFFFF",
      "outCnvBaseFontSize" => "13",
      "outCnvBaseFontColor" => "#FFFFFF",
      "animation" => "1",
      "showHoverEffect" => "1",
      "plotHoverEffect" => "1",
      "showBorder" =>"0",
      "plotSpacePercent" => "8",
      "showPlotBorder" => "0",
      "showValues" => "0",
      "yAxisValuesPadding" => "0",
      "usePlotGradientColor" => "0",
      "bgColor" => "#000000",
      "canvasBgColor" => "#FF0000",
      "bgAlpha" => "85",
      "canvasBgAlpha" => "0",
      "canvasBorderAlpha" =>"0",
      "showAlternateHGridColor" => "0",
      "plotFillAlpha" => "80",
      "paletteColors" =>"#C8F463",
      "exportEnabled"=> "1",
      "exportShowMenuItem" => "0",        
      "divLineDashed" => "1",
      "divLineThickness" => "0.7",        
      "xAxisName" => "Year",
      "xAxisNameFontSize" => "18",      
      "yAxisName" => "Revenue (in million U.S. dollars)",
      "yAxisNameFontSize" => "18",
      "yAxisMinValue"=> "0",        
      "showToolTip" =>"1",
      "toolTipColor" => "#e0e4e6",
      "toolTipBorderColor" => "#e0e4e6",
      "toolTipBorderThickness" => "1",
      "toolTipBgColor" => "#000000",
      "toolTipBgAlpha"=> "80",
      "toolTipBorderRadius" => "2",
      "toolTipPadding" =>"10",
      "toolTipSepChar" => " - "
    );
 
    // Create data for Netflix
    // Form the SQL query that returns last five year's revenue data of Netflix
    $strQueryNetflix = "SELECT Year, Revenue FROM Netflix";

    // Execute the query, or else return the error message.
    $resultNetflix = $dbhandle->query($strQueryNetflix) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    // If the query returns a valid response, prepare the JSON string
    if ($resultNetflix) {
      
      // The `$arrDataNetflix` array holds the chart attributes and data      
      $arrDataNetflix["chart"] = $commonChartAttributesArray;
      
      //Create caption for NetFlix chart
      $arrDataNetflix["chart"]["caption"]  = "Netflix Annual Revenue";
      
      $arrDataNetflix["data"] = array();

      // Push the data into the array
      while($row = mysqli_fetch_array($resultNetflix)) {
        array_push($arrDataNetflix["data"], array(
          "label" => $row["Year"],
          "value" => $row["Revenue"]
          )
        );
      }
      
          
      // Create data for Facebook 
        
      // Form the SQL query that returns last five year's revenue data of Facebook
      $strQueryFb = "SELECT Year, Revenue FROM facebook";

      // Execute the query, or else return the error message.
      $resultFb = $dbhandle->query($strQueryFb) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

      // If the query returns a valid response, prepare the JSON string
      if ($resultFb) {
        
        // The `$arrDataFb` array holds the chart attributes and data     
        $arrDataFb["chart"] = $commonChartAttributesArray;
        
        //Create caption for FB chart
        $arrDataFb["chart"]["caption"] = "Facebook Annual Revenue";

        $arrDataFb["data"] = array();

        // Push the data into the array
        while($rowFb = mysqli_fetch_array($resultFb)) {
          array_push($arrDataFb["data"], array(
            "label" => $rowFb["Year"],
            "value" => $rowFb["Revenue"]
            )
          );
        }
        
        // JSON Encode the data for Facebook to retrieve the string containing the JSON representation of the data in the array.
        $jsonEncodedDataFb = json_encode($arrDataFb);

      }

      // JSON Encode the datafor NetFlix to retrieve the string containing the JSON representation of the data in the array.
      $jsonEncodedDataNetflix = json_encode($arrDataNetflix);
        
      // FusionCharts constructor
      $columnChart = new FusionCharts("column2d", "myFirstChart" , "100%", 500, "chart-1", "json", $jsonEncodedDataNetflix);
        
      // Render the chart 
      $columnChart->render();

      // Close the database connection
      $dbhandle->close();
    
    }

  ?>
  <script>
  
  var dataFacebook = <?php echo $jsonEncodedDataFb;?>;
  var dataNetflix = <?php echo $jsonEncodedDataNetflix;?>;

  function download_chart() {
     FusionCharts('myFirstChart').exportChart({
      "exportFormat": "svg"
    });
  }
  
  // Change Chart Data dynamically
  function setData() {
    var dataButton = document.getElementById('datachange');
    
    if (dataButton.innerHTML == "Show Facebook data") {
      dataButton.innerHTML = "Show Netflix data";
      FusionCharts('myFirstChart').setChartData(dataFacebook, "json");
    } else if (dataButton.innerHTML == "Show Netflix data") {
      dataButton.innerHTML = "Show Facebook data";
      FusionCharts('myFirstChart').setChartData(dataNetflix, "json");
    }
  }
  
  // Make sure we have FusionCharts object before we try to bind events
  FusionCharts.ready(function(){
    // Binding dataPlotRollOver event to feed div with year data
    FusionCharts('myFirstChart').addEventListener("dataPlotRollOver", function(ev, props) {
      document.getElementById('year').innerHTML = props.value;    
    });
  });
   
  </script>
  <!-- Chart container -->
  <div id="chart-1">Your chart will render here</div>
  <div class="moreInfo">
    <div id="year" class="extra">Revenue</div>
    <div id="export" class="extra" onclick="download_chart();">Download SVG</div>
    <div id="datachange" class="extra" onclick="setData();">Show Facebook data</div>
  </div>
</div>
</body>
</html>