<?php

App::uses('Token', 'Model');

/**
 * Token Test Case
 *
 */
class TokenTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.token',
        'app.user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Token = ClassRegistry::init('Token');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Token);

        parent::tearDown();
    }

    /**
     * testIsExpired method
     *
     * @return void
     */
    public function testIsExpired()
    {
        $token = array(
            'user_id' => 1,
            'action'  => 'feeds',
            'value'   => 'test-token',
        );

        // 有効期限より5秒後のToken
        $this->Token->create();
        $token['created_on'] =date(DATE_ATOM, time() - (60 * 60 * 24) - 5);
        $expired_token = $this->Token->save($token);
        $this->assertTrue($this->Token->isExpired($expired_token));

        // 有効期限より5秒前のToken
        $this->Token->create();
        $token['created_on'] =date(DATE_ATOM, time() - (60 * 60 * 24) + 5);
        $not_expired_token = $this->Token->save($token);
        $this->assertFalse($this->Token->isExpired($not_expired_token));
    }
}