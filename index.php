<?php
require 'vendor/autoload.php';

$pastanga = new \Slim\Slim();
$pastanga->config(array(
    'debug' => \Classes\Config::getMode(),
    'templates.path' => \Classes\Config::getTemplatePath()
));

/**
 * main page route (aka index)
 */
$pastanga->get('/', function() use($pastanga){
    $pastanga->render('index.html'); 
});

/**
 * save text from form
 */
$pastanga->post('/save', function() use($pastanga){
    $text = $pastanga->request()->post('text'); 
    $syntax = $pastanga->request()->post('syntax');

    /**
     * generate unique link for this post
     */
    $link = \Classes\Helpers::generateLink();

    /**
     * create database object
     */
    $db = new \Classes\Database();

    /**
     * and save the pasted text
     */
    $db->saveText($text, $syntax, $link);

    /**
     * generate response
     */
    echo json_encode(array(
        'response' => array(
            'link' => $link
        )
    ));
});

/**
 * show the saved text 
 */
$pastanga->get('/:link', function($link) use($pastanga){
    $db = new \Classes\Database();

    /**
     * trying to get the pasted text by link
     */
    $entry = $db->getTextByLink($link);

    /**
     * if we have no this $link in the database
     * then show 404 page
     */
    if(!$entry) {
        $pastanga->pass(); 
    }

    $pastanga->render('view.php', array('text' => $entry['text'], 'type' => $entry['type'])); 
});

$pastanga->run();
