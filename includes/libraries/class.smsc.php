<?php

namespace DD_SMSC;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class SMSC - SMSC.RU API
 *
 * API Class for the SMS service https://smsc.ru/?myDevDiamond
 * (smsc.ru) версия 3.4 (05.03.2015)
 *
 *  Examples:
 *      include "class.smsc.php";
 *      $smsc = new \DD_SMSC\SMSC( array(
 *          'login'      => 'SMSClogin',
 *          'password'   => 'SMSCpassword',
 *          'email_from' => 'manager@yoursite.com',
 *          'charset'    => 'utf-8',
 *          'is_post'    => false,
 *          'is_https'   => false,
 *          'is_debug'   => false,
 *      ));
 *      list($sms_id, $sms_cnt, $cost, $balance) = $smsc->send_sms("79999999999", "Your password", 1);
 *      list($sms_id, $sms_cnt, $cost, $balance) = $smsc->send_sms("79999999999", "Your password", 0, 0, 0, 0, false, "maxsms=3");
 *      list($sms_id, $sms_cnt, $cost, $balance) = $smsc->send_sms("79999999999", "0605040B8423F0DC0601AE02056A0045C60C036D79736974652E72750001036D7973697465000101", 0, 0, 0, 5, false);
 *      list($sms_id, $sms_cnt, $cost, $balance) = $smsc->send_sms("79999999999", "", 0, 0, 0, 3, false);
 *      list($sms_id, $sms_cnt, $cost, $balance) = $smsc->send_sms("dest@mysite.com", "Your password", 0, 0, 0, 8, "source@mysite.com", "subj=Confirmation");
 *      list($cost, $sms_cnt) = $smsc->get_sms_cost("79999999999", "You have successfully logged in!");
 *      $smsc->send_sms_mail("79999999999", "Your password", 0, "0101121000");
 *      list($status, $time) = $smsc->get_status($sms_id, "79999999999");
 *      $balance = $smsc->get_balance();
 *
 * @class   SMSC
 * @author  DevDiamond <me@devdiamond.com>
 * @author  SMSC.RU
 * @package SMSC
 * @version 1.0.0
 */
class SMSC
{
	const SMSC_LINK = '<a target="_blank" href="https://smsc.ru/?myDevDiamond" title="SMSC Service">SMSC</a>';

	private $SMSC_LOGIN;
	private $SMSC_PASSWORD;
	private $SMSC_POST;
	private $SMSC_HTTPS;
	private $SMSC_CHARSET;
	private $SMSC_DEBUG;
	private $SMTP_FROM;
	
	/**
	 * SMSC constructor.
	 *
	 * @param array $args {
	 *      Array with adjustment for service SMSC
	 *
	 *      @type string @login      - (required) Login SMSC
	 *      @type string @password   - (required) Password SMSC
	 *      @type string @email_from - (optional) Email From. Example: manager@yoursite.com (default: '')
	 *      @type string @charset    - (optional) Charset message. <utf-8>, <koi8-r> or <windows-1251> (default: 'windows-1251')
	 *      @type bool   @is_post    - (optional) Is POST (default: false)
	 *      @type bool   @is_https   - (optional) Is https (default: false)
	 *      @type bool   @is_debug   - (optional) is Debug (default: false)
	 * }
	 */
	public function __construct( $args )
	{
		$args = array_merge(array(
			'login'      => '',
			'password'   => '',
			'email_from' => '',
			'charset'    => 'windows-1251',
			'is_post'    => false,
			'is_https'   => false,
			'is_debug'   => false,
		), $args);

		if ( ! $args['login'] || ! $args['password'] )
			die('Error data');

		$this->SMSC_LOGIN    = $args['login'];
		$this->SMSC_PASSWORD = $args['password'];
		$this->SMTP_FROM     = ($args['email_from'] ? $args['email_from'] : '');
		$this->SMSC_CHARSET  = (in_array( $args['charset'], array('utf-8','koi8-r','windows-1251')) ? $args['charset'] : 'windows-1251');
		$this->SMSC_POST     = ($args['is_post'] ? true : false );
		$this->SMSC_HTTPS    = ($args['is_https'] ? true : false );
		$this->SMSC_DEBUG    = ($args['is_debug'] ? true : false );
	}

