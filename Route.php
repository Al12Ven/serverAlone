<?php
class Route
{
    public static function getRoute($route)
    {
        if ($route === '/serverAlone')
            require 'index.php';
        elseif ($route === '/serverAlone/get')
            require 'get.php';
        elseif ($route === '/serverAlone/post')
            require 'post.php';
        else
            require '404.php';
    }
}
