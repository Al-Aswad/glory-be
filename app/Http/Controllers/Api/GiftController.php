<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ClientError;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\RatingRequest;
use App\Models\Product;
use App\Models\ProductStar;
use App\Models\UserRedeem;
use App\Services\GiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Termwind\Components\Dd;

class GiftController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new GiftService();
    }

    public function getGifts(Request $request)
    {
        $gifts = Product::get();

        return ResponseFormatter::success(
            $gifts,
            'Data gift berhasil diambil'
        );
    }

    public function getGiftsById($id)
    {
        $gift = Product::find($id);

        if ($gift)
            return ResponseFormatter::success($gift, 'Data gift berhasil diambil');

        return ResponseFormatter::error(
            null,
            'Data gift tidak ada',
            404
        );
    }

    public function createGift(ProductRequest $request)
    {
        $gift = Product::create($request->all());

        if ($gift)
            return ResponseFormatter::success($gift, 'Data gift berhasil ditambahkan', 201);

        return ResponseFormatter::error(
            null,
            'Data gift gagal ditambahkan',
            404
        );
    }

    public function updateGift(ProductRequest $request, $id)
    {
        $gift = Product::find($id);

        if ($gift) {
            $gift->update($request->all());

            return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
        }

        return ResponseFormatter::error(
            null,
            'Data gift gagal diubah',
            404
        );
    }

    public function updateAttributeGift(ProductRequest $request, $id)
    {
        $gift = Product::find($id);

        if ($gift) {
            $gift->update($request->all());

            return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
        }

        return ResponseFormatter::error(
            null,
            'Data gift gagal diubah',
            404
        );
    }

    public function deleteGift($id)
    {
        $gift = Product::find($id);

        if ($gift) {
            $gift->delete();

            return ResponseFormatter::success($gift, 'Data gift berhasil dihapus');
        }

        return ResponseFormatter::error(
            null,
            'Data gift gagal dihapus',
            404
        );
    }

    public function redeemGifts($id)
    {

        try{
            $this->service->redeemGift($id);
        }catch(ClientError $e){
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                $e->getCode()
            );
        }catch(\Exception $e){
            return ResponseFormatter::error(null,'Maaf, terjadi kesalahan pada server kami',500,);
            Log::error($e->getMessage());
        }

        // $user = auth()->user();
        // $gift = Product::find($id);

        // if ($gift) {
        //     if ($gift->stock >= 1) {
        //         $gift->update([
        //             'stock' => $gift->stock - 1
        //         ]);

        //         UserRedeem::create([
        //             'user_id' => $user->id,
        //             'product_id' => $gift->id
        //         ]);

        //         return ResponseFormatter::success($gift, 'Data gift berhasil ditukar');
        //     }


        //     return ResponseFormatter::success($gift, 'Data gift berhasil diredeem');
        // }

        // return ResponseFormatter::error(
        //     null,
        //     'Data gift gagal diredeem',
        //     404
        // );
    }

    public function ratingGifts(RatingRequest $request, $id)
    {
        $user = auth()->user();


        try {
            $gift = Product::find($id);



            if ($gift) {
                $userHasRedeem = UserRedeem::where('user_id', $user->id)
                    ->where('product_id', $id)
                    ->first();

                if($userHasRedeem){
                    ProductStar::create([
                        'product_id' => $id,
                        'user_id' => $user->id,
                        'star' => round($request->star)
                    ]);

                    return ResponseFormatter::success($gift, 'Data gift berhasil di rate');
                }


                return ResponseFormatter::error(null,'Data item belum di redeem',400);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                // 'Data gift gagal di rate',
                $e->getMessage(),
                404
            );
        }
    }
}
