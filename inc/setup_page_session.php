<?php

 /**
  * Use this on any page after connection to the database.
  * This page will create a sesssion and set the content type in the header
  * NOTE TO SELF: This could potentiall be moved to conn.php
  * BUT WATCH OUT FOR PDO SETATTRIBUTE
  */
 
// Get the db connection details
require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/config.php");

// Get the Try/Catch block to create the db object
require_once(ROOT_PATH . "inc/conn.php");

// Configure PDO to return database rows from db using associative array.
// Array will have string indexes, where string represents the name of column in db
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 

// This block of code is used to undo magic quotes. 
if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
{ 
    function undo_magic_quotes_gpc(&$array) 
    { 
        foreach($array as &$value) 
        { 
            if(is_array($value)) 
            { 
                undo_magic_quotes_gpc($value); 
            } 
            else 
            { 
                $value = stripslashes($value); 
            } 
        } 
    } 
 
    undo_magic_quotes_gpc($_POST); 
    undo_magic_quotes_gpc($_GET); 
    undo_magic_quotes_gpc($_COOKIE); 
} 

// This tells the web browser that your content is encoded using UTF-8 
// and that it should submit content back to you using UTF-8 
header('Content-Type: text/html; charset=utf-8'); 

// This initializes a server session.
session_start(); 