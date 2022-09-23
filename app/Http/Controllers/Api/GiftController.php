<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ClientError;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\RatingRequest;
use App\Http\Requests\RedeemRequest;
use App\Services\GiftService;
use Exception;
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
        try {
            $gift = $this->service->getGiftsById($id);

            return ResponseFormatter::success($gift, 'Data gift berhasil diambil');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(null, $e->getMessage(), 404);
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error($e, 'Maaf, terjadi kegagalan pada server kami', 500);
        }
    }

    public function createGift(ProductRequest $request)
    {
        try {
            $gift = $this->service->createGifts($request->all());

            return ResponseFormatter::success(
                $gift,
                'Data gift berhasil ditambahkan'
            );
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            return ResponseFormatter::error(
                null,
                'Data gift gagal ditambahkan',
                500
            );
        }
    }

    public function updateGift(ProductRequest $request, $id)
    {
        try {
            $gift = $this->service->updateGifts($request, $id);

            return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error(
                null,
                'Maaf, terjadi kegagalan pada server kami',
                500
            );
        }
    }

    public function updateAttributeGift(ProductRequest $request, $id)
    {
        try {
            $gift = $this->service->updateAttributeGifts($request->all(), $id);

            return ResponseFormatter::success($gift, 'Data gift berhasil diubah');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error(
                null,
                'Maaf, terjadi kegagalan pada server kami',
                500
            );
        }
    }

    public function deleteGift($id)
    {
        try {
            $gift = $this->service->deleteGifts($id);

            return ResponseFormatter::success($gift, 'Data gift berhasil dihapus');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error(
                null,
                'Maaf, terjadi kegagalan pada server kami',
                500
            );
        }
    }

    public function redeemGifts($id)
    {
        try {
            $gift = $this->service->redeemGifts($id);

            return ResponseFormatter::success($gift, 'Berhasil redeem gift');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error(
                null, 'Maaf, terjadi kegagalan pada server kami', 500
            );
        }
    }

    public function redeemGiftsBulk(RedeemRequest $request)
    {
        try {
            $productId = $this->service->redeemGiftsBulk($request->product_id);
            // dd("tes");
            return ResponseFormatter::success($productId, 'Berhasil redeem gift');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            Log::error($e->getMessage());

            return ResponseFormatter::error(
                null, 'Maaf, terjadi kegagalan pada server kami', 500
            );
        }
    }

    public function ratingGifts(RatingRequest $request, $id)
    {
        try {
            $gift = $this->service->ratingGifts($id, $request->rating);

            return ResponseFormatter::success($gift, 'Berhasil memberi rating');
        } catch (Exception $e) {
            if ($e instanceof ClientError) {
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            return ResponseFormatter::error(
                null,
                'Maaf, Terjadi kesalahan pada server kami',
                $e->getCode()
            );
        }
    }
}
