<header>Header</header>
<div>{!! str_replace(
    [
        '@_NAME_@',
        '@_DATE_@',
        '@_TIME_@'
    ],
    [
        $sName,
        $sDate,
        $sTime
    ],
    $sBody
) !!}</div>
<footer>Footer</footer>