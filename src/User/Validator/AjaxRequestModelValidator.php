<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Validator;

use Da\User\Contracts\ValidatorInterface;
use yii\base\Model;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class AjaxRequestModelValidator implements ValidatorInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function validate()
    {
        $request = Yii::$app->request;

        if ($request->getIsAjax() && !$request->getIsPjax()) {
            if ($this->model->load($request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                echo json_encode(ActiveForm::validate($this->model));
                Yii::$app->end();
            }
        }
    }
}
