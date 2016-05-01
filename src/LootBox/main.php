<?php
namespace LootBox;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\item\ItemBlock;

class main extends PluginBase implements Listener{
	
	private $config;
	private $items;
	public function onEnable(){
		@mkdir($this->getDataFolder());
                $this->saveDefaultConfig();
                $this->reloadConfig();
		$this->getLogger()->info("Loaded! :)");
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
		$this->saveDefaultConfig();
		$this->config = $this->getConfig()->getAll();
		$num = 0;
		foreach ($this->config["crate"] as $i){
			$e = explode(":", $i);
			$this->items[$num] = array($e[0],$e[1],0);
			$num++;
		}
	}
	public function onTap(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		if ($player instanceof  Player){
		if ($ev->getBlock()->getId() === 54 and $player->getInventory()->getItemInHand()->getId() === 341){
			$ev->setCancelled(true);
			//$this->giveItems($player);
			$x = $ev->getBlock()->getX();
			$y = $ev->getBlock()->getY();
			$z = $ev->getBlock()->getZ();
			$this->giveItems($player, $x, $y, $z);
			$level = $ev->getBlock()->getLevel();
// 			$id = Block::DIAMOND_BLOCK;
// 			$level->setBlockIdAt($x, $y, $z, $id);
			if ($level->getBlockIdAt($x, $y + 1, $z) === 0){
			$r = rand(0, 1);
			$num = 0;
			while ($num <= $r){
 			$level->dropItem(new Vector3($x, $y + 10, $z), Item::get(351,2));
 			$num++;
			}
			}
		}
		}
	}
	public function playerHeldItem(PlayerItemHeldEvent $ev){
		$item = $ev->getItem()->getId();
		$player = $ev->getPlayer();
		if ($item === 341){
			$msg = TextFormat::GOLD."[LootBox]".TextFormat::BLUE." Tap a chest to win rare items!";
			if (($hud = $this->getServer()->getPluginManager()->getPlugin("BasicHUD")) !== null) {
				$hud->sendPopup($player,$msg);
			} else {
				$player->sendPopup($msg);
			}
		}
	}
	
	public function blockBreak(BlockBreakEvent $ev){
		$level = $ev->getBlock()->getLevel();
		switch  (strtolower($ev->getBlock()->getName())){
			case "stone":
				$ev->setCancelled(false);
				$num = rand(1,4);
				if ($num === 123 or $num === 324 or $num === 543 or $num === 1234 or $num === 1892){
					$level->dropItem(new Vector3($ev->getBlock()->getX(), $ev->getBlock()->getY(),$ev->getBlock()->getZ()), ItemBlock::get(251,15, rand(0,0)));
					return ;
				}
				if ($num === 999 or $num === 1532 or $num === 250){
					$level->dropItem(new Vector3($ev->getBlock()->getX(), $ev->getBlock()->getY(),$ev->getBlock()->getZ()), ItemBlock::get(295,0, rand(0,0)));
						
				}
				if ($num === 1234 or $num === 1321 or $num === 52 or $num === 500){
					$level->dropItem(new Vector3($ev->getBlock()->getX(), $ev->getBlock()->getY(),$ev->getBlock()->getZ()), ItemBlock::get(289,0, rand(1,40)));
				}
				$num = rand(1,8000);
				if ($num === 999){
					$player = $ev->getPlayer();
					$x = $ev->getBlock()->getX();
					$y = $ev->getBlock()->getY();
					$z = $ev->getBlock()->getZ();
					$level = $ev->getBlock()->getLevel();
					$level->dropItem(new Vector3($x, $y, $z), Item::get(341));
					$this->getServer()->broadcastMessage(TextFormat::GOLD."[LootBox]".TextFormat::RED.$player->getName()." found a LootBox Key while mining!");
				}
				if ($num === 200 or $num === 300 or $num === 400 or $num === 600 or $num === 800 or $num === 1000 or $num === 1100 or $num === 1500){
					$level->dropItem(new Vector3($ev->getBlock()->getX(), $ev->getBlock()->getY(),$ev->getBlock()->getZ()), ItemBlock::get(39,0, rand(0,0)));
				}
				break;
			case "tall grass":
				$num = rand(0,0);
				if ($num === 1){
					$level->dropItem(new Vector3($ev->getBlock()->getX(), $ev->getBlock()->getY(),$ev->getBlock()->getZ()), ItemBlock::get(295,0, rand(1,3)));
				}
				break;
		}
	}
	
	public function giveItems(Player $player, $x, $y, $z){
		$nitems = rand(2, $this->config["maxitems"][0]);
		//$rn = rand(0,count($this->items));
		$n = 0;
		$player->sendMessage(TextFormat::GOLD."[LootBox]".TextFormat::RED." Your crate items are now in your inventory!");
		//$i = new Item(341,0,1);
		$i = Item::get(341, 0, 1);
		$player->getInventory()->removeItem($i);
		while ($nitems > $n){
			$amount = rand(2, $this->config["num"][0]);
			$num = rand(0,count($this->items)-1);
			$i = $this->items[$num];
			$item = new Item($i[0],$i[1],$amount);
			$player->getInventory()->addItem($item);
			$n++;
	}
		if ($nitems === $this->config["maxitems"][0]){
			$this->getServer()->broadcastMessage(TextFormat::GOLD."[LootBox] ".TextFormat::RED.$player->getName()." GOT A MEGA CRATE!");
		}
	}
	
	
}
