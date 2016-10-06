# laratwitter
Aplicativo de exemplo para o mini curso de Laravel do LAIS-HUOL.

Basicamente, um mini clone do twitter com funcionalidades básicas (logar, registrar, postar, seguir...).

Feito em Laravel 5.3

Para referência, abaixo estão os passos seguidos para o desenvolvimento do app.

### Criação do Projeto e Configuração Inicial

```
homestead up
homestead ssh
cd Code
laravel new twitter
```
O laravel vai criar o proketo, instalar as dependências e configurar usando o arquivo .env

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

### Tabelas

| users          | posts      | followers        |
| -------------- | ---------- | ---------------- |
| id             | id         | id               |
| name           | user_id    | user_id          |
| username       | content    | follower_user_id |
| email          | created_at | created_at       |
| password       | updated_at | updated_at       |
| remember_token |            |                  |
| created_at     |            |                  |
| updated_at     |            |                  |


### Models - User e Post

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

### Autenticação
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

### O controller PostController
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

### Rotas

- adicionar uma rota que mostre os posts ao arquivo routes/web.php
```
Route::get('/post', 'PostController@index');
```

### Tinker
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

### Segurança
- adicionar middleware auth ao ```__construct()``` do PostController, e dar uma breve explicação
```
public function __construct() {
    $this->middleware('auth');
}
```

# Views
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
                            <textarea class="form-control" name="content" rows="2" placeholder="Your complaint goes here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-default pull-right clearfix" name="button">Post</button>
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

# Relacionamentos (ORM)
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

# Formulários, Validação, ORM
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
