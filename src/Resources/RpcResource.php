<?php

namespace App\Resources;

use App\Enums\RpcExpectedType;

class RpcResource
{

    public function __construct(
        public RpcExpectedType $type,
        public mixed $result,
        public int $exitCode
    ) { }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'result' => $this->result,
            'exitCode' => $this->exitCode
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

}
