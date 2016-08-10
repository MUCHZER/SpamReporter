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
$router->map('GET','/', function(  ){
    $method = "reportlist";
    $arg['format'] = 'html';
    $arg['view'] = 'index';
    include_once 'controller/Controller.php';
});

$router->map('GET','/report', function( ){
    $method = "formulaire";
    $arg['format'] = 'html';
    $arg['view'] = 'reportForm';
    include_once 'controller/Controller.php';
});

$router->map('POST','/post/[a:route]', function( $route ){
    include_once 'controller/send.php';
});

$router->map('GET','/vote/[a:view].[a:format]?', function( $view, $format ){
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

$router->map('GET','/report/[i:id]/[a:view].[a:format]?', function( $id, $view, $format ){
    $method = "report";
    $arg['term'] = $id;
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('GET','/search/', function(){
    $method = "search";
    $arg['term'] = $_REQUEST['search'];
    $arg['format'] = 'json';
    include_once 'controller/Controller.php';
});

$router->map('GET','/search/[i:term]/[a:view].[a:format]', function($term, $view, $format){
    $method = "search";
    $arg['term'] = $term;
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('GET','/login/[a:view].[a:format]', function( $view, $format ){
    $method = "login";
    $arg['pseudo'] = $_REQUEST['pseudo'];
    $arg['password'] = $_REQUEST['password'];
    $arg['view'] = $view;
    $arg['format'] = $format;
    include_once 'controller/Controller.php';
});


$router->map('GET','/contact/', function(){

});
//~~~~~~~~~~~~~~~~~~~~~WORKBENCH~~~~~~~~~~~~~~~~~~~~

$router->map('GET','/workbench', function(){
  $method = "formulaire";
  $arg['format'] = 'html';
  $arg['view'] = 'workbench';
  include_once 'controller/Controller.php';
});

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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
