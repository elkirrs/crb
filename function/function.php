<?php

$file = "cache/cache.json";


function checkDates($value)
{
    $date = htmlspecialchars($value);
    $date = date("d/m/Y", strtotime($date));
    return $date;
}


function today()
{
    $now = date('d/m/Y');
    $date = ['ot' => $now ,'do' => $now];
    return $date;
}


/**
 * @param $date
 * @return array
 * Формирование API ссылки для полужения данных в заданном промежутке и формирования массива с данными
 */
function dynamicCourses($date)
{
    $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=" .
        checkDates($date['ot']) . "&date_req2=" . checkDates($date['do']) . "&VAL_NM_RQ=R01235";
    $xml = simplexml_load_file($url);

    foreach ($xml->xpath('Record') as $item)
    {
        $result[] = [
            'date' => date('d.m.Y', strtotime($item->attributes()['Date'])),
            'course' => (float)str_replace(',', '.', $item->Value)
        ];
    }
    return $result;
}


/**
 * @param string $text
 * @param $file
 * Открытия файла кеша в безопасном режиме запись данных кеша и закрытие фала
 */
function reWrite(string $text, $file)
{
    $fileHandle = fopen($file, "w");
    flock($fileHandle, LOCK_EX);
    fwrite($fileHandle, $text);
    flock($fileHandle, LOCK_UN);
    fclose($fileHandle);
}


/**
 * @param $a
 * @param $b
 * @return false|int
 * Метод для сортировки в возрастающем порядке массива при объединении для встроеной функции usort.
 */
function sortDate($a, $b)
{
    $a = strtotime($a["date"]);
    $b = strtotime($b["date"]);
    return $a - $b;
}


/**
 * @param $file
 * @param array $dateArray
 * Обединения двух массивов для перезаписи сушествующих данных с добовлением новых и сортировка с пользовательским методом.
 */
function arrayAdd($file, array $dateArray)
{
    if (is_array($fileDate = file_get_contents($file)))
    {
        $fileArray = json_decode($fileDate, true);
        $fileDateArray = array_merge($fileArray, $dateArray);
        array_unique($fileDateArray, SORT_REGULAR);
    }
    usort($dateArray, 'sortDate');
    $fileJson = json_encode($dateArray);
    reWrite($fileJson, $file);
}


/**
 * @param $file
 * @return mixed
 *  Открывает фаил(кеш) JSON и преобразуем в массив
 */
function openCache($file)
{
    $file = file_get_contents($file);
    $fileArray = json_decode($file, true);
    return $fileArray;
}


function checkCache($file)
{
    return file_get_contents($file);
}


/**
 * @param array $dateDay
 * @return array
 * @throws Exception
 * Вывод данных исходя из выбора дыты
 */
function views(array $dateDay, $file)
{
    if (checkCache($file))
    {
        $fileDate = openCache($file);
        $dateDays = allDate($dateDay);
        $selectUserDay = checkDateCahce($dateDays, $fileDate, $dateDay, $file);
    }
    else
    {
        $selectUserDay = addNewDay($file, $dateDay);
    }
    return $selectUserDay;
}


/**
 * @param array $day
 * @return DatePeriod
 * @throws Exception
 * Вывод всех дат в выбраном деопозоне пользователем
 */
function allDate(array $day)
{
//    $day = array('ot' => date('d/m/Y'), 'do' => date('d/m/Y'));
    $begin = new DateTime($day['ot']);
    $end = new DateTime($day['do']);
    $end = $end->modify('+1 day' );
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    return $daterange;
}


/**
 * @param $file
 * @param $date
 * @return array
 * Метод для Получения нового запроса на выбоку данных и запись в кеш фаил.
 */
function addNewDay($file, $date)
{
    $selectUserDay = dynamicCourses($date);
    arrayAdd($file, $selectUserDay);
    return $selectUserDay;
}


/**
 * @param $dateDays
 * @param $fileDate
 * @param $dateDay
 * @param $file
 * @return array
 * Проверка существующих данных в кеше исходя из выбраной даты.
 * При отсутствии данных формирование запроса на получение новых данных и их добавления.
 */
function checkDateCahce($dateDays, $fileDate, $dateDay, $file)
{
    foreach ($dateDays as $date)
    {
        foreach ($fileDate as $selectDay)
        {
            if (date("d.m.Y", strtotime($selectDay['date'])) == date("d.m.Y", strtotime($date->format("d.m.Y"))))
            {
                $selectUserDay[] =  $selectDay;
            }
        }
    }
    $selectUserDay = addNewDay($file, $dateDay);
    return $selectUserDay;
}