<?php

namespace App\Services;

use App\Exceptions\InvariantError;
use App\Exceptions\NotFoundError;
use App\Models\Product;
use App\Models\ProductStar;
use App\Models\UserRedeem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

interface GiftServiceInterface
{
    public function createGifts($attributes);
    public function getGifts($name, $priceFrom, $priceTo, $limit, $orderBy, $star);
    public function getGiftsById($id);
    public function updateGifts($request, $id);
    public function updateAttributeGifts($attributes, $id);
    public function deleteGifts($id);
    public function redeemGifts($id);
    public function redeemGiftsBulk($id);
    public function ratingGifts($id, $rating);
}

class GiftService implements GiftServiceInterface{

    public function getGifts($name, $priceFrom, $priceTo, $limit, $orderBy, $star){
        $gifts = Product::query();
        if($name)
            $gifts->where('name', 'like', '%'.$name.'%');

        if($priceFrom)
            $gifts->where('price', '>=', $priceFrom);

        if($priceTo)
            $gifts->where('price', '<=', $priceTo);

        if($star)
            $gifts->where('products.star', $star);

        if($orderBy)
            $gifts->orderBy('created_at', $orderBy);

        return $gifts->paginate($limit);
    }

    public function getGiftsById($id){
        $gift = Product::with('stars')->find($id);

        if(!$gift)
            throw new NotFoundError('Data gift tidak ada');

        return $gift;
    }

    public function createGifts($attributes){
        return Product::create($attributes);
    }

    public function updateGifts($request, $id)
    {
        $gift = Product::find($id);

        if (!$gift)
            throw new NotFoundError('Data gift tidak ditemukan');

        $gift->update($request->all());

        return $gift;
    }

    public function updateAttributeGifts($attributes, $id)
    {
        $gift = Product::find($id);

        if (!$gift)
            throw new NotFoundError('Data gift tidak ditemukan');

        $gift->update($attributes);

        return $gift;
    }

    public function deleteGifts($id)
    {
        $gift = Product::find($id);

        if (!$gift)
            throw new NotFoundError('Data gift tidak ditemukan');

        $gift->delete();

        return $gift;
    }

    public function redeemGifts($id){

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
                    return $gift;
                }catch(\Exception $e){
                    DB::rollBack();
                    throw new InvariantError($e->getMessage());
                }
            }

            throw new InvariantError('Stock gift tidak mencukupi');
        }

        throw new NotFoundError('Data gift tidak ada');
    }

    public function redeemGiftsBulk($id){
        try{
            $productId=[];
            DB::beginTransaction();
            foreach ($id as $item) {
                $productId[]=$item;
                $this->redeemGifts($item);
            }
            DB::commit();

            return $productId;
        }catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function ratingGifts($id, $rating){
        $user= Auth::guard('api')->user();

        $gift = Product::find($id);

        if ($gift) {
            $userHasRedeem = UserRedeem::where('user_id', $user->id)
                ->where('product_id', $id)
                ->first();

            $productStart=$this->_ratingGiftsExist($id, $user->id);
            if($productStart){

                $productStart->update([
                        'star' => round($rating)
                    ]);

                $this->_updateRatingOnProduct($id);

                return $gift;
            }else{
                if($userHasRedeem){
                    ProductStar::create([
                        'product_id' => $id,
                        'user_id' => $user->id,
                        'star' => round($rating)
                    ]);

                    $this->_updateRatingOnProduct($id);

                    return $gift;
                }
            }

            throw new InvariantError('Anda belum redeem gift ini');
        }

        throw new NotFoundError('Data gift tidak ditemukan');
    }

    private function _updateRatingOnProduct($id){
        $product = Product::find($id);
        $product->update([
            'star' => $this->_getRating($id)
        ]);
    }

    private function _getRating($id){
        $productStar = ProductStar::where('product_id', $id)->get();
        $totalRating = 0;
        foreach ($productStar as $star){
            $totalRating += $star->star;
        }

        return $totalRating / $productStar->count();
    }

    private function _ratingGiftsExist($id, $user_id){
        $rating = ProductStar::where('product_id', $id)
            ->where('user_id', $user_id)
            ->first();

        return $rating;
    }

}

;?>
