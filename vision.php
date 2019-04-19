<?php 
    if (isset($_POST['analyze'])){
        if (isset($_POST['vision'])) {
            $url = $_POST['vision'];
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Computer Vision | Submission Azure Cloud Academy</title>
    <link rel="stylesheet" type="text/css" href="css/app.css"/>
    <script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>

    <script type="text/javascript">
        function processImage(){
            var subscriptionKey = "db80b96ff6d0481b8525b516a219cfaa";

            var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";

            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };

            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;

            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
    
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
    
                type: "POST",
    
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
    
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
            })
    
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>

    <div class="container">
		<div class="card">
            <div class="card-body">
                <div id="wrapper" style="width:1020px; display:table;">
                    <div id="jsonOutput" style="width:600px; display:table-cell;">
                        Response:
                        <br><br>
                        <textarea id="responseTextArea" class="UIInput"
                                style="width:580px; height:400px;"></textarea>
                    </div>
                    <div id="imageDiv" style="width:420px; display:table-cell;">
                        Source image:
                        <br><br>
                        <img id="sourceImage" width="400" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>