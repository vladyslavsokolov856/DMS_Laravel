<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MenuResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'menu_items' => MenuItemResource::collection($this->menuItems),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