	/**
	 * Send SMS
	 *
	 * @param string $phones   - Phone list, separated by commas or semicolons
	 * @param string $message  - Sent message
	 * @param int    $translit - Optional. Translate or not in translit
	 * @param int    $time     - Optional. Required delivery time as a string (DDMMYYhhmm, h1-h2, 0ts, +m)
	 * @param int    $id       - Optional. Message identifier. Is a 32-bit number in the range of 1 to 2147483647.
	 * @param int    $format   - Optional. message format (0 - usual sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin,
	 *                                     5 - bin-hex, 6 - ping-sms, 7 - mms, 8 - mail, 9 - call)
	 * @param string $sender   - Optional. The sender's name (Sender ID). To disable the default Sender ID is necessary
	 *                                     for the name to convey a blank line or a point.
	 * @param string $query    - Optional. Line of additional parameters added to the URL-request
	 * @param array  $files    - Optional. An array of file paths to send mms or e-mail messages
	 *
	 * @return array  - if success (<id>, <amount sms>, <cost of sms>, <balance>) else (<id>, -<error code>)
	 */
	public function send_sms($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = '', $query = "", $files = array())
	{
		static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1", "mms=1", "mail=1", "call=1");

		$m = $this->_smsc_send_cmd("send", "cost=3&phones=".urlencode($phones)."&mes=".urlencode($message).
			"&translit=$translit&id=$id".($format > 0 ? "&".$formats[$format] : "").
			($sender === '' ? '' : "&sender=".urlencode($sender)).
			($time ? "&time=".urlencode($time) : "").($query ? "&$query" : ""), $files);

		// (id, cnt, cost, balance) or (id, -error)

		if ($this->SMSC_DEBUG)
		{
			if ($m[1] > 0)
				echo "Сообщение отправлено успешно. ID: $m[0], всего SMS: $m[1], стоимость: $m[2], баланс: $m[3].\n";
			else
				echo "Ошибка №", -$m[1], $m[0] ? ", ID: ".$m[0] : "", "\n";
		}

		return $m;
	}

	/**
	 * SMTP version of SMS sending method
	 *
	 * @param string $phones   - Phone list, separated by commas or semicolons
	 * @param string $message  - Sent message
	 * @param int    $translit - Optional. Translate or not in translit
	 * @param int    $time     - Optional. Required delivery time as a string (DDMMYYhhmm, h1-h2, 0ts, +m)
	 * @param int    $id       - Optional. Message identifier. Is a 32-bit number in the range of 1 to 2147483647.
	 * @param int    $format   - Optional. message format (0 - usual sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin,
	 *                                     5 - bin-hex, 6 - ping-sms, 7 - mms, 8 - mail, 9 - call)
	 * @param string $sender   - Optional. The sender's name (Sender ID). To disable the default Sender ID is necessary
	 *                                     for the name to convey a blank line or a point.
	 *
	 * @return bool
	 */
	public function send_sms_mail($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = "")
	{
		return mail("send@send.smsc.ru", "", $this->SMSC_LOGIN.":".$this->SMSC_PASSWORD.":$id:$time:$translit,$format,$sender:$phones:$message", "From: ".$this->SMTP_FROM."\nContent-Type: text/plain; charset=".$this->SMSC_CHARSET."\n");
	}

