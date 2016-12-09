<?php
namespace Da\User\Form;

use Da\User\Query\UserQuery;
use Yii;
use yii\base\Model;

class ResendForm extends Model
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var UserQuery
     */
    protected $userQuery;

    /**
     * @param UserQuery $userQuery
     * @param array  $config
     */
    public function __construct(UserQuery $userQuery, $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email'),
        ];
    }

    /**
     * Creates new confirmation token and sends it to the user.
     *
     * @return bool
     */
    public function resend()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->userQuery->whereEmail($this->email)->one();

        $user = $this->finder->findUserByEmail($this->email);

        if ($user instanceof User && !$user->isConfirmed) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $user->id,
                'type' => Token::TYPE_CONFIRMATION,
            ]);
            $token->save(false);
            $this->mailer->sendConfirmationMessage($user, $token);
        }

        \Yii::$app->session->setFlash(
            'info',
            \Yii::t(
                'user',
                'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'
            )
        );

        return true;
    }
}
