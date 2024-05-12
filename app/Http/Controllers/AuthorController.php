<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\AuthorResource;
use App\Http\Requests\AuthorStoreRequest;
use App\Http\Requests\AuthorUpdateRequest;
use App\Http\Helpers\CacheHelper;


class AuthorController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $authors = CacheHelper::getCachedData('authors', function () {
                return Author::with('books')->get();
            });    
            return $this->jsonResponse(AuthorResource::collection($authors), 'Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, "Failed", 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $author = Author::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            $author->books()->attach($request->book_ids);

            DB::commit();
            return $this->jsonResponse(new AuthorResource($author), 'Store Success', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return $this->jsonResponse(null, 'Store Failed', 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        try {
            return $this->jsonResponse(new AuthorResource($author), 'Show Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Show Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorUpdateRequest $request, Author $author)
    {
        try {
            DB::beginTransaction();
            $newData = [];

            if (isset($request->first_name)) {
                $newData['first_name'] = $request->first_name;
            }
            if (isset($request->last_name)) {
                $newData['last_name'] = $request->last_name;
            }


            $author->update($newData);

            if (isset($request->book_ids)) {
                $author->books()->sync($request->book_ids);
            }

            DB::commit();
            return $this->jsonResponse(new AuthorResource($author), 'Update Success', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return $this->jsonResponse(null, 'Update Failed', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        try {
            //de attach books with author
            $author->books()->detach();

            //delete author
            $author->delete();
            return $this->jsonResponse("", 'Delete Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Delete Failed', 500);
        }
    }
}
