<?php

namespace App\Serializers;

use Closure;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

class ClosureSerializer
{
    /**
     * Serialize a closure to a string. Captures context data like static variables
     * and parameters without serializing the code.
     *
     * @param  Closure  $closure  The closure to serialize.
     * @return string Serialized form of the closure's context.
     *
     * @throws PhpVersionNotSupportedException
     */
    public static function serialize(Closure $closure): string
    {
        return serialize(new SerializableClosure($closure));
    }

    /**
     * Unserialize a closure from a string. Returns a closure reconstructed
     * from the serialized string.
     *
     * @param  string  $serializedClosure  The serialized string of the closure.
     * @return Closure The reconstructed closure.
     */
    public static function unserialize(string $serializedClosure): Closure
    {
        return unserialize($serializedClosure)->getClosure();
    }
}
