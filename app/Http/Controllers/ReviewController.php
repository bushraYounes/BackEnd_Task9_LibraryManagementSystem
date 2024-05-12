<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\ReviewUpdateRequest;
use App\Http\Requests\StoreBookReviewRequest;
use App\Http\Requests\StoreAuthorReviewRequest;
use App\Http\Controllers\UserController;

class ReviewController extends Controller
{
    use ResponseTrait;
    public function storeBookReview(StoreBookReviewRequest $request, $bookId)
    {
        try {
            $review = Review::create([
                'review' => $request->review,
                'reviewable_type' => 'book',
                'reviewable_id' => $bookId,
                'user_id' => Auth::id(),
            ]);
            return $this->jsonResponse(new ReviewResource($review), 'Store Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Store Failed', 200);
        }
    }

    public function storeAuthorReview(StoreAuthorReviewRequest $request, $authorId)
    {
        try {
            $review = Review::create([
                'review' => $request->review,
                'reviewable_type' => 'author',
                'reviewable_id' => $authorId,
                'user_id' => Auth::id(),
            ]);
            return $this->jsonResponse(new ReviewResource($review), 'Store Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'Store Failed', 200);
        }
    }

    public function index()
    {
        try {
            $reviews = Review::all();
            return $this->jsonResponse(ReviewResource::collection($reviews), 'Success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->jsonResponse(null, 'index Failed', 200);
        }
    }

    public function update(ReviewUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $review = Review::findOrFail($id);
            $userController = new UserController();
            $user = Auth::user();
            // Check if the authenticated user is the original reviewer or an admin
            if ($review->user_id == $user->id || $userController->isAdmin($user)) {
                $newData = [];

                if (isset($request->review)) {
                    $newData['review'] = $request->review;
                }

                $review->update($newData);
                DB::commit();
                return $this->jsonResponse(new ReviewResource($review), 'Update Success', 200);
            } else {
                return $this->jsonResponse(null, 'Unauthorized', 401);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return $this->jsonResponse(null, 'update Failed', 200);
        }
    }
}
