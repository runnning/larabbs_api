<?php
use Illuminate\Support\Str;

/**
 * 路由名转化为路由类名
*/
function route_class(): array|string|null
{
    return str_replace('.','-',Route::currentRouteName());
}

function category_nav_active($category_id): string
{
    return active_class((if_route('categories.show')&&if_route_param('category',$category_id)));
}

function make_excerpt($value, $length = 200): string
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return Str::limit($excerpt, $length);
}

function model_admin_link($title,$model): string
{
    return model_link($title,$model,'admin');
}

function model_link($title,$model,$prefix=''): string
{
    //获取数据模型的复数蛇形命名
    $model_name=model_plural_name($model);

    //初始化前缀
    $prefix=$prefix?"/$prefix/":'/';

    //使用站点URL拼接全量URL
    $url=config('app.url').$prefix.$model_name.'/'.$model->id;

    //拼接Html A标签，并返回
    return '<a href="'.$url.'" target="_blank">'.$title.'</a>';
}

/**
 * 模型复数命名
*/
function model_plural_name($model): string
{
    //从实体中获取完整类名,例如：App\Models\User
    $full_class_name=get_class($model);

    //获取基础类名,例如：传参 `App\Models\User` 会得到 `User`
    $class_name=class_basename($full_class_name);

    //蛇形命名,例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name=Str::snake($class_name);

    //获取子串的复数形式,例如：传参 `user` 会得到 `users`
    return Str::plural($snake_case_name);
}
