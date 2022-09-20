<?php

namespace App\Services;

interface GiftServiceInterface
{
    public function getGifts();
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
}

;?>
