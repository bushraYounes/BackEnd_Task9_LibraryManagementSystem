## PostMan Collection

https://documenter.getpostman.com/view/34008000/2sA3JNZzdP

---

# Laravel App

This application provides a RESTful API for Library Management System.

# <span style="color:green">## Setup Instructions</span>

```
git clone https://github.com/bushraYounes/BackEnd_Task9_LibraryManagementSystem.git

composer install

composer require laravel/sanctum

php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

php artisan migrate

php artisan db:seed --class=UserSeeder

php artisan serve
```

------------------------------------------------------------------------------------------
# <span style="color:green">Detailed Documentation</span>

## <span style="color:red">I created the first version of this Documentation using "Obsidian note-taking"</span>

### <span style="color:blue">Step 1: Book & Author CRUDs:</span>

we have Book and Author models with many-to-many relation, to implement the RESTFull APIs:
first: we created migration, model, controller by this command:
```
php artisan make:model Book -mcr --api
php artisan make:model Author -mcr --api
```
then create the third connection table:
```
php artisan make:migration create_author_book_table --create=author_book
```
define columns in each migration file, and add the foreign keys in the third table:
then in models files:
Book Model:
we defined the table columns within `fillable` array, then define this function to get all attached authors to this book:
```
public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
```

Author Model:
we defined the table columns within `fillable` array, then define this function to get all attached books to this author:
```
public function books()
    {
        return $this->belongsToMany(Book::class);
    }
```
then define few  `Accessors and Mutators`:

for example:
```
first_name:  bushra -> Bushra
last_name: younes -> Younes

create new atthribute full name: returns Bushra Younes
```
the implementation functions:
```
getFirstNameAttribute($value)
 
setFirstNameAttribute($value)
 
getLastNameAttribute($value)

setLastNameAttribute($value)
 
getFullNameAttribute()
 
```

then the controllers:
before dive into the CRUD implementation, we need few organizational files:

- we created Trait: `ResponseTrait` to define the main responded json format.

for Book CRUD:
we created customized resource for Book: `BookResource`
and make validation for requests for store and update methods using:
- `BookStoreRequest`
- `BookUpdateRequest`

then we implemented the main CRUD operations.

The same for Author CRUD:
we created customized resource for Book: `AuthorResource`
and make validation for requests for store and update methods using:
- `AuthorStoreRequest`
- `AuthorUpdateRequest`

then we implemented the main CRUD operations.
finally we defined the api routes:
```
Route::get('authors', [AuthorController::class, 'index']);
Route::get('authors/{author}', [AuthorController::class, 'show']);
Route::post('authors', [AuthorController::class, 'store']);
Route::put('authors/{author}', [AuthorController::class, 'update']);
Route::delete('authors/{author}', [AuthorController::class, 'destroy']);


Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::post('books', [BookController::class, 'store']);
Route::put('books/{book}', [BookController::class, 'update']);
Route::delete('books/{book}', [BookController::class, 'destroy']);
```


> [!NOTE] Authorisation
> We will protect the apis with Authorisation middleware later in code.

---------
### <span style="color:blue">Step 2: Use Built in sanctum Authentication:</span>
first apply these commands:
```
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```
then you should add Sanctum's middleware to your `api` middleware group within your application's `app/Http/Kernel.php` file:
```
'api' => [
\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
],
```

then define new controller `UserController`:

`php artisan make:controller UserController`

then define the login and register functions.
check this Link for more details:
https://www.dbestech.com/tutorials/laravel-sanctum-install-and-login-and-register
finally add routes for it:

```
Route::post('/user/register', [UserController::class, 'createUser']);
Route::post('/user/login', [UserController::class, 'loginUser']);

Route::middleware("auth:sanctum")->group(function () {

//we will add our future apis here
});
```
--------------------------------------
### <span style="color:blue">Step 3: Define morph Relations</span>

we created Reviews table where there is a morph relation between reviews books authors
and to identify the user who add the review we created one to many relation between user model and review model.

first we create migration file with model:
```
php artisan make:model Review -m 
```
and add this to identify it as morph:
`$table->morphs('reviewable');`

then mention columns names in `fillable` array in Review Model.
and define the morph method:
```
public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
```


