<?php

//获取当前路由名称
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

//获取两百个字的字符
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}