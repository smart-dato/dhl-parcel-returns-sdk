<?php

namespace SmartDato\DhlParcelReturns\Auth;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class NullAuthenticator implements Authenticator
{
    public function set(PendingRequest $pendingRequest): void {}
}
