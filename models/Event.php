<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property integer $event_id
 * @property integer $user_id
 * @property string $event_name
 * @property string $event_location
 * @property string $event_date
 * @property string $event_description
 * @property string $event_action
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          /*  [['user_id', 'event_name', 'event_location', 'event_date', 'event_description'], 'required'],
            [['user_id'], 'integer'],
            [['event_description'], 'string'],
            [['event_name', 'event_location'], 'string', 'max' => 200],
            [['event_date'], 'string', 'max' => 100],
            [['event_action'], 'string', 'max' => 50],*/
        ];
    }

  /*  public function getCountry() {
        return $this->hasOne(Event::className(), ['event_id' => 'event_gallery_id']);
    }*/
        public function getEventDetails(){

            $query = new \yii\db\Query;
            $query->select('*')
                ->from('event e')
                ->leftJoin('event_gallery eg','eg.gallery_event_id = e.event_id');

             //   ->limit($Limit);
            $command = $query->createCommand();
       $eventList = $command->queryAll();
            //echo"<pre>";print_r($eventList);die;

        return $eventList;
        }

    public function eventDetails($id){

    $query = new \yii\db\Query;
    $query->select('*')
        ->from('event e')
        ->leftJoin('event_gallery eg','eg.gallery_event_id = e.event_id')
        //->where('id=:id', array(':id'=>$id))

        ->where('e.event_id=:id',array(':id'=>$id));
    //  ->andWhere(['e.event_action'=> !`Disapproved`]);

    //   ->limit($Limit);
    $command = $query->createCommand();
    // print_r ($query->sql);die;
    $eventList = $command->queryAll();
    //echo"<pre>";print_r($eventList);die;

    return $eventList;
}
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => 'Event ID',
            'user_id' => 'User ID',
            'event_name' => 'Event Name',
            'event_location' => 'Event Location',
            'event_date' => 'Event Date',
            'event_description' => 'Event Description',
            'event_action' => 'Event Action',
        ];
    }
}
