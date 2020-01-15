<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

/*
Route::rule('admin',function(){
    return '404 Not Found';
});
*/

Route::rule('/category/[:name]$','content/Index/category');
Route::rule('/member/login$','member/User/login');
Route::rule('/member/register$','member/User/register');
Route::rule('/member/logout$','member/User/logout');
Route::rule('/member/code$','member/User/code');
Route::rule('/member/getpwd','member/User/getpwd');
Route::rule('/member/sms','member/User/sms');
Route::rule('/member$','member/Index/index');
Route::rule('/member/info/lists/:name','member/Info/lists');
Route::rule('/news$','content/Index/news');
Route::rule('/news/details/[:id]$','content/Info/news_details');
Route::rule('/info/details/[:itemid]$','content/Info/details');
Route::rule('/search$','content/index/search');
Route::rule('/search/list$','content/index/search_list');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],


	


];
