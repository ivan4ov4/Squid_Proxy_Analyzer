<?php
session_start();

$data = [];
$FileName = "";
$client_file = '';

if(isset($_POST['Sources']))
{
    echo "Export csv source file";
    $data = unserialize($_POST['ArrayData']);
    array_unshift($data, "Sources");
    $FileName = "temp\Sources.csv";
    $client_file = 'Sources.csv';
}

if(isset($_POST['Destinations']))
{
    echo "Export csv destination file";
    $data = unserialize($_POST['ArrayData']);
    array_unshift($data, "Destinations");
    $FileName = "temp\Destinations.csv";
    $client_file = 'Sources.csv';
}

if(isset($_POST))
{
    $fp = fopen($FileName, 'w');
    foreach ($data as $fields) {
        $arr = array($fields);
        var_dump($arr);
        fputcsv($fp, $arr);
    }
    fclose($fp);

    // $file_to_download = $FileName;
     
    // $download_rate = 200; // 200Kb/s
    
    // $f = null;
    
    // try {
    //     if (!file_exists($file_to_download)) {
    //         throw new Exception('File ' . $file_to_download . ' does not exist');
    //     }
    
    //     if (!is_file($file_to_download)) {
    //         throw new Exception('File ' . $file_to_download . ' is not valid');
    //     }
    
    //     header('Cache-control: private');
    //     header('Content-Type: application/octet-stream');
    //     header('Content-Length: ' . filesize($file_to_download));
    //     header('Content-Disposition: filename=' . $client_file);
    
    //     // flush the content to the web browser
    //     flush();
    
    //     $f = fopen($file_to_download, 'r');
    
    //     while (!feof($f)) {
    //         // send the file part to the web browser
    //         print fread($f, round($download_rate * 1024));
    
    //         // flush the content to the web browser
    //         flush();
    
    //         // sleep one second
    //         sleep(1);
    //     }
    // } catch (\Throwable $e) {
    //     echo $e->getMessage();
    // } finally {
    //     if ($f) {
    //         fclose($f);
    //     }
    // }

}
?>