<?php
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

        if($request->getIsAjax() && !$request->getIsPjax()) {
            if($this->model->load($request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                echo json_encode(ActiveForm::validate($this->model));
                Yii::$app->end();
            }
        }
    }
}
