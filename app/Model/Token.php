<?php

/**
 * Token
 *
 */
class Token extends AppModel
{
    public $belongsTo = array('User');

    public $validity_time = 0;

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validity_time = 60 * 60 * 24; // 1.day
    }

    public function beforeCreate()
    {
        $this->data['User']['value'] = $this->_generate_token_value();
        return true;
    }

    /**
     * @todo public static function isExpired
     *
     * Return true if token has expired
     */
    public function isExpired($token)
    {
        if (time() > (strtotime($token['Token']['created_on']) + $this->validity_time)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * destroy_expired
     *
     * Delete all expired tokens
     */
    public function destroy_expired()
    {
        return $this->deleteAll(
            array(
                'action <>'      => 'feeds',
                'created_on < ?' => array(time() - $this->validity_time),
            )
        );
    }

    /**
     * _generate_token_value
     *
     * @todo access private
     * @todo fix token generate algorithm
     */
    public function _generate_token_value()
    {
        return sha1(microtime());
    }

    public function destroy($user_id, $action)
    {
        $this->deleteAll(
            array(
                'action ='  => $action,
                'user_id =' => $user_id,
            )
        );
        $this->save(
            array(
                $this->alias => array(
                    'user_id' => $user_id,
                    'action'  => $action,
                    'value'   => $this->_generate_token_value(),
                )
            )
        );
    }
}