<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Helpers\CacheHelper;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookResource;
use App\Jobs\NewBookNotificationJob;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;


class BookController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $books = CacheHelper::getCachedData('books', function () {
                return Book::with('authors')->get();
            }); 
            return $this->jsonResponse(BookResource::collection($books), 'Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, "Failed", 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $book = Book::create([
                'title'  => $request->title,
                'isbn' => $request->isbn,
                'edition' => $request->edition,
                'year' => $request->year,
                'price' => $request->price,
            ]);

            $book->authors()->attach($request->author_ids);
            
            NewBookNotificationJob::dispatch($book)->onQueue('email');

            DB::commit();
            return $this->jsonResponse(new BookResource($book), 'Store Success', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return $this->jsonResponse(null, 'Store Failed', 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        try {
            return $this->jsonResponse(new BookResource($book), 'Show Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Show Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookUpdateRequest $request, Book $book)
    {
        try {
            DB::beginTransaction();
            $newData = [];

            if (isset($request->title)) {
                $newData['title'] = $request->title;
            }
            if (isset($request->isbn)) {
                $newData['isbn'] = $request->isbn;
            }
            if (isset($request->edition)) {
                $newData['edition'] = $request->edition;
            }
            if (isset($request->year)) {
                $newData['year'] = $request->year;
            }
            if (isset($request->price)) {
                $newData['price'] = $request->price;
            }


            $book->update($newData);

            if (isset($request->author_ids)) {
                $book->authors()->sync($request->author_ids);
            }

            DB::commit();
            return $this->jsonResponse(new BookResource($book), 'Update Success', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return $this->jsonResponse(null, 'Update Failed', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            //de attach authors with book
            $book->authors()->detach();

            //delete book
            $book->delete();
            return $this->jsonResponse("", 'Delete Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Delete Failed', 500);
        }
    }
}
