<?php

namespace App\Models;


use App\Models\Traits\ActiveUserHelper;
use App\Models\Traits\LastActivedAtHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $weixin_openid
 * @property string|null $weixin_unionid
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $avatar
 * @property string|null $introduction
 * @property int $notification_count
 * @property string|null $last_actived_at
 * @property string|null $registration_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reply[] $replies
 * @property-read int|null $replies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Topic[] $topics
 * @property-read int|null $topics_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotificationCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWeixinOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWeixinUnionid($value)
 * @mixin \Eloquent
 * @property string|null $weixin_session_key
 * @property string|null $weapp_openid
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWeappOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWeixinSessionKey($value)
 */
class User extends Authenticatable implements MustVerifyEmailContract,JWTSubject
{
    use HasFactory,MustVerifyEmailTrait;
    use ActiveUserHelper;
    use LastActivedAtHelper;
    use HasRoles;
    use Notifiable{
        notify as protected laravelNotify;
    }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'introduction',
    'avatar',
    'phone',
    'weixin_openid',
    'weixin_unionid',
    'registration_id',
    'weixin_session_key',
    'weapp_openid'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'weixin_openid',
    'weixin_unionid'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

    public function notify($instance)
    {
        //如果通知的人是当前用户,就不必通知了！
        if($this->id==Auth::id()){
            return;
        }
        //只有数据库类型通知才需要提醒,直接发送Email或者其他都Pass
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');
        }
        $this->laravelNotify($instance);
    }

    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model): bool
    {
        return $this->id ===$model->user_id;
    }

    public function replies(){
        return $this->hasMany(Reply::class);
    }

    //通知状态设为已读,并清空未读消息。
    public function markAsRead(){
        $this->notification_count=0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }


    public function setPasswordAttribute($value){
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if(strlen($value)!=60){
            // 不等于 60，做密码加密处理
            $value=bcrypt($value);
        }
        $this->attributes['password']=$value;
    }

    public function setAvatarAttribute($path){
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if(!Str::startsWith($path,'http')){
            //拼接完整的URL
            $path=config('app.url')."/uploads/images/avatars/$path";
        }
        $this->attributes['avatar']=$path;
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
