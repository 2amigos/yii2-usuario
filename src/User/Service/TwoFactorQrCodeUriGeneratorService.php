<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\TwoFA\Contracts\StringGeneratorServiceInterface;
use Da\TwoFA\Manager;
use Da\TwoFA\Service\QrCodeDataUriGeneratorService;
use Da\TwoFA\Service\TOTPSecretKeyUriGeneratorService;
use Da\User\Model\User;
use Yii;

class TwoFactorQrCodeUriGeneratorService implements StringGeneratorServiceInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * TwoFactorQrCodeUriGeneratorService constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function run() : string
    {
        $user = $this->user;
        if (!$user->auth_tf_key) {
            $user->auth_tf_key = (new Manager())->generateSecretKey();
            $user->updateAttributes(['auth_tf_key']);
        }

        $totpUri = (new TOTPSecretKeyUriGeneratorService(Yii::$app->name, $user->email, $user->auth_tf_key))->run();

        return (new QrCodeDataUriGeneratorService($totpUri))->run();
    }
}
