<?php

namespace TelegramPmBot;

class DatabaseWriter extends \TelegramBot\DatabaseWriter
{

    /**
     * @param int $updateId
     * @return bool
     * @throws \ErrorException
     */
    public function writeLastMessageOffset($updateId)
    {
        $command = $this->factory->getWriteLastMessageOffsetCommand($updateId);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing last message offset: ' . $exception->getMessage());
        }
        return $status;
    }

}
