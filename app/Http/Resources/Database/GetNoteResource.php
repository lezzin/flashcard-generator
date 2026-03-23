<?php

namespace App\Http\Resources\Database;

use App\Helpers\Date;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "anki_id" => $this->anki_id,
            "fields_hash" => $this->fields_hash,
            "model_name" => $this->model_name,
            "type" => $this->type,
            "fields" => $this->fields,
            "improved_fields" => $this->improved_fields,
            "keywords" => $this->keywords,
            "is_valid" => $this->is_valid,
            "is_recoverable" => $this->is_recoverable,
            "invalidation_reason" => $this->invalidation_reason,
            "created_at" => Date::toTimezone($this->created_at),
            "updated_at" => Date::toTimezone($this->updated_at),
        ];
    }
}
