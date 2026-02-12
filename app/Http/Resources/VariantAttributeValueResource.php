<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantAttributeValueResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attribute' => new AttributeResource($this->whenLoaded('attribute')),
            'attribute_value' => new AttributeValueResource($this->whenLoaded('attributeValue')),
            'numeric_value' => $this->numeric_value,
        ];
    }
}
