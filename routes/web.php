<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->post('/compress-pdf', 'PdfController@compress');
$router->post('/convert/pdf-to-docx', 'PdfController@convertToDocx');
$router->post('/convert/pdf-to-xlsx', 'PdfController@convertToXlsx');
$router->post('/convert/pdf-to-pptx', 'PdfController@convertToPptx');
$router->post('/convert-image', 'ImageConvertController@convertToImages');




