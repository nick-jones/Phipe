<?php

namespace Phipe;

use Phipe\Connection\Connection;

/**
 * @package Phipe
 */
class WatcherTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Watcher
     */
    protected $watcher;

    protected function setUp() {
        $this->watcher = new Watcher();
    }

    public function testOn() {
        $result = $this->watcher->on(Connection::EVENT_CONNECT, function () {});
        $this->assertEquals($this->watcher, $result);
    }

    public function testOn_InvalidEvent() {
        $this->setExpectedException('\InvalidArgumentException', 'Event "mock" is unrecognised');

        $this->watcher->on('mock', function() {});
    }

    public function testUpdate() {
        $calls = 0;
        $event = Connection::EVENT_CONNECT;
        $connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

        $this->watcher->on($event, function (Connection $subject) use ($connection, &$calls) {
            $this->assertEquals($connection, $subject);
            $calls++;
        });

        $this->watcher->update($connection, $event);
        $this->assertEquals(1, $calls);
    }

    public function testUpdate_NonListeningEvent() {
        $connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

        $this->watcher->on(Connection::EVENT_CONNECT, function ($subject) {
            $this->fail('Not expecting callback to be executed');
        });

        $this->watcher->update($connection, Connection::EVENT_DISCONNECT);
    }

    public function testUpdate_NonConnection() {
        $subject = $this->getMock('\SplSubject');

        $this->watcher->on(Connection::EVENT_CONNECT, function ($subject) {
            $this->fail('Not expecting callback to be executed');
        });

        $this->watcher->update($subject, Connection::EVENT_CONNECT);
    }
}