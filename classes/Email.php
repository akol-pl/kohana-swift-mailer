<?php

defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email module
 *
 * @package    Email
 * @author     Alexey Popov
 * @author     Kohana Team
 * @copyright  (c) 2009-2013 Leemo studio
 * @license    http://kohanaphp.com/license.html
 */
class Email extends Kohana_Email
{
    /**
     * Email attachments
     *
     * @var string
     */
    protected $_attachments = [];

    /**
     * Add attachment do message
     *
     * @param  string $name name of the file in message attachment
     * @param  string $path local path to atachment
     * @return Email
     */
    public function attachment($name, $path)
    {
        $this->_attachments[$path] = $name;

        return $this;
    }

    /**
     * Sends prepared email
     *
     * @return void
     */
    public function send()
    {
        // Determine the message type
        $contentType = ($this->_html) ? 'text/html' : 'text/plain';

        if (!$this->_from)
        {
            $this->_from = $this->_config['from'];
        }

        $message = new Swift_Message($this->_subject, $this->_message, $contentType, 'utf-8');
        $message->setFrom($this->_from);

        if (count($this->_attachments) > 0)
        {
            foreach ($this->_attachments as $path => $name)
            {
                $message->attach(Swift_Attachment::fromPath($path)->setFilename($name));
            }
        }

        foreach (array('to', 'cc', 'bcc') as $param)
        {
            if (sizeof($this->{'_' . $param}) > 0)
            {
                $method = 'set' . UTF8::ucfirst($param);

                $message->$method($this->{'_' . $param});
            }
        }

        if (!empty($this->_reply_to))
        {
            $message->setReplyTo($this->_reply_to);
        }

        // Send message
        $this->_connection->send($message);

        return $this;
    }
    
    public function clearTo()
    {
        $this->_to = [];
    }
    
}

// End Kohana_Email