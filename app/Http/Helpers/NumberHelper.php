<?php

use Illuminate\Support\Carbon;

if (!function_exists('en2bn')) {
    function en2bn($number)
    {
        if (app()->getLocale() !== 'bn') {
            return $number;
        }

        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($en, $bn, $number);
    }
}

if (!function_exists('dateToBn')) {
    function dateToBn($date)
    {
        $convertedDate = \Carbon\Carbon::parse($date)->format('d M Y, h:i A');

        if (app()->getLocale() !== 'bn') {
            return $convertedDate;
        }

        $convertedDate = en2bn($convertedDate);

        $replacements = [
            'Jan' => 'জানু',
            'Feb' => 'ফেব',
            'Mar' => 'মার্চ',
            'Apr' => 'এপ্রি',
            'May' => 'মে',
            'Jun' => 'জুন',
            'Jul' => 'জুল',
            'Aug' => 'আগ',
            'Sep' => 'সেপ্টে',
            'Oct' => 'অক্টো',
            'Nov' => 'নভে',
            'Dec' => 'ডিসে',
            'AM' => 'AM',
            'PM' => 'PM',
        ];

        return strtr($convertedDate, $replacements);
    }
}

if (!function_exists('en2bnMoney')) {
    function en2bnMoney($amount)
    {
        if (app()->getLocale() !== 'bn') {
            return number_format($amount) . ' Taka';
        }

        return en2bn(number_format($amount)) . ' টাকা';
    }
}

if (!function_exists('en2bnMobile')) {
    function en2bnMobile($mobile)
    {
        return en2bn($mobile); 
    }
}


