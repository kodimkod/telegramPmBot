<?php

namespace TelegramPmBot;

class DatabaseFacade extends \TelegramBot\DatabaseFacade
{

    /**
     * @param int | null $userId
     */
    public function writeLastMessagedUser($userId)
    {
        return $this->writer->writeNewCallbackLike($messageId, $channelId, $userId, 'dislike');
    }

}
