<?php

function checkDates($value)
{
    $date = htmlspecialchars($value);
    $date = date("d/m/Y", strtotime($date));
    return $date;
}


function dynamicCourses(array $date)
{
    if (!$date)
    {
        $now = date('d/m/Y');
        $now = date("d/m/Y", strtotime($now));
        $date = ['ot' => $now ,'do' => $now];
    }
    $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=" .
        checkDates($date['ot']) . "&date_req2=" . checkDates($date['do']) . "&VAL_NM_RQ=R01235";
    $xml = simplexml_load_file($url);

    foreach ($xml->xpath('Record') as $item)
    {
        $result[] = [
            'date' => (string)str_replace(',', '.', $item->attributes()['Date']),
            'course' => (float)str_replace(',', '.', $item->Value)
        ];
    }


    return $result;

}

/**
function reWrite($text)
{
    $file = "../cashe/cashe.json";
    $file_handle = fopen($file, "w");
    fwrite($file_handle, $text);
    fclose($file_handle);
}

function arrayAdd($dateArray)
{
    $file = "../cashe/cashe.json";
    $file = file_get_contents($file);
    $fileArray = json_decode($file,true);
    $fileDateArray = array_merge($fileArray, $dateArray);
    $fileDateArray = array_unique($fileDateArray,SORT_REGULAR);
    $fileJson = json_encode($fileDateArray);
    echo $fileJson;

}

arrayAdd([
    ['qw'=> 12, 'as'=> 23],
    ['asd'=> 12, 'asd'=> 23],
]);
*/
