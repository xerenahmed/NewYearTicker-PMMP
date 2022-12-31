<?php

declare(strict_types=1);

/**
 * @name NewYearTicker
 * @main xerenahmed\NewYearTicker\Main
 * @version 1.0.0
 * @api 4.0.0
 * @description This PMMP plugin will show a new year ticker on the screen last 10 seconds of the year.
 * @author xerenahmed
 */
namespace xerenahmed\NewYearTicker{

	use pocketmine\plugin\PluginBase;
	use pocketmine\scheduler\Task;
	use pocketmine\Server;
	use pocketmine\world\sound\ClickSound;
	use pocketmine\world\sound\ExplodeSound;

	use function date;
	use function dechex;
	use function mt_rand;
	use function strtotime;
	use function time;

	class Main extends PluginBase{
		public function onEnable(): void{
			$this->getScheduler()->scheduleRepeatingTask(new MainTask($this), 20);
		}
	}

	class MainTask extends Task {
		public int $startTime;
		public function __construct(public Main $plugin){
			$this->startTime = strtotime("23:59:49");
			$plugin->getLogger()->info("Will start at " . date("H:i:s", $this->startTime) . ' now is ' . date("H:i:s", time()));
		}

		public function onRun(): void{
			if(time() < $this->startTime) {
				return;
			}

			$this->getHandler()->cancel();
			$this->plugin->getScheduler()->scheduleRepeatingTask(new NewYearTickerTask(), 20);
		}
	}

	class NewYearTickerTask extends Task {
		public int $time = 10;

		public function onRun(): void{
			if($this->time <= 0) {
				$this->getHandler()->cancel();

				foreach(Server::getInstance()->getOnlinePlayers() as $player) {
					$player->sendTitle("§b2023!");

					$player->getWorld()->addSound($player->getPosition(), new ExplodeSound());
				}
				return;
			}

			foreach(Server::getInstance()->getOnlinePlayers() as $player) {
				$randomColor = "§" . dechex(mt_rand(0, 15));
				if ($randomColor === "§0" || $randomColor === "§8") {
					$randomColor = "§f";
				}
				$player->sendTitle($randomColor . $this->time, '', 4, 12, 4);
				$player->getWorld()->addSound($player->getPosition(), new ClickSound($this->time * 10));
			}

			$this->time--;
		}
	}
}
