<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reply;
use JetBrains\PhpStorm\Pure;

class ReplyPolicy extends Policy
{
    #[Pure]
    public function destroy(User $user, Reply $reply): bool
    {
        return $user->isAuthorOf($reply)||$user->isAuthorOf($reply->topic);
    }
}
