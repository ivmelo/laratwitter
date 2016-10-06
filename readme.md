# laratwitter
Aplicativo de exemplo para o mini curso de Laravel do LAIS-HUOL.

Basicamente, um mini clone do twitter com funcionalidades básicas (logar, registrar, postar, seguir...).

Feito em Laravel 5.3

Para referência, abaixo estão os passos seguidos para o desenvolvimento do app.

## Criação do Projeto e Configuração Inicial

```
homestead up
homestead ssh
cd Code
laravel new twitter
```
O laravel vai criar o projeto, instalar as dependências e configurar usando o arquivo .env

- editar para apontar para o app
```
~/homestead/Homestead.yaml
/etc/routes
```

- criar banco de dados laratwitter
- editar e mostrar arquivo .env
- adicionar creadenciais do banco de dados ao .env

- rodar homestear provision para atualizar as configurações
```
homestead provision
```
- visitar laratwitter.app e ver o resultado
- mostrar o arquivo de rotas e fazer alguma alteração para mostrar resultado

## Tabelas

| users          | posts      | followers        |
| -------------- | ---------- | ---------------- |
| id             | id         | id               |
| name           | user_id    | user_id          |
| username       | content    | follower_user_id |
| email          |            |                  |
| password       |            |                  |

Lembrar do created_at, updated_at e remember_token na tabela users.

## Models - User e Post

- mostrar model user e criar o de post
User (already present)
Post

- adicionar username na migration users
```
$table->string('username')->unique();
```
- adicionar ```username``` ao array fillable no model User


- criar model Post e migration
```
php artisan make:model Post --migration
```

- adicionar colunas content e user_id na migration
```
$table->text('content');
$table->integer('user_id');
```

- adicionar content e user_id ao fillable do Post
```
protected $fillable = ['content', 'user_id'];
```

- migrar
```
php artisan migrate
```

## Autenticação
- executar comando de scaffold de autenticação
```
php artisan make:auth
```

- adicionar validação de username ao RegisterController
```
'username' => 'required|min:5|unique:users'
```
- adicionar username ao User::create do RegisterControler
```
'username' => $data['username']
```

- adicionar username ao register.blade.php
```
<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
    <label for="username" class="col-md-4 control-label">Username</label>

    <div class="col-md-6">
        <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required>

        @if ($errors->has('username'))
            <span class="help-block">
                <strong>{{ $errors->first('username') }}</strong>
            </span>
        @endif
    </div>
</div>
```

- testar login e registro (registrar um usuário)
- testar logout
- alterar nome do app (padrão é 'Laravel')

- remover nome "laravel" e por "laratwitter" no config/app.php
```
'name' => 'laratwitter',
```

## O controller PostController
- criar PostController
```
php artisan make:controller PostController --resource
```

- mostrar estrutura do controller e retornar todos os posts
- alterar o controller para pegar todos os posts e mostrar na tela
```
use App\Post;
```

```
$posts = Post::all();
return response()->json($posts, 200);
```

## Rotas

- adicionar uma rota que mostre os posts ao arquivo routes/web.php
```
Route::get('/post', 'PostController@index');
```

## Tinker
- breve explicação sobre o tinker
- criar dois posts usando o tinker
```
php artisan tinker

$post = new App\Post();
$post->content = 'Participando do tutorial de Laravel do LAIS HUOL';
$post->user_id = 1;
$post->save();

$post2 = new App\Post;
$post2->content = 'Quero cafeeeeeeeeeeee...';
$post2->user_id = 1;
$post2->save();

App\Post::all();

exit
```
- mostrar posts sendo retornados no /post

## Segurança
- adicionar middleware auth ao ```__construct()``` do PostController, e dar uma breve explicação
```
public function __construct() {
    $this->middleware('auth');
}
```

- redirecionar para / ao fazer login (Alterar LoginController & Register Controller)
```
protected $redirectTo = '/';
```

