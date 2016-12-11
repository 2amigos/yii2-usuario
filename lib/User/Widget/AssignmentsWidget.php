<?php

namespace Da\User\Widget;

use dektrium\rbac\components\DbManager;
use dektrium\rbac\models\Assignment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class AssignmentsWidget extends Widget
{
    /** @var integer ID of the user to whom auth items will be assigned. */
    public $userId;

    /** @var DbManager */
    protected $manager;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = Yii::$app->authManager;
        if ($this->userId === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$userId');
        }
    }

    /** @inheritdoc */
    public function run()
    {
        $model = Yii::createObject([
            'class'   => Assignment::className(),
            'user_id' => $this->userId,
        ]);

        if ($model->load(\Yii::$app->request->post())) {
            $model->updateAssignments();
        }

        return $this->render('form', [
            'model' => $model,
        ]);
    }
}
