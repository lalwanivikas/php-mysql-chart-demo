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
  <style type="text/css">
    body {
      background-color: #262626;
      width: 650px;
      margin: 10px auto;
    }
  </style>

  <!-- You need to include the following JS files to render the chart.
  When you make your own charts, make sure that the path to these JS files is correct.
  Else, you will get JavaScript errors. -->
  <script src="js/fusioncharts.js"></script>
  <script src="js/fusioncharts.charts.js"></script>

</head>

<body>
  <?php

    // Form the SQL query that returns last five year's revenue data of Netflix
    $strQuery = "SELECT Year, Revenue FROM Netflix";

    // Execute the query, or else return the error message.
    $result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    // If the query returns a valid response, prepare the JSON string
    if ($result) {
      
      // The `$arrData` array holds the chart attributes and data
      $arrData = array(
        
        "chart" => array(

          "caption" => "Netflix Annual Revenue",
          "subCaption" => "From 2011 to 2015",
          "captionFontBold" => "0",
          "captionFontSize" => "25",
          "captionPadding" => "50",
          "subCaptionFontBold" => "0",
          "subCaptionFontSize" => "18",
          "formatNumberScale" => "0",
          "numberPrefix" => "$",

          // base font cosmetics
          "baseFont" => "Open Sans",
          "baseFontSize" => "15",
          "baseFontColor" => "#FFFFFF",
          "outCnvBaseFontSize" => "13",
          "outCnvBaseFontColor" => "#FFFFFF",

          // chart cosmetics & options
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

          // div line cosmetics
          "divLineDashed" => "1",
          "divLineThickness" => "0.7",

          // x-axis options
          "xAxisName" => "Year",
          "xAxisNameFontSize" => "18",

          // y-axix options
          "yAxisName" => "Revenue (in million U.S. dollars)",
          "yAxisNameFontSize" => "18",
          "yAxisMinValue"=> "0",

          // tooltip cosmetics
          "showToolTip" =>"1",
          "toolTipColor" => "#e0e4e6",
          "toolTipBorderColor" => "#e0e4e6",
          "toolTipBorderThickness" => "1",
          "toolTipBgColor" => "#000000",
          "toolTipBgAlpha"=> "80",
          "toolTipBorderRadius" => "2",
          "toolTipPadding" =>"10",
          "toolTipSepChar" => " - "

          )
        );

      $arrData["data"] = array();

      // Push the data into the array
      while($row = mysqli_fetch_array($result)) {
        array_push($arrData["data"], array(
          "label" => $row["Year"],
          "value" => $row["Revenue"]
          )
        );
      }

      // JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
      $jsonEncodedData = json_encode($arrData);
      
      // FusionCharts constructor
      $columnChart = new FusionCharts("column2d", "myFirstChart" , "100%", 500, "chart-1", "json", $jsonEncodedData);
      
      // Render the chart 
      $columnChart->render();

      // Close the database connection
      $dbhandle->close();
    
    }

  ?>
  
  <!-- Chart container -->
  <div id="chart-1">Your chart will render here</div>

</body>
</html>