## Views
- mostrar layouts/app.blade.php e explicar
- criar diretório views/posts
- arquivo views/posts/index.blade.php
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <form action="{{ url('post') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <textarea class="form-control" name="content" rows="2" placeholder="Your complaint goes here...">{{ old('content') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm pull-right clearfix" name="button">Post</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>

                    <div class="panel-body">
                        @if ($posts->count() > 1)
                            @foreach ($posts as $post)
                                <div class="media">
                                    <div class="media-left media-middle">
                                        <a href="{{ url('u/' . $post->user->username ) }}">
                                            <img class="media-object" src="{{ $post->user->gravatar_url }}" alt="foto de perfil de {{ $post->user->name }}">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="media-heading">{{ $post->content }}</h3>
                                        <p>
                                            <a href="{{ url('post/' . $post->id ) }}">{{ $post->created_at->diffForHumans() }}</a> by <a href="{{ url('u/' . $post->user->username ) }}">{{ '@' . $post->user->username }}</a>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h2>No posts yet.</h2>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

- alterar o index do PostController para retornar a view
```
// return response()->json($posts, 200);
return view('posts.index', compact('posts'));
```

- adicionar acessor no model User para pegar a imagem do gravatar
```
/**
 * Get the user's gravatar picture url.
 *
 * @param  string  $value
 * @return string
 */
public function getGravatarUrlAttribute()
{
    return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email));
}
```

- definir a rota / para ver os posts
```
Route::get('/', 'PostController@index');
```

## Relacionamentos (ORM)
- mostrar relacionamentos user <-> post e adicionar no model user
```
public function posts() {
    return $this->hasMany('App\Post');
}
```

- adicionar no model post
```
public function user() {
    return $this->belongsTo('App\User');
}
```

## Formulários, Validação, ORM
- criar rota POST para /post
```
Route::post('/post', 'PostController@store');
```

- adicionar validacao ao controller
```
$this->validate($request, [
    'content' => 'required|min:3|max:140', // limite de 140 caracteres
]);
```

- criar e armazenar tweet
```
use Auth;
```

```
$post = new Post();
$post->content = $request->content;

Auth::user()->posts()->save($post);

return redirect()->back();
```

- mostrar funcionando
- alterar ordem de exibição dos tweets (mais recente primeiro)
```
$posts = Post::with('user')->orderby('updated_at', 'desc')->get();
```

## Perfil de Usuário
- criar controller de usuário
```
php artisan make:controller UserController --resource
```

- criar rota para mostrar usuário
```
Route::get('/u/{username}', 'UserController@show');
```

- pegar usuário por username e mostrar na tela
```
public function show($username)
{
    $user = User::where('username', '=', $username)->firstOrFail();
    return view('users.show', compact('user'));
}
```

- criar view para mostrar detalhes de usuário (views/users/show.blade.php)
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">

                        <div class="row">
                            <div class="col-md-2">
                                <img src="{{ $user->gravatar_url }}" alt="Foto de perfil de {{ $user->name }}" class="img-circle" style="width: 100%"/>
                            </div>
                            <div class="col-md-8">
                                <h2 style="margin-top: 0px">{{ $user->name }}</h2>
                                <h3 style="margin-top: 0px">{{ '@' . $user->username }}</h3>
                                <p style="margin-top: 0px">Member since {{ $user->created_at->format('d/m/y') }}.</p>
                            </div>
                            <div class="col-md-2">
                                @if ($user->id != Auth::user()->id)
                                <form action="{{ url('u/' . $user->id . '/follow') }}" method="post">
                                    {{ csrf_field() }}
                                    <button type="submit" name="button" class="btn btn-block btn-primary">Follow</button>
                                </form>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="panel-body">
                        @if ($user->posts->count() > 1)
                            @foreach ($user->posts as $post)
                                <div class="media">
                                    <div class="media-left media-middle">
                                        <a href="{{ url('u/' . $post->user->username ) }}">
                                            <img class="media-object" src="{{ $post->user->gravatar_url }}" alt="foto de perfil de {{ $post->user->name }}">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="media-heading">{{ $post->content }}</h3>
                                        <p>
                                            <a href="{{ url('post/' . $post->id ) }}">{{ $post->created_at->diffForHumans() }}</a> by <a href="{{ url('u/' . $post->user->username ) }}">{{ '@' . $post->user->username }}</a>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h2 class="text-center">No posts yet.</h2>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

