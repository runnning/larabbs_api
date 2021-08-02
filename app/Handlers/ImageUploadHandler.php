<?php
/**
 *User:ywn
 *Date:2021/7/16
 *Time:13:37
 */

namespace App\Handlers;

Use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageUploadHandler
{
    //只允许以下后缀名图片上传
    protected $allowed_ext=['png','jpg','gif','jpeg'];

    public function save($file,$floder,$file_prefix,$max_width=false){
        $floder_name="uploads/images/$floder/".date("Ym/d",time());

        $upload_path=public_path().'/'.$floder_name;

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension=strtolower($file->getClientOriginalExtension())?:'png';

        //拼接文件名，前缀可以是相关模型的ID
        //如:1_1493521050_7BVc9v9ujP.png
        $filename=$file_prefix.'_'.time().'_'.Str::random(10).'.'.$extension;

        //如果上传的不是图片终止操作
        if(!in_array($extension,$this->allowed_ext)){
            return false;
        }

        //将图片移动到目标存储路径中
        $file->move($upload_path,$filename);

        //如果限制了图片宽度,就进行裁剪
        if($max_width&&$extension!='gif'){
            $this->reduceSize($upload_path.'/'.$filename,$max_width);
        }

        return [
            'path'=>config('app.url')."/$floder_name/$filename"
        ];
    }

    public function reduceSize($file_path,$max_width){
        //先实例化,传参是文件的磁盘物理路径
        $image=Image::make($file_path);

        //大小调整
        $image->resize($max_width,null,function ($constraint){
            //设置宽度是$max_width,高度等比例缩放
            $constraint->aspectRatio();


            //防止截图时图片尺寸变大
            $constraint->upsize();
        });
        //对图片修改后进行保存
        $image->save();
    }
}
