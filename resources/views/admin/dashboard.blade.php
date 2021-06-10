<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Dashboard</title>
        <link rel="stylesheet" href="{{ asset('bootstrap-3.1.1/css/bootstrap.min.css') }}">
        <style>
        body {margin:0;}

        ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
        position: fixed;
        top: 0;
        width: 100%;
        }

        li {
        float: left;
        }

        li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        }

        li a:hover:not(.active) {
        background-color: #111;
        }

        /* Header/Logo Title */
        .header {
        padding: 10px;
        text-align: center;
        background: black;
        color: white;
        font-size: 20px;
        }
        .active {
        background-color: #4CAF50;
        }
        body {
        font-family: "Lato", sans-serif;
        }

        .sidenav {
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
        }

        .sidenav a {
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 25px;
        color: #818181;
        display: block;
        transition: 0.3s;
        }

        .sidenav a:hover {
        color: #f1f1f1;
        }

        .sidenav .closebtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 36px;
        margin-left: 50px;
        }

        footer {
        text-align: center;
        padding: 3px;
        background-color: black;
        color: white;
        }



        @media screen and (max-height: 450px) {
        .sidenav {padding-top: 15px;}
        .sidenav a {font-size: 18px;}
        }
        </style>
    </head>
    <body>
        @include('admin.layouts.head')
        <header class="header">
            <h1>Dashboard<br>{{auth('admin')->user()->name}}</h1>
        </header>
        
        <footer>
            <p>&copy; {{date('Y')}} Jim<br>
        </footer>
    </body>
</html>