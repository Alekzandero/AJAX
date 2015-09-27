<?php
define('DEBUG', true);

if (DEBUG) {
	ini_set("display_startup_errors", "1");
	ini_set("display_errors", "1");
	error_reporting(E_ALL);
}


//TODO: Передаем серверу JSON-пакет. Он его разбирает и осуществляет соответствующие действия.
//		В ответ направляет JSON-пакет, который разбирается на стороне клиента и тот вносит соответствующую информацию на страницу в браузере.
class Server
{
	// Параметры подключения к SQL-серверу с данными фактов
	const SQL_SERVER = 'localhost';
	const SQL_USER = 'ajax_user';
	const SQL_PASS = 'ytrewq7';
	const SQL_DB = 'ajax_database';

	private $conn;

	// Запуск сервера
	static function start()
	{
		return new self;
	}

	// Получаем информацию для сервера и обрабатываем ее
	public function process()
	{
		$this->setValue('nickname', $_GET['nickname']);
		return $this;
	}

	// Выводим информацию от сервера на экран
	public function display()
	{
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-Type: text/html; charset=utf-8');

		echo 'Записано на сервер: ' . $this->getValue('nickname');
	}

	// Выводим информацию от сервера на экран
	public function displayText()
	{
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0

		// Обычный POST
		// header('Content-Type: text/html; charset=utf-8');
		//echo 'Получено сервером: '; 
		//print_r($_POST); //печатаем массив, переданный в формате hello=world&good=morning
		
		// JSON
        header('Content-type: application/json; charset=utf-8');
		$data = json_decode(file_get_contents('php://input'), true); // Раскодируем JSON в массив
		$data["hello"] .= " is " . time();
		echo json_encode($data); // Кодируем массив в JSON
		// echo print_r($data, true); // Выводим массив
	}

	// Конструктор класса
	private function __construct()
	{
		// Подключаемся к базе данных 
		$this->conn = new mysqli(self::SQL_SERVER, self::SQL_USER, self::SQL_PASS, self::SQL_DB);
		if ($this->conn->connect_error) {
			if (DEBUG) {
		    	die('Ошибка соединения: '.mysql_error());
			}
		}
		return $this;
	}

	private function __destruct()
	{
		// Закрываем соединение с базой
		$this->conn->close();
	}

	// Задаем значение в базе данных
	private function setValue($value_name, $value_text)
	{
		return $this->conn->query('UPDATE ajax_table SET value_text = \'' . $value_text . '\' WHERE value_name = \'' . $value_name . '\'');
	}

	// Получаем значение из базы данных
	private function getValue($value_name)
	{
		$res = $this->conn->query("SELECT value_text FROM ajax_table WHERE value_name='$value_name'");
		$row = $res->fetch_object();
		return $row->value_text;
	}
}

//Server::start()->process()->display();
Server::start()->displayText();

?>