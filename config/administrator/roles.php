<?php
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
return [
    'title'=>'角色',
    'single'=>'角色',
    'model'=>Role::class,

    'permission'=> static function(){
        return Auth::user()?->can('manage_users');
    },
    'columns'=>[
        'id'=>[
          'title'=>'ID',
        ],
        'name'=>[
          'title'=>'角色'
        ],
        'permissions'=>[
          'title'=>'权限',
          'output'=> static function($value, $model){
                //加载权限模型
                $model->load('permissions');
                $result=[];
                foreach ($model->permissions as $permission){
                    $result[]=$permission->name;
                }
                return empty($result)?'N/A':implode(' | ',$result);
          },
            'sortable'=>false,
        ],
        'operation'=>[
            'title'=>'管理',
            'output'=> static function($value, $model){
                return $value;
            },
            'sortable'=>false,
        ],
    ],

    'edit_fields'=>[
        'name'=>[
            'title'=>'角色',
        ],
        'permissions'=>[
            'type'=>'relationship',
            'title'=>'权限',
            'name_field'=>'name',
        ],
    ],

    'filters'=>[
        'id'=>[
           'title'=>'ID',
        ],
        'name'=>[
            'title'=>'标识'
        ],
    ],

    // 新建和编辑时的表单验证规则
    'rules'=>[
        'name'=>'required|max:15|unique:roles,name'
    ],

    // 新建和编辑时的表单验证规则
    'messages'=>[
        'name.required'=>'角色不能为空',
        'name.unique'=>'角色已经存在',
    ]
];
