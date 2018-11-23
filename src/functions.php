<?php

if( !function_exists('class_method') ){

    /**
     * @param $classMethod
     * @return array
     * @throws ErrorException
     */
    function class_method($classMethod)
    {
        if( preg_match('/^([\\\d\w_]+)@([\d\w_]+)$/', $classMethod, $match) ){

            if( class_exists($match[1]) ){

                $instance = new $match[1];

                if( \method_exists($instance, $match[2]) ){
                    return [$instance, $match[2]];
                }
            }
        }

        throw new \ErrorException("Cannot resolve class method: {$classMethod}");
    }
}