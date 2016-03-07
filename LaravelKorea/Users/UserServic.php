<?php

namespace LaravelKorea\User;

class UserService
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

}
