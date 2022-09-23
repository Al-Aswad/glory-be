<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\RatingRequest;
use App\Http\Requests\RedeemRequest;
use App\Services\GiftService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GiftController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new GiftService();
    }

    public function getGifts(Request $request)
    {
        try {
            $gifts = $this->service->getGifts(
                $request->query('name'),
                $request->query('price_from'),
                $request->query('price_to'),
                $request->query('limit', 10),
                $request->query('order_by'),
                $request->query('star'),
            );

            return ResponseFormatter::success($gifts, 'Data gift berhasil diambil');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return ResponseFormatter::error($e, 'Maaf, terjadi kegagalan pada server kami', 500);
        }
    }

    public function getGiftsById($id)
    {
        $gift = $this->service->getGiftsById($id);

        return ResponseFormatter::success($gift, 'Data gift berhasil diambil');
    }

    public function createGift(ProductRequest $request)
    {
        $gift = $this->service->createGifts($request->all());

        return ResponseFormatter::success($gift, 'Data gift berhasil ditambahkan');
    }

    public function updateGift(ProductRequest $request, $id)
    {
        $gift = $this->service->updateGifts($request, $id);

        return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
    }

    public function updateAttributeGift(ProductRequest $request, $id)
    {
        $gift = $this->service->updateAttributeGifts($request->all(), $id);

        return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
    }

    public function deleteGift($id)
    {
        $gift = $this->service->deleteGifts($id);

        return ResponseFormatter::success($gift, 'Data gift berhasil dihapus');
    }

    public function redeemGifts($id)
    {
        $gift = $this->service->redeemGifts($id);

        return ResponseFormatter::success($gift, 'Berhasil redeem gift');
    }

    public function redeemGiftsBulk(RedeemRequest $request)
    {
        $productId = $this->service->redeemGiftsBulk($request->product_id);

        return ResponseFormatter::success($productId, 'Berhasil redeem gift');
    }

    public function ratingGifts(RatingRequest $request, $id)
    {
        $gift = $this->service->ratingGifts($id, $request->rating);

        return ResponseFormatter::success($gift, 'Berhasil memberi rating');
    }
}
