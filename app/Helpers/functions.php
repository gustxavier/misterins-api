<?php
function formatDate($date)
{
    if (strpos($date, '/')) {
        return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
    } else {
        return date("Y/m/d", strtotime($date));
    }
}
