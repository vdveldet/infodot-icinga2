<?php
require 'flight/Flight.php';

function hello(){
    echo 'hello world!';
}
function index(){
    echo 'hello index!';
}
function vis(){
    echo 'hello vis!';
}


Flight::route('/hello', 'hello');
Flight::route('/vis', 'vis');
Flight::route('/', 'index');

Flight::start();

