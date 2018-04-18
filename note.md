#####laravel代码生成器：

* 安装：composer require 'summerblue/generator:~0.5' --dev

* 使用：php artisan make:scaffold Projects --schema="name:string:index,description:text:nullable,subscriber_count:integer:unsigned:default(0)"

 * 作用：完成注册路由、新建模型、新建表单验证类、新建资源控制器以及所需视图文件等任务
  
* 备注：  
 
        unsigned() —— 设置不需要标识符（unsigned）
        default() —— 为字段添加默认值。
        nullable() —— 可为空
   

#####laravel数据填充：（生成用户）

*   UserFactory  …… 按规则生成一个数据用户并返回
*   UsersTableSeeder …… 接收生成的用户，并按需求插入数据库 
    
        // 获取 Faker 实例
               $faker = app(Faker\Generator::class);
               
        // 生成数据集合 times(10) 表示生成10条
               $users = factory(User::class)->times(10)->make()
               
*    php artisan db:seed --class=UsersTableSeeder  ……生成数据


#####laravel预加载

* 获取所有书籍及其作者的数据


    $books = App\Book::all();
    
    foreach ($books as $book) {
        echo $book->author->name;
    }
    
> 上方的循环会运行一次查找并取回所有数据表上的书籍，接着每本书会运行一次查找作者的操作。

> 因此，若存在着 25 本书，则循环就会执行 26 次查找：1 次是查找所有书籍，其它 25 次则是在查找每本书的作者。

* 查找时使用 with 方法来指定想要预加载的关联数据


    $books = App\Book::with('author')->get();
    
    foreach ($books as $book) {
        echo $book->author->name;
    }
    
>对于该操作则只会执行两条 SQL 语句：

    select * from books
    
    select * from authors where id in (1, 2, 3, 4, 5, ...)
    
* 有时你可能想要在单次操作中预加载多种不同的关联。要这么做，只需传递额外的参数至 with 方法即可：
   
    $books = App\Book::with('author', 'publisher')->get();



#####安装 Debugba

* 安装：

         composer require "barryvdh/laravel-debugbar:~3.1" --dev
         
* 开启：

        php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
        
        config/debugbar.php：    'enabled' => env('APP_DEBUG', false),
 

#####导航的 Active 状态

* composer require "hieu-le/active:~3.5"

>用法： 如果传参满足指定条件 ($condition) ，此函数将返回 $activeClass，否则返回 $inactiveClass。

    class = " {{ active_class((if_route('categories.show') && if_route_param('category', 1))) }}" 
    
|   

        if_route() - 判断当前对应的路由是否是指定的路由；
        if_route_param() - 判断当前的 url 有无指定的路由参数。
        if_query() - 判断指定的 GET 变量是否符合设置的值；
        if_uri() - 判断当前的 url 是否满足指定的 url；
        if_route_pattern() - 判断当前的路由是否包含指定的字符；
        if_uri_pattern() - 判断当前的 url 是否含有指定的字符；>


#####Guzzle 库是一套强大的 PHP HTTP 请求套件

* 安装：composer require "guzzlehttp/guzzle:~6.3"

* 使用：https://github.com/guzzle/guzzle


##### PinYin 基于 CC-CEDICT 词典的中文转拼音工具

* 安装：  composer require "overtrue/pinyin:~3.0"

* 使用：   https://github.com/overtrue/pinyin


    
##### 一个模块开发流程

* 控制器，模型，视图，Request <br><br>
    [数据生成：database/factories/XXXXFactory.php]   <br><br>
    [数据填充逻辑:database/seeds/XXXXTableSeeder.php]
    
    >模型:
    
    * 一个 [ 话题 ] 模型里面： " 获取该话题所有 回复 ->hasMany(‘回复模型’)"
            
             return $this->hasMany(Reply::class);

    * 一个 [ 话题 ] 模型里面：" 获取该话题所属 用户 ->belongsTo(‘用户模型’)  "
            
            return $this->belongsTo(User::class);
            
    > Request  App\Http\Requests\UserRequest.php:
    
            //自定义字段规则
            public function rules()
                {
                    return [
                        'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id()
                    ];
                }
                
             //自定义不满足时提示的信息    
            public function messages()
                {
                    return [
                        'name.unique' => '用户名已被占用，请重新填写',
                        'name.regex' => '用户名只支持中英文、数字、横杆和下划线。',
                    ];
                }
            
    > App\Http\Controllers\UserController.php
    
             //执行更新资料
             public function update(UserRequest $request)
             {
                $data = $request->all();   //  自动注入 Request\UserRequest 里面定义规则
             }
         
    > 验证不通过，在视图可以通过 $errors 变量获得错误信息，可以通过 count($errors) 判断是否产生错误
    
            @if (count($errors) > 0)
                    <h4>有错误发生：</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
            @endif
            
    
##### Laravel权限管理系统

