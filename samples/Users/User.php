<?php

namespace Osm\Admin\Samples\Users;

use Osm\Admin\Accounts\Account;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 * @property string $email #[Serialized]
 * @property string $password #[Serialized]
 */
#[Type('user')]
class User extends Account
{

}