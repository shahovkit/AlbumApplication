<?php
namespace Album\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\FlashMessenger;

class ShowMessages extends AbstractHelper
{
    public function __invoke()
    {

        $messenger = new FlashMessenger();
        $error_messages = $messenger->getErrorMessages();
        $messages = $messenger->getMessages();
        $result = '';
        if (count($error_messages)) {
            $result .= '<div class="alert alert-danger" role="alert">';
            foreach ($error_messages as $message) {
                $result .= '<p>' . $message . '</p>';
            }
            $result .= '</div>';
        }
        if (count($messages)) {
            $result .= '<div class="alert alert-warning" role="alert">';
            foreach ($messages as $message) {
                $result .= '<p>' . $message . '</p>';
            }
            $result .= '</div>';
        }
        return $result;
    }
}