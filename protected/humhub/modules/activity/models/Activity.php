<?php

namespace humhub\modules\activity\models;

use Yii;
use yii\web\HttpException;

/**
 * This is the model class for table "activity".
 *
 * @property integer $id
 * @property string $class
 * @property string $module
 * @property string $object_model
 * @property string $object_id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Activity extends \humhub\modules\content\components\activerecords\Content
{

    /**
     * Add mix-ins to this model
     *
     * @return type
     */
    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\UnderlyingObject::className(),
                'mustBeInstanceOf' => [
                    \humhub\modules\content\components\activerecords\Content::className(),
                    \humhub\modules\content\components\activerecords\ContentContainer::className(),
                    \humhub\modules\content\components\activerecords\ContentAddon::className(),
                ]
            ]
        ];
    }

    public function getWallOut()
    {

        $activity = $this->getClass();
        if ($activity !== null) {
            return $activity->render();
        }

        return "";
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by', 'object_id'], 'integer'],
            [['class'], 'string', 'max' => 100],
            [['module', 'object_model'], 'string', 'max' => 100]
        ];
    }

    /**
     * @return \humhub\modules\notification\components\BaseNotification
     */
    public function getClass()
    {
        if (class_exists($this->class)) {
            return Yii::createObject([
                        'class' => $this->class,
                        'record' => $this,
                        'originator' => $this->content->user,
                        'source' => $this->getUnderlyingObject(),
            ]);
        } else {
            throw new Exception("Could not find BaseActivity " . $this->class . " for Activity Record.");
        }
        return null;
    }

}