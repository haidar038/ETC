<?php
// includes/functions.php

// Fungsi untuk generate token QR (random string sederhana)
function generateQRToken($length = 10)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}