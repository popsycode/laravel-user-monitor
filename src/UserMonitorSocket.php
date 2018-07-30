<?php

namespace POPsy\UserMonitor;

use Illuminate\Session\SessionManager;
use Ratchet\ConnectionInterface;

class UserMonitorSocket extends BaseSocketListener
{
    /**
     * Current clients.
     *
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * UserMonitorSocket constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        //take user id
        $userId = $this->getUserFromSession($conn);

        //Create a list of users connected to the server
        array_push($this->userList, $userId);

        //We tell everything that happened
        echo "New connection! user_id = ({$userId})\n";
    }

    public function getUserFromSession($conn)
    {
        // Create a new session handler for this client
        $session = (new SessionManager(\App::getInstance()))->driver();

        // fix issue https://github.com/laravel/framework/issues/24364
        if (\Config::get('session.driver') == 'file') {
            clearstatcache();
        }

        // Get the cookies
        $cookies = $conn->WebSocket->request->getCookies();

        // Get the laravel's one
        $laravelCookie = urldecode($cookies[\Config::get('session.cookie')]);

        // get the user session id from it
        $idSession = \Crypt::decrypt($laravelCookie);

        // Set the session id to the session handler
        $session->setId($idSession);

        // Bind the session handler to the client connection
        $conn->session = $session;
        $conn->session->start();

        //We take the user from a session
        $userId = $conn->session->get(\Auth::getName());
        return $userId;
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception          $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
