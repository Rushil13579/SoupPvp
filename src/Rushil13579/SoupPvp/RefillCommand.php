<?php

namespace Rushil13579\SoupPvp;

use pocketmine\{
    Server,
    Player
};

use pocketmine\command\{
    Command,
    CommandSender
};

use Rushil13579\SoupPvp\Main;

class RefillCommand extends Command {

    /** @var Main */
    private $main;

    public function __construct(Main $main){
        $this->main = $main;

        parent::__construct('refill', 'Allows a player to refill their soup');
        $this->setPermission('souppvp.refill');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage($this->main->getConfig()->get('not-player-msg'));
            return false;
        }

        if(!$this->testPermission($sender)){
            $sender->sendMessage($this->main->getConfig()->get('no-perm-msg'));
            return false;
        }

        $this->main->refill($sender);
    }
}