## PostMan Collection

https://documenter.getpostman.com/view/34008000/2sA3JNZzdP

---

# Laravel App

Welcome to the Laravel App! This application provides a RESTful API for Library Management System.

## Setup Instructions

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
## Detailed Documentation

### Step 1: Book & Author CRUDs:

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
---------
### Step 2: Use Built in sanctum Authentication:
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

