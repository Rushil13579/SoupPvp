<?php

namespace Rushil13579\SoupPvp;

use pocketmine\{Player, Server};

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;

use pocketmine\tile\Sign;
use pocketmine\item\Item;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

	public $refillcd = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->getResource("config.yml");

		$this->versionCheck();

		$this->getServer()->getCommandMap()->register('refill', new RefillCommand($this));
	}

	public function versionCheck(){
		if($this->getConfig()->get('version') != '1.1.0'){
			$this->getLogger()->warning('§cThe configuration file is outdated! Please delete it and restart your server to install the latest version');
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
	}

	public function onInteract(PlayerInteractEvent $e){
		$p = $e->getPlayer();
		if($e->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR || $e->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			$item = $p->getInventory()->getItemInHand();
			$i = "" . $item->getId() . ":" . $item->getDamage();
			if($i == $this->getConfig()->get("soup-item-id")){
				if(in_array($p->getLevel()->getName(), $this->getConfig()->get("soup-worlds"))){
					if(!$p->getGamemode() == 1 && !$p->getGamemode() == 3){
						if($p->getHealth() < 20){
							$p->setHealth($p->getHealth() + $this->getConfig()->get("heal-per-soup"));
							$p->getInventory()->setItemInHand(Item::get(Item::AIR));
						} else {
							$p->sendMessage($this->getConfig()->get("max-health-msg"));
						}
					} else {
						$p->sendMessage($this->getConfig()->get("no-soup-gamemode-msg"));
					}
				}
			}
		}

		$block = $e->getBlock();
		if($block->getId() == '63' or $block->getId() == '68'){
			$sign = $block->getLevel()->getTile($block);

			if($sign instanceof Sign){
				$signLines = $sign->getText();

				if($signLines[0] == '[REFILL]'){
					if($p->hasPermission('souppvp.refill.sign.use')){
						$this->refill($p);
					} else {
						$p->sendMessage($this->getConfig()->get('no-perm-msg'));
					}
				}
			}
		}
	}

	public function onChange(SignChangeEvent $ev){
		$player = $ev->getPlayer();
		$line1 = $ev->getLine(0);

		if($line1 == '[REFILL]'){
			if($player->hasPermission('souppvp.refill.sign.create')){
			} else {
				$ev->setLine(0, 'You cannot do this');
			}
		}
	}

	public function refill($player){
		$soupItemData = $this->getConfig()->get('soup-item-id');
		$array = explode(':', $soupItemData);
		$soupItem = Item::get((int)$array[0], (int)$array[1], 1);
		$i = 0;
		while($player->getInventory()->canAddItem($soupItem)){
			$player->getInventory()->addItem($soupItem);
			$i++;
		}
		if($i == 0){
			$player->sendMessage($this->getConfig()->get('inv-full-msg'));
		} else {
			$player->sendMessage(str_replace('{count}', $i, $this->getConfig()->get('refilled-msg')));
		}
	}
}