*   Laravel-permission : https://github.com/spatie/laravel-permission

    >数据表各自的作用：
    
        * roles —— 角色的模型表；
        * permissions —— 权限的模型表；
        * model_has_roles —— 模型与角色的关联表，用户拥有什么角色在此表中定义，一个用户能拥有多个角色；
        * role_has_permissions —— 角色拥有的权限关联表，如管理员拥有查看后台的权限都是在此表定义，一个角色能拥有多个权限；
        * model_has_permissions —— 模型与权限关联表，一个模型能拥有多个权限。
        
        
##### 用户切换工具
    
* sudo-su  ： https://github.com/viacreative/sudo-su


* 安装
    > app/Providers/AppServiceProvider.php
    
        public function register()
            {
                if (app()->isLocal()) {
                    $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
                }
            }
            
* 发布资源文件
    > php artisan vendor:publish --provider="VIACreative\SudoSu\ServiceProvider"
    
        会生成：
        
        /public/sudo-su 前端 CSS 资源存放文件夹；
        config/sudosu.php 配置信息文件；
        
* 修改配置文件：
    > config/sudosu.php
    
        <?php
        
        return [
        
            // 允许使用的顶级域名
            'allowed_tlds' => ['dev', 'local', 'test'],
        
            // 用户模型
            'user_model' => App\Models\User::class
        
        ];
        
    ######Sudosu 为了避免开发者在生产环境下误开启此功能，在配置选项 allowed_tlds 里做了域名后缀的限制，tld 为 Top Level Domain 的简写。此处因我们的项目域名为 larabbs.test，故将 test 域名后缀添加到 allowed_tlds 数组中。
        
        
*  模板植入
    > resources/views/layouts/app.blade.php
    
           @if (app()->isLocal())
               @include('sudosu::user-selector')
           @endif
       
           <!-- Scripts -->
           <script src="{{ asset('js/app.js') }}"></script>
           @yield('scripts')
           
#####  Laravel Administrator 管理后台

       github: https://github.com/summerblue/administrator

* 安装：
    > composer require "summerblue/administrator:~1.1"
    
* 发布资源文件
    >  php artisan vendor:publish --provider="Frozennode\Administrator\AdministratorServiceProvider"
    
        会生成：
            config/administrator.php —— 配置信息。
            public/packages/summerblue/administrator —— 前端资源文件，这是用来做页面渲染的。
    
* 创建必要的文件夹
    > Administrator 会监测 settings_config_path 和 model_config_path 选项目录是否能正常访问，否则会报错，故我们先使用以下命令新建目录
        
            config/administrator 
            config/administrator/settings
            
       
* 注意：要使用后台管理功能
    
    1： 通过 Composer 安装：
    
    > composer require "spatie/laravel-permission:~2.7"
    
    2：生成数据库迁移文件：
    
    > php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
    
    3: 生成配置信息：配置信息存放于 config/permission.php 
    
    > php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
    
    
    
    
    
##### 微信授权


  1:获取授权码
	
    https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx543xxxxxxxx3c82&redirect_uri=http://bbs.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
    
            
           获取结果: http://...../?code=071Zce2e1zCCor0YnE2e1B3W1e1Zce2v&state=STATE

  2:获取access_token openid
	
		https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx543xxxxxxxx3c82&secret=d1216ac7axxxxxxxxxxc2c98d2831a37&code=001xcQ1s0cRptd1YumYr0epS1s0xcQ1f&grant_type=authorization_code
            
         获取结果:{
                     "access_token":"8_MJCsgvCf9bJf_lxzC-GW5dZD59zEnhZZy9tNcebHDNv8KC5pQUsqCx6VJ4_qGKsbpnEWL-IkC2g",
                     "expires_in":7200,
                     "refresh_token":"8_-V79fxCrY3zYn8HtyiVim_UKh7GOYjVJq6OGraFyiEwPYGnKyvGu_V54Yq2D4SOA5vhNwUb_cnLKjkmLgtw4LA",
                     "openid":"oeWizwSdYbfNiXkhUtww",
                     "scope":"snsapi_userinfo"
                 }

  3：获取个人信息

		https://api.weixin.qq.com/sns/userinfo?access_token=8_MJCsgvCf9bJf_6qYYgsvZESalxzC-GW5dZD59zEnhZZy9tNcebHDNv8KC5pQUsqCx6VJ4_qGKsbpnEWL-IkC2g&openid=oeWizwSdYbf1sbQsY0ONiXkhUtww&lang=zh_CN

            
            
        获取结果:{
                        "openid":"oeWizwS0ONiXkhUtww",
                        "nickname":"",
                        "sex":1,
                        "language":"en",
                        "city":"",
                        "province":"",
                        "country":"中国",
                        "headimgurl":"http://thirdwx.qlogo.cn/mmopen/vi_32/n3E9esUk5rran4d7dVibcibTL9wb41LEich5ddhB4W8nCUvlYBRuicXBAjw8qfbdeKzCEvoa2BXru0A/",
                        "privilege":[]
                 }