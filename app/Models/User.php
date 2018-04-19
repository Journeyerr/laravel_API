<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use Traits\LastActivedAtHelper;

    /*
     *user 模型需要继承
     * Tymon\JWTAuth\Contracts\JWTSubject 接口
     * 并实现接口的两个方法 getJWTIdentifier() 和 getJWTCustomClaims()。
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar', 'phone','weixin_openid', 'weixin_unionid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //获取 用户 所有话题
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    // 获取  用户  所有的回复
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    //将消息通知清零
    public function clearNotification()
    {
        $this->notification_count  = 0;
        $this->save();
    }


    /*
     *在 Eloquent 模型实例中获取或设置某些属性值的时候，访问器和修改器允许你对 Eloquent 属性值进行格式化。
     * 有两种方法可以修改 Eloquent 模型属性的值，一种是『访问器』，另一种是『修改器』。
     *访问器和修改器最大的区别是『发生修改的时机』，访问器是 访问属性时 修改，修改器是在 写入数据库前 修改。
     *
     *在我们的密码加密的场景里，我们会使用修改器在密码即将入库前，对其进行加密。
     * 命名规范是 set{属性的驼峰式命名}Attribute，
     * 当我们给属性赋值时，如 $user->password = 'password'，该修改器将被自动调用：
     *
     * 如果更改邮箱： setEmailAttribute($value) 会自动被调用
     **/

    //因为后台会明文存储密码，修改密码时会自动调用此方法，如果明文则加密
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }




}