	/**
	 * Receiving SMS cost
	 *
	 * @param string $phones   - Phone list, separated by commas or semicolons
	 * @param string $message  - Sent message
	 * @param int    $translit - Optional. Translate or not in translit
	 * @param int    $format   - Optional. message format (0 - usual sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin,
	 *                                     5 - bin-hex, 6 - ping-sms, 7 - mms, 8 - mail, 9 - call)
	 * @param string $sender   - Optional. The sender's name (Sender ID). To disable the default Sender ID is necessary
	 *                                     for the name to convey a blank line or a point.
	 * @param string $query    - Optional. Line of additional parameters added to the URL-request
	 *
	 * @return array  - if success (<cost of sms>, <amount sms>) else (0, -<error code>)
	 */
	public function get_sms_cost($phones, $message, $translit = 0, $format = 0, $sender = '', $query = "")
	{
		static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1", "mms=1", "mail=1", "call=1");

		$m = $this->_smsc_send_cmd("send", "cost=1&phones=".urlencode($phones)."&mes=".urlencode($message).
			($sender == '' ? '' : "&sender=".urlencode($sender)).
			"&translit=$translit".($format > 0 ? "&".$formats[$format] : "").($query ? "&$query" : ""));

		// (cost, cnt) or (0, -error)

		if ($this->SMSC_DEBUG)
		{
			if ($m[1] > 0)
				echo "Стоимость рассылки: $m[0]. Всего SMS: $m[1]\n";
			else
				echo "Ошибка №", -$m[1], "\n";
		}

		return $m;
	}

	/**
	 * Checking the status of sent SMS or HLR-query
	 *
	 * @param int|string $id    - ID message or a comma separated list of ID
	 * @param int|string $phone - phone number or list of numbers separated by commas
	 * @param int        $all   - return all the data sent by SMS, including the text of the message (0.1 or 2)
	 *
	 * @return array {
	 *      Request for multiple two-dimensional array
	 *
	 *      For a single SMS-message:
	 *          (<status>, <time change>, <error code delivery>)
	 *
	 *      For HLR-query:
	 *          (<status>, <time change>, <error code sms> <code IMSI SIM-card>, <service center number>,
	 *          <registering country code> <area code> <name of the country of registration>, <title operator>,
	 *          <name of roaming the country>, <name of the roaming operator>)
	 *
	 *      if $all = 1 in addition return the elements at the end of the array:
	 *          (<time change>, <phone number>, <cost of>, <sender id>, <name of the status>, <text of the message>)
	 *
	 *      if $all = 2 in addition return elements:
	 *          <country>, <operator> and <region>
	 *
	 *      Under plural request:
	 *          if $all = 0 then for each message or HLR-request in addition returns:
	 *              <ID message> и <phone number>
	 *
	 *          if $all = 1 or $all = 2 then in answer is added
	 *              <ID message>
	 *
	 *      in the event of errors
	 *          (0, -<error code>)
	 * }
	 */
	public function get_status($id, $phone, $all = 0)
	{
		$m = $this->_smsc_send_cmd("status", "phone=".urlencode($phone)."&id=".urlencode($id)."&all=".(int)$all);

		// (status, time, err, ...) или (0, -error)

		if ( !strpos($id, ","))
		{
			if ($this->SMSC_DEBUG )
			{
				if ($m[1] != "" && $m[1] >= 0)
					echo "Статус SMS = $m[0]", $m[1] ? ", время изменения статуса - ".date("d.m.Y H:i:s", $m[1]) : "", "\n";
				else
					echo "Ошибка №", -$m[1], "\n";
			}

			if ($all && count($m) > 9 && (!isset($m[$idx = $all == 1 ? 14 : 17]) || $m[$idx] != "HLR")) // ',' в сообщении
				$m = explode(",", implode(",", $m), $all == 1 ? 9 : 12);
		}
		else
		{
			if (count($m) == 1 && strpos($m[0], "-") == 2)
				return explode(",", $m[0]);

			foreach ($m as $k => $v)
				$m[$k] = explode(",", $v);
		}

		return $m;
	}

	/**
	 * Obtain balance
	 *
	 * @return bool|mixed  - if success balance ELSE false
	 */
	public function get_balance()
	{
		$m = $this->_smsc_send_cmd("balance"); // (balance) или (0, -error)

		if ( $this->SMSC_DEBUG )
		{
			if (!isset($m[1]))
				echo "Сумма на счете: ", $m[0], "\n";
			else
				echo "Ошибка №", -$m[1], "\n";
		}

		return isset($m[1]) ? false : $m[0];
	}

