<?php

namespace App\Http\Resources;

use App\Support\CdnHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'image_path' => CdnHelper::normalize($this->image_path),
            'created_at' => $this->created_at,
        ];
    }
}
