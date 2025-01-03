<?php

use App\Models\Messages;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messages.{id}', function (User $user, int $id) {
    // return true;
    return $user->id === Messages::find($id)->memberable->id;
});
