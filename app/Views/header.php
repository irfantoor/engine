<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Welcome to Irfans Engine</title>
 
    <style>
body { background-color: #fff; color: #222; font-family: sans-serif;padding:20px}
pre { margin: 0; font-family: monospace;}
a:link { color: #009; text-decoration: none; background-color: #fff;}
a:hover { text-decoration: underline;}
table { border-collapse: collapse; border: 0; width:95%; max-width: 934px; box-shadow: 1px 2px 3px #ccc;}
.center { text-align: center;}
.center table { margin: 1em auto; text-align: left;}
.center th { text-align: center !important;}
td, th { border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
h1, h2, h3, h4, h5, h6 { color:#36c}
h1 { font-size: 150%;}
h2 { font-size: 125%;}
.p { text-align: left;}
.e { background-color: #ccf; width: 300px; font-weight: bold;}
.h { background-color: #99c; font-weight: bold;}
.v { background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
.v i { color: #999;}
img { float: right; border: 0;}
hr { width:100%; background-color: #ccc; border: 0; height: 1px;}
form * { font-size:100%; margin-top:3px;}
input { height: 22px; min-width:200px; margin-bottom:20px;}
button { height:32px; width:100px; margin-bottom:20px }
.shell { background-color:#000; color:#fff;padding:10px; margin:10px 0; font-weight: 200}
.note { font-family: monospace; background:#ff9; padding:16px; max-width:300px};
    </style>
 
    <link rel="shortcut icon" href="/img/favicon.ico">
    <?php $engine->trigger('header'); ?>
</head>
<body id="page-top">

<div class="container">
    <?php $engine->trigger('page_top'); ?>
    
<h1>My Site</h1>

<nav>
    <a href="/">Home</a> |
    <a href="/phpinfo">PHP Info</a> |
    <a href="/env">Environment</a> |
    
    <a href="/admin">Admin</a>
    <?php $engine->trigger('menu'); ?>
</nav>

<hr>
    <!-- Page Content -->
    <div class="container">
