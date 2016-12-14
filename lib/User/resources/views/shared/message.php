<?php

/**
 * @var yii\web\View
 * @var \Da\User\Module $module
 * @var string          $title
 */
$this->title = $title;

?>

<?= $this->render(
    '_alert',
    [
        'module' => $module,
    ]
);
