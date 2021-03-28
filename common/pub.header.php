<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}
$page_content = <<<INI_HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{$page_header}</title>
        <meta name="description" content="{$cfgrow['site_descr']}">
        <meta name="keywords" content="{$cfgrow['site_keywrd']}">
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.ico"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">

    </head>

    <body>
        <div id="app">
INI_HTML;
echo myvalidate($page_content, 1);
