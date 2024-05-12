<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreBookReviewRequest;

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

   
}
