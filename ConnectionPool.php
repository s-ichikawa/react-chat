<?php

namespace ReactChat;

use React\Socket\ConnectionInterface;

class ConnectionPool
{
    /**
     * @var \SplObjectStorage
     */
    private $connection;

    /**
     * ConnectionPool constructor.
     */
    public function __construct()
    {
        $this->connection = new \SplObjectStorage();
    }

    public function add(ConnectionInterface $connection)
    {
        $connection->write("Hi\n");

        $this->initEvents($connection);
        $this->connection->attach($connection);

        $this->sendAll("New User enters the chat\n", $connection);
    }

    protected function initEvents(ConnectionInterface $connection)
    {
        $connection->on('data', function ($data) use ($connection) {
            $this->sendAll($data, $connection);
        });

        $connection->on('close', function () use ($connection) {
            $this->connection->detach($connection);
            $this->sendAll("A user leaves the chat\n", $connection);
        });
    }

    protected function sendAll($data, ConnectionInterface $except)
    {
        foreach ($this->connection as $conn) {
            if ($conn == $except) {
                continue;
            }

            $conn->write($data);
        }
    }
}