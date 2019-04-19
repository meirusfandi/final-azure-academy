<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Azure Blob Storage | Submission Azure Cloud Academy</title>
    <link rel="stylesheet" type="text/css" href="css/app.css"/>
    <script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>
    
    <div class="container">
		<div class="card">
			<div class="card-body">

                <h2 class="text-center">Blob Storage Computer Vision - Mei Rusfandi</h2>
                <hr>
                <h3>Add Image To Analize using Computer Vision</h3>
                    
                <form action="index.php" method="post" class="form-inline" enctype="multipart/form-data">
                    <input type="file" name="image"> 
                    <input type="submit" value="Upload" id="upload" name="upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>

    <div class="container">
		<div class="card">
			<div class="card-body">

                <h2 class="text-center">Hasil Upload File - Mei Rusfandi</h2>
                <hr>
                <h3>View Image To Analize using Computer Vision and Analyze it</h3>
                <table class='table table-hover'>
			<thead>
				<tr>
					<th>No. </th>
					<th>File Name</th>
					<th>File URL</th>
					<th>Action</th>
				</tr>
			</thead>
                <tbody>
                    <?php
                    $i = 0;
                    do {
                        foreach ($result->getBlobs() as $blob)
                        {
                            ?>
                            <tr>
                                <td><?php echo ++$i;?></td>
                                <td><?php echo $blob->getName() ?></td>
                                <td><?php echo $blob->getUrl() ?></td>
                                <td>
                                    <form action="analyze.php" method="post">
                                        <input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
                                        <input type="submit" name="submit" value="Analyze!" class="btn btn-primary">
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                    } while($result->getContinuationToken());

                    ?>
                </tbody>
            </table>

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

    <?php 
        require_once 'vendor/autoload.php';
        require_once './random_string.php';

        use MicrosoftAzure\Storage\Blob\BlobRestProxy;
        use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
        use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
        use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
        use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

        // $connect_string = "DefaultEndpointsProtocol=https;AccountName=".getenv("ACCOUNT_NAME").";AccountKey=".getenv("ACCOUNT_KEY").";EndpointSuffix=core.windows.net";
        $connect_string = "DefaultEndpointsProtocol=https;AccountName=fansdev;AccountKey=QFChV4ExeYoe/GCcpbnAagmKnFOvW8y7Lu3dwjyhhnrk/u38o9rLyjoFNXtMLPAO4dKDayHl+nxQPn+jtwKpow==;EndpointSuffix=core.windows.net";

        //create blob client service
        $blobClient = BlobRestProxy::createBlobService($connect_string);
        // $blob_client = ServiceBuilder::getInstance()->createBlobService($connect_string);

        $create_container_options = new CreateContainerOptions();
        $create_container_options->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

        //setup metadata container
        $create_container_options->addMetaData("key1", "value1");
        $create_container_options->addMetaData("key2", "value2");

        //create container name
        $container_name = "fansdev".generateRandomString();

        if (isset($_POST['upload'])) {
            $filename = strtolower($_FILES["image"]["name"]);
            $content = fopen($_FILES["image"]["tmp_name"], "r");
        
            $blobClient->createBlockBlob($container_name, $filename, $content);
            header("Location: index.php");
        }
        if (isset($_POST['analyze'])){

        }

        $listBlobs = new ListBlobsOptions();
        $listBlobs->setPrefix("");

        $result = $blobClient->listBlobs($container_name, $listBlobs);
    ?>

</body>
</html>