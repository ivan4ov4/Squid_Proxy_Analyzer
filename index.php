<?php
session_start();
$lineCount = 0;
$sourceAddrArray = [];
$destinationAddrArray = [];
$errorLines = 0;

if(isset($_POST["submit"])) {
    if(!isset($_POST['filePathLocation']))
    {
        echo "invalid varable!"; exit;
    }

    $getPath = $_POST['filePathLocation'];

    try
    {
        foreach(file($getPath) as $line) {
            $parts = preg_split('/\s+/', $line);
            $getSourceAddr = $parts[2];
            $getDestinationAddr = parse_url($parts[6], PHP_URL_HOST);
            
            $result = uploadToArray($sourceAddrArray,$getSourceAddr);
            if($result != 0)
            {
                $sourceAddrArray = $result;
            }

            $result = uploadToArray($destinationAddrArray,$getDestinationAddr);
            if($result != 0)
            {
                $destinationAddrArray = $result;
            }

            $lineCount +=1;
        }
    } 
    catch (Exception $ex)
    {
        echo "File can't be open!";
    }
}

function uploadToArray ($arrayCollection, $elemetToCheck)
{
    $sourceResult = tryExtractIpAddress($elemetToCheck);
    if($sourceResult != 1)
    {
        $key = array_search($sourceResult, $arrayCollection);
        if($key === false)
        {
            array_push($arrayCollection, $sourceResult);
            return $arrayCollection;
        }
        return 0;
    } else {
        $sourceResult = tryExtractDomainAddress($elemetToCheck);
        $key = array_search($sourceResult, $arrayCollection);
        if($key === false)
        {
            array_push($arrayCollection, $sourceResult);
            return $arrayCollection;
        }
        return 0;
    }
}

function tryExtractIpAddress($ipOrDomain) : string
{
    $re = '/(^.+?((?:\d+\.){3}\d+).+$)/m';
    preg_match_all($re, " ". $ipOrDomain . " ", $matches, PREG_SET_ORDER, 0);

    if(count($matches) != 0)
    {
        $extract = $matches[0][2];
        return $extract;
    }
    
    return 1;
}

function tryExtractDomainAddress($ipOrDomain) : string
{
    $re = '/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n?]+)/im';
    preg_match_all($re, $ipOrDomain, $matches, PREG_SET_ORDER, 0);

    if(count($matches) != 0)
    {
        $extract = $matches[0][1];
        return $extract;
    }
    
    return 1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="post" enctype="multipart/form-data">
        Select log location
        <input type="text" name="filePathLocation" id="filePathLocation">
        <input type="submit" value="Upload" name="submit">
    </form>
    <br>

    <?php if(isset($_POST["submit"])) { ?>
    <form action="scvExport.php" method="post" target="_blank" enctype="multipart/form-data">
        Choose what do you want to export:<br>
        Sources total count: <?php echo count($sourceAddrArray); ?>
        <?php
            $serialized =htmlspecialchars(serialize($sourceAddrArray));
            echo "<input type=\"hidden\" name=\"ArrayData\" value=\"$serialized\"/>";
        ?>
        <input type="submit" value="Sources.csv" name="Sources"><br>
    </form>

    <form action="scvExport.php" method="post" target="_blank" enctype="multipart/form-data">
        <?php
        $serialized =htmlspecialchars(serialize($destinationAddrArray));
        echo "<input type=\"hidden\" name=\"ArrayData\" value=\"$serialized\"/>";
        ?>
        Destinations total count: <?php echo count($destinationAddrArray); ?> 
        <input type="submit" value="Destinations.csv" name="Destinations">
    </form>
    <br>
        <table>
            <tr>
                <td>Lines with error: </td>
                <td><?php echo $errorLines; ?></td>
            </tr>
            <tr>
                <td>Lines proceed: </td>
                <td><?php echo $lineCount; ?></td>
            </tr>
        </table>

        <table>
        <tr>
            <th>Sources</th>
        </tr>
        <?php 
        $setLoop = count($sourceAddrArray);
        if($setLoop >= 10) $setLoop = 10;
        for($i = 0; $i < $setLoop; $i++) 
        { ?>
            <tr>
                <td> <?php 
                echo $sourceAddrArray[$i]; ?> </td>
            </tr>
        <?php } ?>

        </table>

        <table>
        <tr>
            <th>Destinations</th>
        </tr>

        <?php 
        $setLoop = count($destinationAddrArray);
        if($setLoop >= 10) $setLoop = 10;
        for($i = 0; $i < $setLoop; $i++) 
        { ?>
            <tr>
                <td> <?php echo $destinationAddrArray[$i]; ?> </td>
            </tr>
        <?php } ?>
        
        </table>
    <?php } ?>
</body>
</html>