	/**
	 * Call request. It generates a URL and make 3 attempts to read
	 *
	 * @param string $cmd
	 * @param string $arg
	 * @param array  $files
	 *
	 * @return array
	 */
	private function _smsc_send_cmd($cmd, $arg = "", $files = array())
	{
		$url = ($this->SMSC_HTTPS ? "https" : "http")."://smsc.ru/sys/$cmd.php?login=".urlencode($this->SMSC_LOGIN)."&psw=".urlencode($this->SMSC_PASSWORD)."&fmt=1&charset=".$this->SMSC_CHARSET."&".$arg;

		$i = 0;
		do {
			if ($i)
			{
				sleep(2 + $i);

				if ($i == 2)
					$url = str_replace('://smsc.ru/', '://www2.smsc.ru/', $url);
			}

			$ret = $this->_smsc_read_url($url, $files);
		}
		while ($ret == "" && ++$i < 4);

		if ($ret == "")
		{
			if ($this->SMSC_DEBUG)
				echo "Ошибка чтения адреса: $url\n";

			$ret = ","; // фиктивный ответ
		}

		$delim = ",";

		if ($cmd == "status")
		{
			/**
			 * @var int $id
			 */
			parse_str($arg);

			if (strpos($id, ","))
				$delim = "\n";
		}

		return explode($delim, $ret);
	}

	/**
	 * URL Reading.
	 *
	 * To work must be available: curl or fsockopen (only http) or taken allow_url_fopen is enabled for the file_get_contents
	 *
	 * @param string $url
	 * @param array  $files
	 *
	 * @return mixed|string
	 */
	private function _smsc_read_url($url, $files)
	{
		$ret = "";
		$post = $this->SMSC_POST || strlen($url) > 2000 || $files;

		if (function_exists("curl_init"))
		{
			static $c = 0; // keepalive

			if (!$c)
			{
				$c = curl_init();
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($c, CURLOPT_TIMEOUT, 60);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
			}

			curl_setopt($c, CURLOPT_POST, $post);

			if ($post)
			{
				list($url, $post) = explode("?", $url, 2);

				if ($files)
				{
					parse_str($post, $m);

					foreach ($m as $k => $v)
						$m[$k] = isset($v[0]) && $v[0] == "@" ? sprintf("\0%s", $v) : $v;

					$post = $m;
					foreach ($files as $i => $path)
						if (file_exists($path))
							$post["file".$i] = function_exists("curl_file_create") ? curl_file_create($path) : "@".$path;
				}

				curl_setopt($c, CURLOPT_POSTFIELDS, $post);
			}

			curl_setopt($c, CURLOPT_URL, $url);

			$ret = curl_exec($c);
		}
		elseif ($files)
		{
			if ($this->SMSC_DEBUG)
				echo "Не установлен модуль curl для передачи файлов\n";
		}
		else
		{
			if (!$this->SMSC_HTTPS && function_exists("fsockopen"))
			{
				$m = parse_url($url);

				if (!$fp = fsockopen($m["host"], 80, $errno, $errstr, 10))
					$fp = fsockopen("212.24.33.196", 80, $errno, $errstr, 10);

				if ($fp)
				{
					fwrite($fp, ($post ? "POST $m[path]" : "GET $m[path]?$m[query]")." HTTP/1.1\r\nHost: smsc.ru\r\nUser-Agent: PHP".($post ? "\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($m['query']) : "")."\r\nConnection: Close\r\n\r\n".($post ? $m['query'] : ""));

					while (!feof($fp))
						$ret .= fgets($fp, 1024);
					list(, $ret) = explode("\r\n\r\n", $ret, 2);

					fclose($fp);
				}
			}
			else
				$ret = file_get_contents($url);
		}

		return $ret;
	}
}