### Relação Muitos para Muitos e Botão de Seguir
- criar tabela followers
```
php artisan make:migration create_followers_table
```

- adicionar campos necessários a migration
```
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::create('followers', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        $table->integer('follower_user_id');
        $table->timestamps();
    });
}

/**
 * Reverse the migrations.
 *
 * @return void
 */
public function down()
{
    Schema::drop('followers');
}
```

- executar a migração
```
php artisan migrate
```

- definir relacionamento many to many no model user
```
/**
 * Get this user's followers.
 *
 * @param  string  $value
 * @return BelongsToMany
 */
public function followers() {
    return $this->belongsToMany('App\User', 'followers', 'user_id', 'follower_user_id');
}
```

- adicionar a rota para fazer o post request para follow
```
Route::post('u/{user_id}/follow', 'UserController@follow');
```

- adiciona método para saber se um usário segue o outro no model User
```
/**
 * Return wether the user is a follower or not.
 *
 * @param  string  $value
 * @return BelongsToMany
 */
public function isFollower($id) {
    if ($this->followers->where('id', '=', $id)->count() > 0) {
        return true;
    }
    return false;
}
```

- adiciona método follow no UserController
```
$user_to_follow = User::findOrFail($user_id);

if ($user_to_follow->isFollower(Auth::user()->id)) {
    return redirect()->back();
}

$user_to_follow->followers()->attach(Auth::user());

return redirect()->back();
```

- adiciona botão com condicional para seguir, e dar unfollow na view (users/show.blade.php), além de verificar se o usuário está logado
```
@if (Auth::user())
    @if ($user->id != Auth::user()->id)
        @if ($user->isFollower(Auth::user()->id))
            <form action="{{ url('u/' . $user->id . '/unfollow') }}" method="post">
                {{ csrf_field() }}
                <button type="submit" name="button" class="btn btn-block btn-primary">Following</button>
            </form>
        @else
            <form action="{{ url('u/' . $user->id . '/follow') }}" method="post">
                {{ csrf_field() }}
                <button type="submit" name="button" class="btn btn-block btn-primary">Follow</button>
            </form>
        @endif
    @endif
@endif
```

## Botão de Unfollow

- cria rota e implementa ação de unfollow no UserController
```
Route::post('u/{user_id}/unfollow', 'UserController@unfollow');
```

```
/**
 * Unfollow a user.
 *
 * @param  int  $user_id
 * @return \Illuminate\Http\Response
 */
public function unfollow($user_id)
{
    $user_to_unfollow = User::findOrFail($user_id);

    if ($user_to_unfollow->isFollower(Auth::user()->id)) {
        $user_to_unfollow->followers()->detach(Auth::user());

        return redirect()->back();
    }

    return redirect()->back();
}
```

- adiciona contagem de seguidores a página do usário
```
<h4 style="margin-top: 0px">{{ $user->followers->count() }} {{ str_plural('follower', $user->followers->count())}}. Member since {{ $user->created_at->format('d/m/y') }}.</h4>
```

## Ajustes Gerais e Finalização
- ver apenas posts de quem você está seguindo

- criar método para pegar quem você segue
```
/**
 * Get the users who are being followed by the current.
 *
 * @param  string  $value
 * @return BelongsToMany
 */
public function following() {
    return $this->belongsToMany('App\User', 'followers', 'follower_user_id', 'user_id');
}
```

- atualizar index do PostController
```
public function index()
{
    $users = [];

    array_push($users, Auth::user()->id);

    foreach (Auth::user()->following as $user) {
        array_push($users, $user->id);
    }

    $posts = Post::with('user')->whereIn('user_id', $users)
        ->orderby('updated_at', 'desc')->get();


    return view('posts.index', compact('posts'));
}
```

## The End!
- Tutorial por: Ivanilson Melo.
- Apoio: Duarte Fernandes, Jean Jar, Daniel Souza.
