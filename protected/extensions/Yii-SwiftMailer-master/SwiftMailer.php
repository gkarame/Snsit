<?php
/**
 * Swift Mailer wrapper class.
 *
 * @author Sergii 'b3atb0x' hello@webkadabra.com
 */
class SwiftMailer extends CComponent
{

	/**
	 * smtp, sendmail or mail
	 */
	public $mailer = 'sendmail'; // 
	/**
	 * SMTP outgoing mail server host
	 */
	public $host;
	/**
	 * Outgoing SMTP server port
	 */
	public $port = 25;
	/**
	 * SMTP Password
	 */
	public $username;
	/**
	 * SMTP email
	 */
	public $password;
	/**
	 * SMTP security
	 */
	public $security;
	/**
	 * @param string Message Subject
	 */
	public $Subject;
	/**
	 * @param mixed Email addres messages are going to be sent "from"
	 */
	public $From;
	/**
	 * @param string HTML Message Body
	 */
	public $body;
	/**
	 * @param string Alternative message body (plain text)
	 */
	public $altBody = null;

	public $_addresses = array();
	public $_ccs = array();
	public $_bccs = array();
	protected $_attachments = array();

	public $logMailerActivity = true;

	public function init()
	{
		spl_autoload_unregister(array('YiiBase', 'autoload'));
		require_once(dirname(__FILE__) . '/lib/swift_required.php');
		spl_autoload_register(array('YiiBase', 'autoload'));
	}

	public function AddAddress($address)
	{
		if (is_array($address))
		{
			$this->_addresses = array_merge($this->_addresses, $address);
		}
		else
		{
			if (!in_array($address, $this->_addresses)) 
			{
				$this->_addresses[] = $address;
			}
		}
		return $this;
	}
	public function AddCCs($address)
	{
		if (is_array($address))
		{
			$this->_ccs = array_merge($this->_ccs, $address);
		}
		else
		{
			if (!in_array($address, $this->_ccs)) 
			{
				$this->_ccs[] = $address;
			}
		}
		return $this;
	}
	public function AddBccs($address)
	{
		if (is_array($address))
		{
			$this->_bccs = array_merge($this->_bccs, $address);
		}
		else
		{
			if (!in_array($address, $this->_bccs)) 
			{
				$this->_bccs[] = $address;
			}
		}
		return $this;
	}
	public function AddFile($address)
	{
		if (!in_array($address, $this->_attachments))
			$this->_attachments[] = $address;
		return $this;
	}

	public function MsgHTML($body)
	{
		$this->body = $body;
		if ($this->altBody == null) {
			$this->altBody = strip_tags($this->body);
		}
		return $this;
	}

	/**
	 * Helper function to send emails like this:
	 * <code>
	 *        Yii::app()->mailer->AddAddress($email);
	 *        Yii::app()->mailer->Subject = $newslettersOne['name'];
	 *         Yii::app()->mailer->MsgHTML($template['content']);
	 *        Yii::app()->mailer->Send();
	 * </code>
	 * @return boolean Whether email has been sent or not
	 */
	public function Send($getResult = false)
	{
		//Create the Transport
		$transport = $this->loadTransport();

		//Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);


		//Create a message
		if (((strpos($this->Subject, 'WMS Inquiry - SR#') !== false || strpos($this->Subject, 'Change Request - SR#') !== false) && strpos($this->Subject, 'Re-assigned')  !== false ) || (strpos($this->Subject, 'Change Request - New - SR#') !== false || strpos($this->Subject, 'WMS Inquiry - New - SR#') !== false)) {
			$message = Swift_Message::newInstance($this->Subject)
			->setFrom($this->From)
			->setTo($this->_addresses)
			->setCc($this->_ccs)
			->setPriority(1)
			->setBCc($this->_bccs);
		}else{
			$message = Swift_Message::newInstance($this->Subject)
			->setFrom($this->From)
			->setTo($this->_addresses)
			->setCc($this->_ccs)
			->setBCc($this->_bccs);
		}		
			
		if ($this->body) {
			$message->addPart($this->body, 'text/html');
		}
		if ($this->altBody) {
			$message->setBody($this->altBody);
		}


		if ($this->_attachments) {
			foreach ($this->_attachments as $path)
				$message->attach(Swift_Attachment::fromPath($path));
		}

		$result = $mailer->send($message);

		if ($this->logMailerActivity == true) {
			if (!$result) {
				$logMessage = 'Failed to send "' . $this->Subject . '" email to [' . implode(', ', $this->addressesFlat()) . ']'
					. "\nMessage:\n"
					. ($this->altBody ? $this->altBody : $this->body);
				Yii::log($logMessage, 'error', 'appMailer');
			} else {
				$logMessage = 'Sent email "' . $this->Subject . '" to [' . implode(', ', $this->addressesFlat()) . ']'
					. "\nMessage:\n"
					. ($this->altBody ? $this->altBody : $this->body);
				Yii::log($logMessage, 'trace', 'appMailer');
			}
		}
		$this->ClearAddresses();
		if ($getResult)
			return $result;
	}

	public function ClearAddresses()
	{
		$this->_addresses = array();
	}
	public function ClearCcs()
	{
		$this->_ccs = array();
	}
	public function ClearBccs()
	{
		$this->_bccs = array();
	}
	public function ClearFiles()
	{
		$this->_attachments = array();
	}

	public function addressesFlat()
	{
		$return = array();
		if (!empty($this->_addresses)) {
			foreach ($this->_addresses as $address) {
				if (is_array($address)) {
					$return[] = $address[0];
				} else
					$return[] = $address;
			}
		}

		return $return;
	}

	/* Helpers */
	public function preferences()
	{
		return Swift_Preferences;
	}

	public function attachment()
	{
		return Swift_Attachment;
	}

	public function newMessage($subject)
	{
		return Swift_Message::newInstance($subject);
	}

	public function mailer($transport = null)
	{
		return Swift_Mailer::newInstance($transport);
	}

	public function image()
	{
		return Swift_Image;
	}

	public $sendmailCommand = '/usr/bin/sendmail -t';

	public function smtpTransport($host = null, $port = null, $security = null)
	{
		return Swift_SmtpTransport::newInstance($host, $port, $security);
	}

	public function sendmailTransport($command = null)
	{
		return Swift_SendmailTransport::newInstance($command);
	}

	public function mailTransport()
	{
		return Swift_MailTransport::newInstance();
	}

	protected function loadTransport()
	{
		if ($this->mailer == 'smtp') {
			$transport = self::smtpTransport($this->host, $this->port, $this->security);

			if ($this->username)
				$transport->setUsername($this->username);
			if ($this->password)
				$transport->setPassword($this->password);
		} elseif ($this->mailer == 'mail') {
			$transport = self::mailTransport();
		} elseif ($this->mailer == 'sendmail') {
			$transport = self::sendmailTransport($this->sendmailCommand);
		}

		return $transport;
	}


}