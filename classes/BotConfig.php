<?php

namespace TelegramPmBot;


class BotConfig extends \TelegramBot\BotConfig
{

    public function __construct($appRoot)
    {
        $this->appRoot = $appRoot;
        $ini_array = parse_ini_file($appRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "settings.ini");
        $this->validateSettings($ini_array);
        $this->token = $ini_array['token'];
        $this->dbHost = $ini_array['dbHost'];
        $this->dbName = $ini_array['dbName'];
        $this->dbUser = $ini_array['dbUser'];
        $this->dbPassword = $ini_array['dbPassword'];
        $this->botId = $ini_array['botId'];
       $this->authorizedUsers = $ini_array['authorizedUsers'];
    }

   
    /**
     * @param array $settings
     * @throws UnexpectedValueException
     */
    protected function validateSettings(array $settings)
    {
        if (!isset($settings['token']) || strlen($settings['token']) < 7) {
            throw new UnexpectedValueException('Wrong bot token');
        }
        if (!isset($settings['dbHost']) || strlen($settings['dbHost']) < 2) {
            throw new UnexpectedValueException('Wrong db host, probably less than 2 characters.');
        }
        if (!isset($settings['dbName']) || strlen($settings['dbName']) < 2) {
            throw new UnexpectedValueException('Wrong db name, probably less than 2 characters.');
        }
        if (!isset($settings['dbUser']) || strlen($settings['dbUser']) < 2) {
            throw new UnexpectedValueException('Wrong db user, probably less than 2 characters.');
        }
        if (!isset($settings['dbPassword']) || strlen($settings['dbPassword']) < 2) {
            throw new UnexpectedValueException('Wrong db password, probably less than 2 characters.');
        }
        if (!isset($settings['botId']) || strlen($settings['botId']) < 5) {
            throw new UnexpectedValueException('Wrong bot id, probably less than 5 characters.');
        }
        if (!isset($settings['welcomeUserName']) || strlen($settings['welcomeUserName']) < 2) {
            throw new UnexpectedValueException('Wrong welcome user name, probably less than 2 characters.');
        }
        if (!isset($settings['welcomeUserId']) || strlen($settings['welcomeUserId']) < 5) {
            throw new UnexpectedValueException('Wrong welcome user id, probably less than 5 characters.');
        }
        if (!isset($settings['welcomeUserGroupId']) || strlen($settings['welcomeUserGroupId']) < 5) {
            throw new UnexpectedValueException('Wrong welcome user group id, probably less than 5 characters.');
        }
        if (!isset($settings['ownGroupIds']) || !is_array($settings['ownGroupIds'])) {
            throw new UnexpectedValueException('Wrong own groups, not array?');
        }
        if (!isset($settings['callbackButton1'])) {
            throw new UnexpectedValueException('No text for callback button 1');
        }
        if (!isset($settings['callbackButton2'])) {
            throw new UnexpectedValueException('No text for callback button 2');
        }
    }

}
