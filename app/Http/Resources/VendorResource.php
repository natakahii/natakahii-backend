<?php

namespace App\Http\Resources;

use App\Support\CdnHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'shop_name' => $this->shop_name,
            'shop_slug' => $this->shop_slug,
            'description' => $this->description,
            'logo' => CdnHelper::normalize($this->logo),
            'commission_rate' => $this->commission_rate,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
