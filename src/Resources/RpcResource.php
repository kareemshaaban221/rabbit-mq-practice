<?php

namespace App\Resources;

use App\Enums\RpcExpectedType;

class RpcResource implements \ArrayAccess
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

    public function offsetExists(mixed $offset): bool {
        return in_array($offset, get_class_vars(self::class));
    }

    public function offsetGet(mixed $offset): mixed {
        if ($this->offsetExists($offset)) {
            return $this->$offset;
        } else {
            throw new \ValueError("[ArrayAccessError] Not found offset $offset");
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        throw new \BadFunctionCallException("[ArrayAccessException] Cannot set new value to this class object");
    }

    public function offsetUnset(mixed $offset): void {
        throw new \BadMethodCallException("[ArrayAccessException] Cannot unset value to this class object");
    }

}
