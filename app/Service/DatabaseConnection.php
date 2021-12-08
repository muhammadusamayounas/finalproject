<?php

namespace App\Service;
use MongoDB\Client as mongo;
use Illuminate\Http\Request;
       
class DatabaseConnection
{     
function createconnection($table)   
 {
        $collection= (new mongo)->finalproject->$table;       
        return $collection;                      
        }   
 }
?>