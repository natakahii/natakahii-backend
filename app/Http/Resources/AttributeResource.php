<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'is_filterable' => $this->is_filterable,
            'is_variant_attribute' => $this->is_variant_attribute,
            'sort_order' => $this->sort_order,
            'values' => AttributeValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
