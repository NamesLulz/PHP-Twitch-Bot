<?php
set_time_limit(0);
error_reporting(FALSE);

class Bot
{
	var $socket, $config;
	var $empty_config = array(
		'server' => 'irc.twitch.tv',
		'port' => 6667,
		'pass' => '',
		'nick' => '',
		'user' => '',
		'channel' => ''
	);

	/*
	** MySQL is in development.
	*/
	var $sql = false;
	var $mysql = array(
		'mysql-host' => '',
		'mysql-user' => '',
		'mysql-pass' => '',
		'mysql-data' => ''
	);
	
	var $mysqm = array(
		'mysql-host' => '',
		'mysql-user' => '',
		'mysql-pass' => '',
		'mysql-data' => ''
	);

	public function message($type, $message)
	{
		$message = trim($message);
		switch(strtolower($type))
		{
			case "message":
				echo "= Message: " . $message . "\n";
				fwrite($this->debug_file, 'message: ' . $message . "\n");
			break;
			case "info":
				echo "= Info: " . $message . "\n";
				fwrite($this->debug_file, 'info: ' . $message . "\n");
			break;
			case "cursor":
				echo "> ";
			break;
			case "commands":
				echo "= Commands: " . $message . "\n";
				fwrite($this->debug_file, 'commands: ' . $message . "\n");
			break;
			case "error":
				echo "= Error: " . $message . "\n";
				fwrite($this->debug_file, 'error: ' . $message . "\n");
			break;
			case "sent":
				echo "= Sent data: " . $message . "\n";
				fwrite($this->debug_file, 'sent: ' . $message . "\n");
			break;
			case "received":
				echo "= Received data: " . $message . "\n";
				fwrite($this->debug_file, 'received: ' . $message . "\n");
			break;
			case "check":
				echo "= Checked (key : value): " . $message . "\n";
				fwrite($this->debug_file, 'check: ' . $message . "\n");
			break;
			default:
				echo "= Error: Unknown type used, " . trim($type) . "\n";
				fwrite($this->debug_file, 'error: ' . trim($type) . "\n");
			break;
		}
	}
	
	public function onOpen()
	{
		if(!file_exists('debug.txt'))
		{
			$this->debug_file = fopen('debug.txt', 'a+');
			fwrite($this->debug_file, "First opened on: " . date('l jS \of F Y h:i:s A') . ".\n");
		}
		else
		{
			$this->debug_file = fopen('debug.txt', 'a+');
			fwrite($this->debug_file, "Opened on: " . date('l jS \of F Y h:i:s A') . ".\n");
		}

		if(!file_exists('config.txt'))
		{
			$config_file = fopen('config.txt', 'a+');
			fwrite($config_file, json_encode($this->empty_config));
			fwrite($this->debug_file, "Created config file, wrote inside: " . $this->empty_config . "\n");
			fclose($config_file);

			$config_file = fopen('config.txt', 'a+');
			$this->config = json_decode(fread($config_file, filesize('config.txt')));
			$this->confih = $this->config;
		}
		else
		{
			$config_file = fopen('config.txt', 'a+');
			$this->config = json_decode(fread($config_file, filesize('config.txt')));
			$this->confih = $this->config;
		}

		$this->message('message', 'Welcome to NamesLulz\'s bot!');
		$this->message('info', 'Type, "help" for a list of commands.');
		$this->console();
	}
	
	public function console()
	{
		$this->message('cursor');
		$handle = fopen('php://stdin', 'r');
		$ex = explode(' ', trim(fgets($handle)));
		fwrite($this->debug_file, "input: " . json_encode($ex) . "\n");
		
		switch(strtolower($ex[0]))
		{
			case "help":
				switch(strtolower($ex[1]))
				{
					case "update":
						$this->message('info', 'Usage: "update key value".');
						$this->message('info', 'Type, "help keys" for a list of keys.');
					break;
					case "default":
						$this->message('info', 'Usage: "default key".');
						$this->message('info', 'Type, "help keys" for a list of keys.');
					break;
					case "keys":
						$this->message('info', 'Config keys: "server", "port", "pass", "nick", "user", "channel".');
						$this->message('info', 'MySQL keys: "mysql-server", "mysql-user", "mysql-pass", "mysql-data".');
					break;
					case "mysql":
						$this->message('info', 'Usage: "mysql true" or "mysql false".');
						$this->message('info', 'This enables MySQL (not really used at the moment).');
					break;
					default:
						$this->message('commands', '"help", "exit", "update", "default", "mysql", "check".');
					break;
				}
			break;
			case "exit":
			case "stop":
			case "quit":
			case "close":
			case "leave":
			case "destroy":
				fwrite($this->config_file, $this->config);
				fclose($handle);
				$this->message('message', 'Goodbye!');
				exit;
			break;
			case "update":
				switch(strtolower($ex[1]))
				{
					case "server":
						$this->config['server'] = $ex[2];
						$this->message('message', 'Server updated to, "' . $ex[2] . '".');
					break;
					case "port":
						$this->config['port'] = $ex[2];
						$this->message('message', 'Port updated to, "' . $ex[2] . '".');
					break;
					case "pass":
						$this->config['pass'] = $ex[2];
						$this->message('message', 'Pass updated to, "' . $ex[2] . '".');
					break;
					case "nick":
						$this->config['nick'] = $ex[2];
						$this->message('message', 'Nick updated to, "' . $ex[2] . '".');
					break;
					case "user":
						$this->config['user'] = $ex[2];
						$this->message('message', 'User updated to, "' . $ex[2] . '".');
					break;
					case "channel":
						$this->config['channel'] = $ex[2];
						$this->message('message', 'Channel updated to, "' . $ex[2] . '".');
					break;
					case "mysql-host":
						$this->mysql['mysql-host'] = $ex[2];
						$this->message('message', 'Mysql-host updated to, "' . $ex[2] . '".');
					break;
					case "mysql-user":
						$this->mysql['mysql-host'] = $ex[2];
						$this->message('message', 'Mysql-host updated to, "' . $ex[2] . '".');
					break;
					case "mysql-pass":
						$this->mysql['mysql-pass'] = $ex[2];
						$this->message('message', 'Mysql-pass updated to, "' . $ex[2] . '".');
					break;
					case "mysql-data":
						$this->mysql['mysql-data'] = $ex[2];
						$this->message('message', 'Mysql-data updated to, "' . $ex[2] . '".');
					break;
					default:
						$this->message('error', 'Unknown key, "' . $ex[2] . '".');
						$this->message('info', 'Type, "help keys" for a list of keys.');
					break;
				}
			break;
			case "default":
				switch(strtolower($ex[1]))
				{
					case "server":
						$this->config['server'] = $this->confih['server'];
						$this->message('message', 'Using default serer, "' . $this->confih['server'] . '".');
					break;
					case "port":
						$this->config['port'] = $this->confih['port'];
						$this->message('message', 'Using default port, "' . $this->confih['port'] . '".');
					break;
					case "pass":
						$this->config['pass'] = $this->confih['pass'];
						$this->message('message', 'Using default pass, "' . $this->confih['pass'] . '".');
					break;
					case "nick":
						$this->config['nick'] = $this->confih['nick'];
						$this->message('message', 'Using default nick, "' . $this->confih['nick'] . '".');
					break;
					case "user":
						$this->config['user'] = $this->confih['user'];
						$this->message('message', 'Using default user, "' . $this->confih['user'] . '".');
					break;
					case "channel":
						$this->config['channel'] = $this->confih['channel'];
						$this->message('message', 'Using default channel, "' . $this->confih['channel'] . '".');
					break;
					case "mysql-server":
						$this->mysql['mysql-server'] = $this->mysqm['mysql-server'];
						$this->message('message', 'Using default mysql-server, "' . $this->mysqm['mysql-server'] . '".');
					break;
					case "mysql-user":
						$this->mysql['mysql-user'] = $this->mysqm['mysql-user'];
						$this->message('message', 'Using default mysql-user, "' . $this->mysqm['mysql-user'] . '".');
					break;
					case "mysql-pass":
						$this->mysql['mysql-pass'] = $this->mysqm['mysql-pass'];
						$this->message('message', 'Using default mysql-pass, "' . $this->mysqm['mysql-pass'] . '".');
					break;
					case "mysql-data":
						$this->mysql['mysql-data'] = $this->mysqm['mysql-data'];
						$this->message('message', 'Using default mysql-data, "' . $this->mysqm['mysql-data'] . '".');
					break;
					default:
						$this->message('error', 'Unknown key, "' . $ex[2] . '".');
						$this->message('info', 'Type, "help keys" for a list of keys.');
					break;
				}
			break;
			case "mysql":
				if($ex[2] == true && $this->sql == false)
				{
					$this->sql = true;
					$this->message('message', 'MySQL enabled.');
				}
				else if($ex[2] == true && $this->sql == true)
				{
					$this->message('error', 'MySQL is already enabled.');
				}
				else if($ex[2] == false && $this->sql == true)
				{
					$this->sql = false;
					$this->message('message', 'MySQL disabled.');
				}
				else if($ex[2] == false && $this->sql == false)
				{
					$this->message('error', 'MySQL is already disabled.');
				}
				else
				{
					$this->message('error', 'Value must be true or false, not, "' . $ex[2] . '".');
				}
			break;
			case "check":
				$found = false;
				while($found == false)
				{
					foreach($this->config as $key => $value)
					{
						if($key == strtolower($ex[1]))
						{
							$this->message('check', $key . ' : ' . $value);
							$found = true;
						}
					}
					
					foreach($this->mysql as $key => $value)
					{
						if($key == strtolower($ex[1]))
						{
							$this->message('check', $key . ' : ' . $value);
							$found = true;
						}
						else if($key == "mysql-data" && $found == false)
						{
							$this->message('error', 'Unable to find key, "' . $ex[1] . '".');
							$found = true;
						}
					}
				}
			break;
			case "connect":
				fclose($handle);
				$this->login();
			break;
			default:
				$this->message('error', 'Unknown command, "' . $ex[0] . '". Type, "help" for a list of commands.');
			break;
		}
		
		$this->console();
	}
	
	public function login()
	{
		$this->socket = fsockopen($this->config['server'], $this->config['port'], $errno, $errstr);
		
		if($this->socket)
		{
			$this->message('info', 'Socket opened.');		
			fputs($this->socket, "PASS " . $this->config['pass'] . "\r\n"); $this->message('sent', 'Sent pass, "' . $this->config['pass'] . '".');
			fputs($this->socket, "NICK " . $this->config['nick'] . "\r\n"); $this->message('sent', 'Sent nick, "' . $this->config['nick'] . '".');
			fputs($this->socket, "USER " . $this->config['user'] . "\r\n"); $this->message('sent', 'Sent user, "' . $this->config['user'] . '".');
			fputs($this->socket, "JOIN " . $this->config['channel'] . "\r\n"); $this->message('sent', 'Joined channel, "' . $this->config['channel'] . '".');
			$this->bot_();
		}
		else
		{
			$this->message('error', 'Unable to open the socket.');
			$this->message('error', 'Error number: ' . $errno . '.');
			$this->message('error', 'Error string: ' . $errstr . '.');
			$this->socket = null;
			$this->console();
		}
	}
	
	public function bot_()
	{
		$data = fgets($this->socket);
		$this->message('received', nl2br($data));
		flush();
		
		$ex = explode(' ', $data);
		if($ex[0] == 'PING')
		{
			fputs($this->socket, "PONG " . $ex[1] . "\n"); $this->message('sent', 'Sent pong to server.');
		}
		
		$cmd = str_replace(array(chr(10), chr(13)), '', $ex[3]);
		switch($cmd)
		{
			case ":!echo":
				for($i = 4; $i < count($ex); $i++)
				{
					if($i == 4)
						$msg = $ex[$i];
					else
						$msg = $msg . ' ' . $ex[$i];
				}
				
				fputs($this->socket, "PRIVMSG " . $ex[2] . " :" . $msg . "\n"); $this->message('sent', 'Sent a message to, "' . $ex[2] . '".'); $this->message('sent', 'Saying, "' . $msg . '".');
			break;
			case ":!google":
				$msg = "https://www.google.com/search?q=";
				for($i = 4; $i < count($ex); $i++)
				{
					if($i == 4)
						$msg .= $ex[$i];
					else
						$msg = $msg . '%20' . $ex[$i];
				}

				fputs($this->socket, "PRIVMSG " . $ex[2] . " :" . $msg . "\n"); $this->message('sent', 'Sent a message to, "' . $ex[2] . '".'); $this->message('sent', 'Saying, "' . $msg . '".');
			break;
			case ":!youtube":
				$msg = "https://www.youtube.com/results?search_query=";
				for($i = 4; $i < count($ex); $i++)
				{
					if($i == 4)
						$msg .= $ex[$i];
					else
						$msg = $msg . '%20' . $ex[$i];
				}

				fputs($this->socket, "PRIVMSG " . $ex[2] . " :" . $msg . "\n"); $this->message('sent', 'Sent a message to, "' . $ex[2] . '".'); $this->message('sent', 'Saying, "' . $msg . '".');
			break;
			case ":!join":
				fputs($this->socket, "PART " . $ex[2] . "\n"); $this->message('sent', 'Left channel, "' . $ex[2] . '".');
				fputs($this->socket, "JOIN #" . $ex[4] . "\n"); $this->message('sent', 'Joined channel, "' . $ex[4] . '".');
			break;
			case ":!quit":
				fclose($this->socket); $this->message('info', 'Socket closed.');
				$this->console();
			break;
			case ":!exit":
				fwrite($this->config_file, $this->config);
				fclose($this->socket); $this->message('info', 'Socket closed.');
				$this->message('message', 'Goodbye!');
				exit;
			break;
		}
		
		$this->bot_();
	}
}

$bot = new Bot();
$bot->onOpen();
