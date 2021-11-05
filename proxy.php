<?php
$method = $_SERVER['REQUEST_METHOD'];

if ($_GET && $_GET['url'])
{
    $headers = getallheaders();

    $headers_str = [];
    $url = urldecode(str_replace("/?url=", "", $_SERVER['REQUEST_URI']));

    foreach ($headers as $key => $value)
    {
        if ($key == 'Host') continue;
        $headers_str[] = $key . ":" . $value;
    }

    $ch = curl_init($url);

    

    if ($method == "PUT" || $method == "PATCH" || ($method == "POST" && empty($_FILES)))
    {

        $data_str = file_get_contents('php://input');

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
    }
    elseif ($method == "POST")
    {
        $data_str = array();
        if (!empty($_FILES))
        {
            foreach ($_FILES as $key => $value)
            {
                $full_path = realpath($_FILES[$key]['tmp_name']);
                $data_str[$key] = '@' . $full_path;
            }
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str + $_POST);
    }
    
    if ($method !== 'GET')
    {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_str);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $result = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    header('Content-Type: ' . $contentType);

    curl_close($ch);

    echo $result;

}
else
{
    echo "<a href=\"https://karson.com.tr\">karson.com.tr</a>";
}
