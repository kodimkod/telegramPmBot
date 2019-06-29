<?php

namespace TelegramPmBot;

use TelegramBot\{
    TelegramMessages,
    DatabaseFacade,
    Factory
};
use TelegramPmBot\{
    BotConfig
};

class BotController {

    const LOG_TYPE_UNRECOGNIZED = 0;
    const LOG_TYPE_NORMAL_MESSAGE = 1;

    /**
     * @var TelegramMessages
     */
    protected $messages;

    /**
     * @var BotConfig
     */
    protected $config;

    /**
     * @var DatabaseFacade
     */
    protected $database;

    /**
     * @var Factory $factory
     */
    protected $factory;

    /**
     * @param BotConfig $config
     * @param TelegramMessages $messages
     * @param DatabaseFacade $facade
     * @param Factory $factory
     */
    public function __construct(BotConfig $config, TelegramMessages $messages, DatabaseFacade $facade,
            Factory $factory) {
        $this->config = $config;
        $this->messages = $messages;
        $this->database = $facade;
        $this->factory = $factory;
    }

    public function run() {
        $offset = $this->database->readLastMessageOffset();
        $offset = (int) $offset + 1;
        echo $offset . PHP_EOL;
        $messages = $this->messages->getUpdates($this->config->getToken(), $offset);
        if ($this->shouldNotProcessMessages($messages)) {
            $this->logContent($messages, self::LOG_TYPE_UNRECOGNIZED);
            return false;
        }
        foreach ($messages['result'] as $message) {
            $this->logContent($message, self::LOG_TYPE_NORMAL_MESSAGE);
            $this->processSingleMessage($message);
            if (isset($message['update_id'])) {
                $this->saveLastUpdateId($message['update_id']);
            }
        }
    }

    /**
     * @param mixed $messages
     * @return bool
     */
    protected function shouldNotProcessMessages($messages): bool {
        if (!is_array($messages) || empty($messages)) {
            return true;
        }
        if (!isset($messages['result']) || !is_array($messages['result']) || empty($messages['result'])) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $content
     * @param int $messageType
     */
    protected function logContent($content, $messageType) {
        $contentExtractor = $this->factory->getContentExtractor($content);
        switch ($messageType) {
            case self::LOG_TYPE_NORMAL_MESSAGE:
                $this->database->writeNormalMessageLog($contentExtractor->getMessageId(), 'normal',
                        $contentExtractor->getGroupId(), $contentExtractor->getGroupName(),
                        $contentExtractor->getMessage());
                break;
            case self::LOG_TYPE_UNRECOGNIZED:
            default:
                $this->database->writeNormalMessageLog($contentExtractor->getMessageId(), 'unrecognized',
                        $contentExtractor->getGroupId(), $contentExtractor->getGroupName(),
                        $contentExtractor->getMessage());
                break;
        }
    }

    /**
     * @param string $updateId
     */
    protected function saveLastUpdateId(string $updateId) {
        $this->database->writeLastMessageOffset($updateId);
    }

    /**
     * @param array $message
     */
    protected function processSingleMessage($message) {
        $contentExtractor = $this->factory->getContentExtractor($message);
        if ($contentExtractor->isPrivateMessage()) {
            $result = $this->processPrivateMessage($contentExtractor);
            return $result;
        }
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function processPrivateMessage($contentExtractor): bool {
        setlocale(LC_ALL, "en_US.UTF-8");
        $receivers = $this->config->getAuthorizedUsers();
        $result = true;
        if (!in_array($contentExtractor->getGroupId(), $receivers)) {
            foreach ($receivers as $receiver) {
                $resultText = $this->messages->sendMessage($this->config->getToken(),
                        $receiver, "[" . $contentExtractor->getGroupId() . "](tg://user?id=" . $contentExtractor->getGroupId() . ")" .
                        ' @' . $contentExtractor->getUserName() . ' ' .
                        $contentExtractor->getUserFullName() .
                        ' : ' . $contentExtractor->getMessageContent(), 'Markdown');
                if (isset($resultText['ok']) && $resultText['ok'] == true) {
                    $result &= true;
                } else {
                    $result = false;
                }
            }
        } else { // message sent from one of authorized users
            
        }

        return $result;
    }

}