and define the user method that refer to the user that add this review:
```
public function user()
    {
        return $this->belongsTo(User::class);
    }
```


finally in the app/Providers/AppServiceProvider.php file
we put this :

```
 public function boot(): void
    {
        Relation::enforceMorphMap([
            'book' => 'App\Models\Book',
            'author' => 'App\Models\Author',
        ]);
    }
```


connect between the reviews and the user who add them
we add this to reviews migration file
`$table->foreignId('user_id')->constrained();`

and `user_id` to `$fillable[]` in Review Model

add this function to Review model
```
  public function user()
    {
        return $this->belongsTo(User::class);
    }
```

add this function to User Model

```
  public function reviews(){

        return $this->hasMany(Review::class);

    }
```


to make the morph relation with Book and Author:

put this method in Book model:
```
 public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
```


put this method in Author model:
```
 public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
```

now we are ready to implement the Reviews CRUD
create ReviewController
as we did previously in the BookController and AuthorController:

and we will need three form requests:
- ReviewUpdateRequest
- StoreAuthorReviewRequest
- StoreBookReviewRequest

and also `ReviewResource`

in this ReviewController we will implement these functions:
- storeBookReview
- storeAuthorReview
- index
- update
- destroy

then define routes:

```
 Route::post('/reviews/books/{book_id}', [ReviewController::class, 'storeBookReview']);
 Route::post('/reviews/authors/{author_id}', [ReviewController::class, 'storeAuthorReview']);
 Route::put('/reviews/{id}', [ReviewController::class, 'update']);
 Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
 Route::get('/reviews', [ReviewController::class, 'index']);
```

> [!NOTE] Authorisation
> We will protect the apis with Authorisation middleware later in code.


----------------------------------------------
### Step 4: user seeder
create seeder file :
```
php artisan db:seed --class=UserSeeder
```

define the seed, 
```
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345'),
        ]);
```
------------------------------------------
## Step 5: Cache and Helpers
we should cache for saving:
- list of all Books
- list of all Authors

and we will use helper functions in the implementation:
we created CacheHelper class with `getCachedData` method then we use it in each of AuthorController and BookController.

------------------------------------------------------------

## Step 6: Authorisation and middleware: 

we need to protect our apis, and only allow the authenticated and authorised users to access them:
first there are few apis that the user should be logged in the system to to be able to access the,
and other apis should be allowed to be accessed by admin.
to implement the previous rules we need the pre defined middlewares provided by `sanctum` and we will create another middleware `isAdmin` to check if the logged in user is admin or not.

#### isAdmin middleware:
first create this middleware:
`php artisan make:middleware AdminMiddleware`

define the `handel` function where we check if the logged in user is admin or not.

then add this middleware to `app/Http/Kernel.php` file,  within `$routeMiddleware`  to apply it selectively to specific routes:

```
protected $routeMiddleware = [
    'isAdmin' => \App\Http\Middleware\AdminMiddleware`::class,
];
```

##### Finally the Protected Routes with middlewares:

```
Route::post('/user/register', [UserController::class, 'createUser']);
Route::post('/user/login', [UserController::class, 'loginUser']);

  
Route::middleware("auth:sanctum")->group(function () {

    Route::post('/reviews/books/{book_id}', [ReviewController::class, 'storeBookReview']);
    Route::post('/reviews/authors/{author_id}', [ReviewController::class, 'storeAuthorReview']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);


    Route::middleware('isAdmin')->group(function () {
        Route::post('authors', [AuthorController::class, 'store']);
        Route::put('authors/{author}', [AuthorController::class, 'update']);
        Route::delete('authors/{author}', [AuthorController::class, 'destroy']);
  

        Route::post('books', [BookController::class, 'store']);
        Route::put('books/{book}', [BookController::class, 'update']);
        Route::delete('books/{book}', [BookController::class, 'destroy']);
    });
});

  
Route::get('authors', [AuthorController::class, 'index']);
Route::get('authors/{author}', [AuthorController::class, 'show']);


Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);

Route::get('/reviews', [ReviewController::class, 'index']);
```

----------------------------------------------------------------------

## Step 7: