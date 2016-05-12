<?php
set_time_limit(0);
error_reporting(FALSE);

class Bot
{
	var $socket, $config, $connection, $sql = false;
	var $empty_config = array(
		'server' => 'irc.twitch.tv',
		'port' => 6667,
		'pass' => '',
		'nick' => '',
		'user' => '',
		'channel' => ''
	);

	var $empty_mysql_config = array(
		'mysql_host' => '',
		'mysql_user' => '',
		'mysql_pass' => '',
		'mysql_data' => ''
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
			case "query":
				echo "= Query result: " . $message . "\n";
				fwrite($this->debug_file, 'query result: ' . $message . "\n");
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

		if(!file_exists('config-bot.txt'))
		{
			$this->config_file = fopen('config-bot.txt', 'a+');
			fwrite($this->config_file, json_encode($this->empty_config));
			fwrite($this->debug_file, "Created config-bot file, wrote inside: " . json_encode($this->empty_config) . "\n");
			fclose($this->config_file);

			$this->config_file = fopen('config-bot.txt', 'a+');
			$this->config = json_decode(fread($this->config_file, filesize('config-bot.txt')));
			$this->confih = $this->config;
			fclose($this->config_file);
		}
		else
		{
			$this->config_file = fopen('config-bot.txt', 'c+');
			$this->config = json_decode(fread($this->config_file, filesize('config-bot.txt')));
			$this->confih = $this->config;
			fclose($this->config_file);
		}

		if(!file_exists('config-mysql.txt'))
		{
			$this->mysql_file = fopen('config-mysql.txt', 'a+');
			fwrite($this->mysql_file, json_encode($this->empty_mysql_config));
			fwrite($this->debug_file, "Created config-mysql file, wrote inside: " . json_encode($this->empty_mysql_config) . "\n");
			fclose($this->mysql_file);

			$this->mysql_file = fopen('config-mysql.txt', 'a+');
			$this->mysql = json_decode(fread($this->mysql_file, filesize('config-mysql.txt')));
			$this->mysqm = $this->mysql;
			fclose($this->mysql_file);
		}
		else
		{
			$this->mysql_file = fopen('config-mysql.txt', 'c+');
			$this->mysql = json_decode(fread($this->mysql_file, filesize('config-mysql.txt')));
			$this->mysqm = $this->mysql;
			fclose($this->mysql_file);
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
			case "update":
				switch(strtolower($ex[1]))
				{
					case "server":
						$this->config->server = $ex[2];
						$this->message('message', 'Server updated to, "' . $ex[2] . '".');
					break;
					case "port":
						$this->config->port = $ex[2];
						$this->message('message', 'Port updated to, "' . $ex[2] . '".');
					break;
					case "pass":
						$this->config->pass = $ex[2];
						$this->message('message', 'Pass updated to, "' . $ex[2] . '".');
					break;
					case "nick":
						$this->config->nick = $ex[2];
						$this->message('message', 'Nick updated to, "' . $ex[2] . '".');
					break;
					case "user":
						$this->config->user = $ex[2];
						$this->message('message', 'User updated to, "' . $ex[2] . '".');
					break;
					case "channel":
						$this->config->channel = $ex[2];
						$this->message('message', 'Channel updated to, "' . $ex[2] . '".');
					break;
					case "mysql-host":
						$this->mysql->mysql_host = $ex[2];
						$this->message('message', 'Mysql-host updated to, "' . $ex[2] . '".');
					break;
					case "mysql-user":
						$this->mysql->mysql_user = $ex[2];
						$this->message('message', 'Mysql-host updated to, "' . $ex[2] . '".');
					break;
					case "mysql-pass":
						$this->mysql->mysql_pass = $ex[2];
						$this->message('message', 'Mysql-pass updated to, "' . $ex[2] . '".');
					break;
					case "mysql-data":
						$this->mysql->mysql_data = $ex[2];
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
						$this->config->server = $this->confih->server;
						$this->message('message', 'Using default serer, "' . $this->confih->server . '".');
					break;
					case "port":
						$this->config->port = $this->confih->port;
						$this->message('message', 'Using default port, "' . $this->confih->port . '".');
					break;
					case "pass":
						$this->config->pass = $this->confih->pass;
						$this->message('message', 'Using default pass, "' . $this->confih->pass . '".');
					break;
					case "nick":
						$this->config->nick = $this->confih->nick;
						$this->message('message', 'Using default nick, "' . $this->confih->nick . '".');
					break;
					case "user":
						$this->config->user = $this->confih->user;
						$this->message('message', 'Using default user, "' . $this->confih->user . '".');
					break;
					case "channel":
						$this->config->channel = $this->confih->channel;
						$this->message('message', 'Using default channel, "' . $this->confih->channel . '".');
					break;
					case "mysql-server":
						$this->mysql->mysql_server = $this->mysqm->mysql_server;
						$this->message('message', 'Using default mysql-server, "' . $this->mysqm->mysql_server . '".');
					break;
					case "mysql-user":
						$this->mysql->mysql_user = $this->mysqm->mysql_user;
						$this->message('message', 'Using default mysql-user, "' . $this->mysqm->mysql_user . '".');
					break;
					case "mysql-pass":
						$this->mysql->mysql_pass = $this->mysqm->mysql_pass;
						$this->message('message', 'Using default mysql-pass, "' . $this->mysqm->mysql_pass . '".');
					break;
					case "mysql-data":
						$this->mysql->mysql_data = $this->mysqm->mysql_data;
						$this->message('message', 'Using default mysql-data, "' . $this->mysqm->mysql_data . '".');
					break;
					default:
						$this->message('error', 'Unknown key, "' . $ex[2] . '".');
						$this->message('info', 'Type, "help keys" for a list of keys.');
					break;
				}
			break;
			case "mysql":
				if($ex[1] == "true" && $this->sql == false)
				{
					$this->sql = true;
					if($this->mysql->mysql_data == null || $this->mysql->mysql_data == "")
					{
						$this->connection = new mysqli($this->mysql->mysql->host, $this->mysql->mysql_user, $this->mysql->mysql_pass);
					}
					else
					{
						$this->connection = new mysqli($this->mysql->mysql_host, $this->mysql->mysql_user, $this->mysql->mysql_pass, $this->mysql->mysql_data);
					}

					if($this->connection->sqlstate != null && $this->connection)
					{
						fwrite($this->debug_file, "MySQL connection online.\n");
						$this->message('message', 'MySQL connection online.');
					}
					else
					{
						$this->message('error', 'Unable to connect; check debug logs for more info.');
						if($this->connection->errno == null && $this->connection->error == null)
						{
							fwrite($this->debug_file, "If all the variables below are null, your server is offline or variables are not set.\n");
							fwrite($this->debug_file, "More info: " . json_encode($this->connection) . "\n");
							$this->connection->close();
						}
						else
						{
							fwrite($this->debug_file, "Error number: " . $this->connection->errno . "\n");
							fwrite($this->debug_file, "Error string: " . $this->connection->error . "\n");
							$this->connection->close();
						}
					}
				}
				else if($ex[1] == "true" && $this->sql == true)
				{
					$this->message('error', 'MySQL is already enabled.');

					if($this->connection)
					{
						$this->message('info', 'MySQL connection is online.');
					}
					else
					{
						$this->sql = false;
						$this->message('info', 'MySQL connection is online, retry the command.');
					}
				}
				else if($ex[1] == "false" && $this->sql == true)
				{
					$this->sql = false;
					$this->connection->close();
					$this->message('message', 'MySQL disabled.');
				}
				else if($ex[1] == "false" && $this->sql == false)
				{
					$this->message('error', 'MySQL is already disabled.');
				}
				else
				{
					$this->message('error', 'Value must be true or false, not, "' . $ex[2] . '".');
				}
			break;
			case "query":
				for($i = 1; $i < count($ex); $i++)
				{
					if($i == 1)
					{
						$query = $ex[$i];
					}
					else
					{
						$query = $query . ' ' . $ex[$i];
					}
				}
				
				if(strtolower($ex[1]) == 'select')
				{
					$result = $this->connection->query($query);
					while($row = $result->fetch_assoc())
					{
						$data[] = $row;
					}
					
					$this->message('query', json_encode($data));
				}
				else
				{
					$result = $this->connection->query($query);
					$this->message('query', json_encode($result);
				}
			break;
			case "check":
				if($ex[1] == "connection")
				{
					$found = true;
					if($this->connection->errno == null || $this->connection->error == null || !$this->connection)
					{
						$this->message('info', 'Connection is offline.');
					}
					else
					{
						$this->message('info', 'Connection is online.');
					}
				}
				
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
			case "exit":
			case "stop":
			case "quit":
			case "close":
			case "leave":
			case "destroy":
				$temp_destroy_config = fopen('config-bot.txt', 'w+');
				fclose($temp_destroy_config);
				$temp_config = fopen('config-bot.txt', 'a+');
				fwrite($temp_config, json_encode($this->config));
				fclose($temp_config);
				$temp_destroy_config_ = fopen('config-mysql.txt', 'w+');
				fclose($temp_destroy_config_);
				$temp_config_ = fopen('config-mysql.txt', 'a+');
				fwrite($temp_config_, json_encode($this->mysql));
				fclose($temp_config_);

				fclose($handle);
				$this->message('message', 'Goodbye!');
				exit;
			break;
			default:
				$this->message('error', 'Unknown command, "' . $ex[0] . '". Type, "help" for a list of commands.');
			break;
		}
		
		$this->console();
	}
	
	public function login()
	{
		$this->socket = fsockopen($this->config->server, $this->config->port, $errno, $errstr);
		
		if(!$this->socket)
		{
			$this->message('error', 'Unable to open the socket.');
			$this->message('error', 'Error number: ' . $errno . '.');
			$this->message('error', 'Error string: ' . $errstr . '.');
			$this->console();
		}

		$this->message('info', 'Socket opened.');		
		fputs($this->socket, "PASS " . $this->config->pass . "\r\n"); $this->message('sent', 'Sent pass, "' . $this->config->pass . '".');
		fputs($this->socket, "NICK " . $this->config->nick . "\r\n"); $this->message('sent', 'Sent nick, "' . $this->config->nick . '".');
		fputs($this->socket, "USER " . $this->config->user . "\r\n"); $this->message('sent', 'Sent user, "' . $this->config->user . '".');
		fputs($this->socket, "JOIN " . $this->config->channel . "\r\n"); $this->message('sent', 'Joined channel, "' . $this->config->channel . '".');
		$this->bot_();
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
					{
						$msg = $ex[$i];
					}
					else
					{
						$msg = $msg . ' ' . $ex[$i];
					}
				}
				
				fputs($this->socket, "PRIVMSG " . $ex[2] . " :" . $msg . "\n"); $this->message('sent', 'Sent a message to, "' . $ex[2] . '".'); $this->message('sent', 'Saying, "' . trim($msg) . '".');
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

				fputs($this->socket, "PRIVMSG " . $ex[2] . " :" . $msg . "\n"); $this->message('sent', 'Sent a message to, "' . $ex[2] . '".'); $this->message('sent', 'Saying, "' . trim($msg) . '".');
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
				$temp_destroy_config = fopen('config-bot.txt', 'w+');
				fclose($temp_destroy_config);
				$temp_config = fopen('config-bot.txt', 'a+');
				fwrite($temp_config, json_encode($this->config));
				fclose($temp_config);
				$temp_destroy_config_ = fopen('config-mysql.txt', 'w+');
				fclose($temp_destroy_config_);
				$temp_config_ = fopen('config-mysql.txt', 'a+');
				fwrite($temp_config_, json_encode($this->mysql));
				fclose($temp_config_);
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
