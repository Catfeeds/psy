<?php

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property integer $id
 * @property integer $uid
 * @property string $phone
 * @property string $username
 * @property string $price
 * @property integer $pid
 * @property string $end
 * @property string $begin
 * @property string $pname
 * @property string $note
 * @property integer $status
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 */
class Order extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, pid, created', 'required'),
			array('uid, pid, status, deleted, created, updated', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>12),
			array('username, pname, note', 'length', 'max'=>255),
			array('price, end, begin', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, uid, phone, username, price, pid, end, begin, pname, note, status, deleted, created, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'uid' => 'Uid',
			'phone' => 'Phone',
			'username' => 'Username',
			'price' => 'Price',
			'pid' => 'Pid',
			'end' => 'End',
			'begin' => 'Begin',
			'pname' => 'Pname',
			'note' => 'Note',
			'status' => 'Status',
			'deleted' => 'Deleted',
			'created' => 'Created',
			'updated' => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('begin',$this->begin,true);
		$criteria->compare('pname',$this->pname,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('created',$this->created);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Order the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
