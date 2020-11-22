<?php

namespace SoupPvp;

use pocketmine\Player;
use pocketmine\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getResource("config.yml");
	}

	public function onInteract(PlayerInteractEvent $e){
		$p = $e->getPlayer();
		if($e->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR || $e->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			$item = $p->getInventory()->getItemInHand();
			$i = "" . $item->getId() . ":" . $item->getDamage();
			if($i == $this->getConfig()->get("soup-item-id")){
				if(in_array($p->getLevel()->getName(), $this->getConfig()->get("soup-worlds"))){
					if(!$p->getGamemode() == 1 && $p->getGamemode() == 3){
						if($p->getHealth() < 20){
							$p->setHealth($p->getHealth() + $this->getConfig()->get("heal-per-soup"));
							$p->getInventory()->setItemInHand(ItemFactory::get(Item::AIR));
						} else {
							$p->sendMessage($this->getConfig()->get("max-health-msg"));
						}
					} else {
						$p->sendMessage($this->getConfig()->get("no-soup-gamemode-msg"));
					}
				}
			}
		}
	}
}