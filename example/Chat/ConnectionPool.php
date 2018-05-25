<?php

namespace Example\Chat;

use React\Socket\ConnectionInterface;

class ConnectionPool
{
    /**
     * @var \SplObjectStorage
     */
    private $connection;

    /**
     * @param $connection ConnectionInterface
     * @return mixed
     */
    public function getConnectionData(ConnectionInterface $connection)
    {
        return $this->connection->offsetGet($connection);
    }

    /**
     * @param $connection ConnectionInterface
     * @param array $data
     */
    public function setConnectionData(ConnectionInterface $connection, $data = [])
    {
        $this->connection->offsetSet($connection, $data);
    }

    /**
     * ConnectionPool constructor.
     */
    public function __construct()
    {
        $this->connection = new \SplObjectStorage();
    }

    /**
     * Connectionを追加する
     * @param ConnectionInterface $connection
     */
    public function add(ConnectionInterface $connection)
    {
        $connection->write("Hi\nEnter your name:");

        $this->initEvents($connection);
        $this->setConnectionData($connection, []);
    }

    /**
     * 接続したConnectionに対してイベントを設定する
     *
     * @param ConnectionInterface $connection
     */
    protected function initEvents(ConnectionInterface $connection)
    {
        $connection->on('data', function ($data) use ($connection) {
            $connectionData = $this->getConnectionData($connection);

            if (empty($connectionData)) {
                $this->addNewMember($data, $connection);
                return;
            }

            $name = $connectionData['name'];
            $this->sendAll("$name: $data", $connection);
        });

        $connection->on('close', function () use ($connection) {
            $data = $this->getConnectionData($connection);
            $name = $data['name'] ?? '';

            $this->connection->offsetUnset($connection);
            $this->sendAll("User $name leaves the chat\n", $connection);
        });
    }

    protected function addNewMember($name, ConnectionInterface $connection)
    {
        $name = str_replace([
            "\n",
            "\r",
        ], "", $name);

        $this->setConnectionData($connection, [
            'name' => $name,
        ]);

        $this->sendAll("User $name join the chat\n", $connection);
    }

    /**
     * Poolされている全てのConnectionに対して書き込みを行う
     * @param $data
     * @param ConnectionInterface $except
     */
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