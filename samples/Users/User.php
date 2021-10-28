<?php

namespace Osm\Data\Samples\Users;

use Osm\Data\Accounts\Account;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Base\Attributes\Type;

/**
 * @property string $email #[Serialized]
 * @property string $password #[Serialized]
 */
#[Type('user')]
class User extends Account
{

}