<?php
/*
 * Controller
 */
//include_once 'model/DataReport.class.php';
include_once 'model/templateParser.class.php';
include 'vendor/autoload.php';

// router init
$router = new AltoRouter();
$router->setBasePath('/spamreportv2');


    /*
     * Route map settings
     */
$router->map('GET','/', function(){
    include 'view/index.html';
});

$router->map('GET','/report', function(){
    $method = "report";
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

$router->map('GET','/search/[i:term]/[a:view].[a:format]', function($term, $view, $format){
    $method = "search";
    $arg['term'] = $term;
    $arg['format'] = $format;
    $arg['view'] = $view;
    include_once 'controller/Controller.php';
});

$router->map('GET','/contact/', function(){

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