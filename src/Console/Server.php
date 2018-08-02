<?php

namespace POPsy\UserMonitor\Console;

use Illuminate\Console\Command;
use POPsy\UserMonitor\UserMonitorSocket;
use Ratchet\App as Socket;

class Server extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user-monitor:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start web socket server.';

    /**
     * $httpHost HTTP hostname clients intend to connect to.
     * MUST match JS `new WebSocket('ws://$httpHost').
     */
    protected $httpHost;

    /**
     * Port to listen on. If 80, assuming production,
     * Flash on 843 otherwise expecting Flash to be proxied through 8843.
     */
    protected $port;

    /**
     *IP address to bind to. Default is localhost/proxy only.
     *'0.0.0.0' for any machine.
     */
    protected $address;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $config = config('user-monitor');
        $this->httpHost = $config['httpHost'];
        $this->port = $config['port'];
        $this->address = $config['address'];
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $socket = new Socket($this->httpHost, $this->port, $this->address);
        $socket->route('/user-monitor', new UserMonitorSocket(), ['*']);
        $this->info('Laravel web socket server user-monitor started on '.$this->httpHost.':'.$this->port.'/'.'address:'.$this->address);
        $socket->run();
    }
}
