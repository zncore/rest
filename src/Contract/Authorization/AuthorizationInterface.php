<?php

namespace PhpLab\Rest\Contract\Authorization;

interface AuthorizationInterface
{

    public function authByLogin(string $login, string $password = 'Wwwqqq111'): AuthorizationInterface;

    public function logout(): AuthorizationInterface;

    public function getAuthToken(): ?string;

    public function authorization();

}