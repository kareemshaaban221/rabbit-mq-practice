<?php

namespace App\Resources;

use App\Enums\RpcExpectedType;

/**
 * RpcResource class
 *
 * This class is a data transfer object (DTO) that represents the result of an RPC call.
 * It is used to standardize the format of the response from the server and to provide
 * a way to access the result of the RPC call in a uniform way.
 *
 * @package  App\Resources
 */
class RpcResource implements \ArrayAccess
{

    /**
     * Constructor
     *
     * @param RpcExpectedType $type The expected return type of the RPC call.
     * @param mixed $result The result of the RPC call.
     * @param int $exitCode The exit code of the RPC call.
     */
    public function __construct(
        public RpcExpectedType $type,
        public mixed $result,
        public int $exitCode
    ) { }

    /**
     * toArray
     *
     * Returns an associative array representation of the RpcResource object.
     *
     * @return array An associative array representation of the RpcResource object.
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'result' => $this->result,
            'exitCode' => $this->exitCode
        ];
    }

    /**
     * toJson
     *
     * Returns a JSON representation of the RpcResource object.
     *
     * @return string A JSON representation of the RpcResource object.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * offsetExists
     *
     * Checks if the given offset exists in the RpcResource object.
     *
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists(mixed $offset): bool {
        return in_array($offset, get_class_vars(self::class));
    }

    /**
     * offsetGet
     *
     * Returns the value of the given offset in the RpcResource object.
     *
     * @param mixed $offset The offset to get.
     * @return mixed The value of the offset.
     * @throws ValueError If the offset does not exist.
     */
    public function offsetGet(mixed $offset): mixed {
        if ($this->offsetExists($offset)) {
            return $this->$offset;
        } else {
            throw new \ValueError("[ArrayAccessError] Not found offset $offset");
        }
    }

    /**
     * offsetSet
     *
     * Sets the value of the given offset in the RpcResource object.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to set.
     * @throws BadFunctionCallException Because the RpcResource object is read-only.
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new \BadFunctionCallException("[ArrayAccessException] Cannot set new value to this class object");
    }

    /**
     * offsetUnset
     *
     * Unsets the value of the given offset in the RpcResource object.
     *
     * @param mixed $offset The offset to unset.
     * @throws BadMethodCallException Because the RpcResource object is read-only.
     */
    public function offsetUnset(mixed $offset): void {
        throw new \BadMethodCallException("[ArrayAccessException] Cannot unset value to this class object");
    }

}


