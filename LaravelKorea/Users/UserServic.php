<?php

namespace LaravelKorea\Users;

class UserService
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

}
