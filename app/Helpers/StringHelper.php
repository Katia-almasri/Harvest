<?php
namespace App\Helpers;

class StringHelper{
    public static function generateRandomString($length = 30) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $timestamp = time();
        $randomString = '';
        $charactersLength = strlen($characters);
        $timestampLength = strlen($timestamp);
        for ($i = 0; $i < $length - $timestampLength; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $randomString .= $timestamp;
        return $randomString;
    }
}
