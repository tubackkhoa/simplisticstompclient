<?php

/**
 * Handles SENDs to STOMP
 * Only tested with the rabbitmq stomp gateway and rabbitmq server 1.4.0
 * Only supports sending message to stomp, no support for other commands
 *
 */
class StompSender{
	private $host;
	private $port;
	
	private $login;
	private $passcode;
	private $virtual_host;
	private $realm;
	
	private $timeout;

	/**
	 * Constructor
	 *
	 * @param string $login (e.g. 'guest')
	 * @param string $passcode (e.g. 'guest')
	 * @param string $virtual_host (e.g. '/')
	 * @param string $realm (e.g. '/data')
	 */
	public function __construct($login, $passcode, $virtual_host, $realm){
		$this->host = 'tcp://localhost';
		$this->port = 61613;
		
		$this->login = (string)$login;
		$this->passcode = (string)$passcode;
		$this->virtual_host = (string)$virtual_host;
		$this->realm = (string)$realm;
		
		$this->timeout = 5;
	}
	
	/**
	 * Sets timeout for socket connection, default 5 sec
	 *
	 * @param int $sec Value in seconds
	 */
	public function setTimeout($sec){
		$this->timeout = (int)$sec;
	}
	
	/**
	 * Sets host where stomp is listening, default 'tcp://localhost'
	 *
	 * @param string $host
	 */
	public function setHost($host){
		$this->host = (string)$host;
	}
	
	/**
	 * Sets port where stomp is listening, default 61613
	 *
	 * @param int $port
	 */
	public function setPort($port){
		$this->port = (int)$port;
	}

	/**
	 * Sends a message to a queue
	 *
	 * @param string $message Message to send
	 * @param string $queue Destination
	 * @throws StompException on socket fails and unexpected responses
	 * @return boolean true on success (throws exception on fail)
	 */
	public function send($message, $queue){
		$m = (string)$message;
		$q = (string)$queue;
		
		$msg_connect = "CONNECT\nlogin:$this->login\npasscode:$this->passcode\nvirtual-host:$this->virtual_host\nrealm:$this->realm\n\n\x00";
		$msg_send = "SEND\ndestination:$q\nreceipt:ok\n\n$m\x00";
		$msg_disconnect = "DISCONNECT\n\n\x00";
		if(!($r = fsockopen($this->host,$this->port))) throw new StompException('fsockopen failed');
		stream_set_timeout($r, $this->timeout);
		if(!fwrite($r, $msg_connect.$msg_send.$msg_disconnect)){
			$md = stream_get_meta_data($r);
			if($md['timed_out']) throw new StompException('connection timed out');
			throw new StompException('fwrite failed');
		}
		if(!('CONNECTED' == fread($r,9))){
			$md = stream_get_meta_data($r);
			if($md['timed_out']) throw new StompException('connection timed out');
			throw new StompException('did not get response CONNECTED');
		}
		fread($r,44);
		$md = stream_get_meta_data($r);
		if($md['timed_out']) throw new StompException('connection timed out');
		if(!("RECEIPT\nreceipt-id:ok" == fread($r,21))){
			$md = stream_get_meta_data($r);
			if($md['timed_out']) throw new StompException('connection timed out');
			throw new StompException('did not get response RECEIPT');
		}
		fclose($r);

		return true;
	}
}

/**
 * Exception thrown by StompSender on socket fails and unexpected responses
 *
 */
class StompException extends Exception {}
?>