<?php

//include_once 'model/DataReport.class.php';
include_once 'model/templateParser.class.php';
include 'vendor/autoload.php';


// router init
$router = new AltoRouter();
$router->setBasePath('/spamreportv2');


/*
* Route map settings
*/
$router->map('GET|POST','/', function(  ){
    $method = "reportlist";
    $arg['format'] = 'html';
    $arg['view'] = 'index';
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/annuaire/', function(  ){
    $method = "incoming";
    $arg['format'] = 'html';
    $arg['view'] = 'incoming';
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/report/', function( ){
    $method = "formulaire";
    $arg['format'] = 'html';
    $arg['view'] = 'reportForm';
    include_once 'controller/Controller.php';
});

$router->map('POST','/post/[a:route]', function( $route ){
    include_once 'controller/send.php';
});

$router->map('GET|POST','/vote/[a:view].[a:format]?', function( $view, $format ){
    $method = "vote";
    $arg['vote'] = $_REQUEST['vote'];
    //$arg['author_token'] = $_REQUEST['author_token'];
    $arg['author_token'] = $_REQUEST[1];
    // $arg['post_id'] = $_REQUEST['post_id'];
    $arg['post_id'] = $_REQUEST[80];
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/report/[i:id]/[a:view].[a:format]?', function( $id, $view, $format ){
    $method = "report";
    $arg['term'] = $id;
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('POST','/search/', function(){
    $method = "search";
    $arg['term'] =  $_REQUEST['search'] ;
    $arg['format'] = 'html';
    $arg['view'] = 'index';
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/search/[i:term]/[a:view].[a:format]', function($term, $view, $format){
    $method = "search";
    $arg['term'] = $term;
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/login/[a:view].[a:format]', function( $view, $format ){
    $method = "login";
    $arg['pseudo'] = $_REQUEST['pseudo'];
    $arg['password'] = $_REQUEST['password'];
    $arg['view'] = $view;
    $arg['format'] = $format;
    include_once 'controller/Controller.php';
});

$router->map('GET','/subscribe/', function( ){
    $method = "subscribe";
    $arg['view'] = 'workbench';
    $arg['format'] = 'html';
    include_once 'controller/Controller.php';
});

$router->map('POST','/subscribe/', function( ){
    include_once 'controller/send.php';
});

$router->map('GET|POST','/login/', function( ){
    $method = "login";
    $arg['pseudo'] = $_REQUEST['pseudo'];
    $arg['password'] = $_REQUEST['password'];
    $arg['view'] = 'login';
    $arg['format'] = 'html';
    include_once 'controller/Controller.php';
});

$router->map('GET|POST','/contact/', function(){
    $method = "contact";
    $arg['format'] = 'html';
    $arg['view'] = 'contact';
    include_once 'controller/Controller.php';

});

//matching
$match = $router->match();

// do we have a match?
if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] );
} else {
	// no route was matched
	// header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
	echo 'RIP 404 ZER';
}
