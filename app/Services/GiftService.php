<?php

namespace App\Services;

use App\Exceptions\InvariantError;
use App\Exceptions\NotFoundError;
use App\Models\Product;
use App\Models\UserRedeem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

interface GiftServiceInterface
{
    public function getGifts();
    public function redeemGift($id);
    // public function getGiftsById($id);
    // public function createGift($request);
    // public function updateGift($request, $id);
    // public function updateAttributeGift($request, $id);
    // public function deleteGift($id);
    // public function redeemGifts($request, $id);
    // public function ratingGifts($request, $id);
}

class GiftService implements GiftServiceInterface{
    public function getGifts(){
        return \App\Models\Product::with('productStar')->get();
    }

    public function redeemGift($id){

     $user= Auth::guard('api')->user();

     $gift = Product::find($id);

        if ($gift) {

            if ($gift->stock >= 1) {
                try{
                    DB::beginTransaction();

                    $gift->update([
                        'stock' => $gift->stock - 1
                    ]);

                    UserRedeem::create([
                        'user_id' => $user->id,
                        'product_id' => $gift->id
                    ]);

                    DB::commit();
                }catch(\Exception $e){
                    DB::rollBack();
                    throw new InvariantError($e->getMessage());
                }


                return $gift;
            }

            throw new InvariantError('Stock gift tidak mencukupi');
        }

        throw new NotFoundError('Data gift tidak ada');
    }
}

;?>
