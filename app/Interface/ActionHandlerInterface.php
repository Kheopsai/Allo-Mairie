<?php

namespace App\Interface;

interface ActionHandlerInterface
{
    public function validate($data): bool;

    public function handle($data);

    public function process($data);
}
