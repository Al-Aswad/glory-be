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
    public function ratingGifts($id, $rating);
}

class GiftService implements GiftServiceInterface{

    public function getGifts($name, $priceFrom, $priceTo, $limit, $orderBy, $star){
        $gifts = Product::with('stars');
        if($name)
            $gifts->where('name', 'like', '%'.$name.'%');

        if($priceFrom)
            $gifts->where('price', '>=', $priceFrom);

        if($priceTo)
            $gifts->where('price', '<=', $priceTo);

        if($orderBy)
            $gifts->orderBy('created_at', $orderBy);
        if($star)
            $gifts->whereRelation('stars', function($query) use ($star){
                $query->where('star', $star)->count();
            });

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

    public function ratingGifts($id, $rating){
        $user= Auth::guard('api')->user();

        $gift = Product::find($id);

        if ($gift) {
            $userHasRedeem = UserRedeem::where('user_id', $user->id)
                ->where('product_id', $id)
                ->first();

            if($userHasRedeem){
                ProductStar::create([
                    'product_id' => $id,
                    'user_id' => $user->id,
                    'star' => round($rating)
                ]);

                return $gift;
            }

            throw new InvariantError('Anda belum redeem gift ini');
        }

        throw new NotFoundError('Data gift tidak ditemukan');
    }

}

;?>
