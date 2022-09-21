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
        try {
            $gift = $this->service->updateGifts($request, $id);

            return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
        } catch (ClientError $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                $e->getCode()
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(
                null,
                'Terjadi kesalahan pada server',
                500
            );
        }
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
       try {
           $gift = $this->service->deleteGifts($id);

           return ResponseFormatter::success($gift, 'Data gift berhasil dihapus');
       } catch (ClientError $e) {
           return ResponseFormatter::error(
               null,
               $e->getMessage(),
               $e->getCode()
           );
       } catch (\Exception $e) {
           Log::error($e->getMessage());

           return ResponseFormatter::error(
               null,
               'Data gift gagal dihapus',
               500
           );
       }
    }

    public function redeemGifts($id)
    {

        try{
            $this->service->redeemGifts($id);
        }catch(ClientError $e){
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                $e->getCode()
            );
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return ResponseFormatter::error(null,'Maaf, terjadi kesalahan pada server kami',500);
        }
    }

    public function ratingGifts(RatingRequest $request, $id)
    {
        try {

            $this->service->ratingGifts($id, $request->rating);
        } catch (ClientError $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                $e->getCode()
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseFormatter::error(null, 'Maaf, terjadi kesalahan pada server kami', 500);
        }
    }
}
