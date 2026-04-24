<?php
/*
 *  ██╗  ██╗███████╗ █████╗ ██████╗ ████████╗
 *  ██║  ██║██╔════╝██╔══██╗██╔══██╗╚══██╔══╝
 *  ███████║█████╗  ███████║██████╔╝   ██║   
 *  ██╔══██║██╔══╝  ██╔══██║██╔══██╗   ██║   
 *  ██║  ██║███████╗██║  ██║██║  ██║   ██║   
 *  ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝  
 *
 *  ██████╗ ███████╗██╗   ██╗███████╗██╗      ██████╗ ██████╗ ███████╗██████╗ 
 *  ██╔══██╗██╔════╝██║   ██║██╔════╝██║     ██╔═══██╗██╔══██╗██╔════╝██╔══██╗
 *  ██║  ██║█████╗  ██║   ██║█████╗  ██║     ██║   ██║██████╔╝█████╗  ██████╔╝
 *  ██║  ██║██╔══╝  ╚██╗ ██╔╝██╔══╝  ██║     ██║   ██║██╔═══╝ ██╔══╝  ██╔══██╗
 *  ██████╔╝███████╗ ╚████╔╝ ███████╗███████╗╚██████╔╝██║     ███████╗██║  ██║
 *  ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝╚══════╝ ╚═════╝ ╚═╝     ╚══════╝╚═╝  ╚═╝
 *
namespace heartdeveloper\BanSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private $bans;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->bans = new Config($this->getDataFolder() . "bans.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {

        switch ($command->getName()) {

            case "banir":
                if (!$sender->hasPermission("bansystem.ban")) {
                    $sender->sendMessage("§cVocê não tem permissão.");
                    return true;
                }
                if (count($args) < 2) {
                    $sender->sendMessage("§cUso: /banir <jogador> <razao>");
                    return true;
                }
                $playerName = array_shift($args);
                $reason = implode(" ", $args);
                $ip = "";
                $target = $this->getServer()->getPlayerExact($playerName);
                if ($target instanceof Player) {
                    $ip = $target->getAddress();
                    $target->kick("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~\n\n§c Você está banido!\n\n§7Motivo: §f" . $reason, false);
                }
                $this->bans->set(strtolower($playerName), [
                    "name" => $playerName,
                    "reason" => $reason,
                    "expire" => -1,
                    "bannedBy" => $sender->getName(),
                    "ip" => $ip,
                    "time" => time()
                ]);
                $this->bans->save();
                $sender->sendMessage("§aJogador §e" . $playerName . " §abanido permanentemente.");
                return true;

            case "banirtempo":
                if (!$sender->hasPermission("bansystem.bantemp")) {
                    $sender->sendMessage("§cVocê não tem permissão.");
                    return true;
                }
                if (count($args) < 3) {
                    $sender->sendMessage("§cUso: /banirtempo <jogador> <tempo(s/m/h/d)> <razao>");
                    return true;
                }
                $playerName = array_shift($args);
                $timeArg = array_shift($args);
                $reason = implode(" ", $args);
                $seconds = $this->parseTime($timeArg);
                if ($seconds <= 0) {
                    $sender->sendMessage("§cFormato inválido. Use: 10s, 5m, 2h, 1d");
                    return true;
                }
                $expireAt = time() + $seconds;
                $ip = "";
                $target = $this->getServer()->getPlayerExact($playerName);
                if ($target instanceof Player) {
                    $ip = $target->getAddress();
                    $target->kick("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~\n\n§c Você está banido!\n\n§7Motivo: §f" . $reason . "\n\n§7Volte aqui em: §e" . $this->formatTime($seconds), false);
                }
                $this->bans->set(strtolower($playerName), [
                    "name" => $playerName,
                    "reason" => $reason,
                    "expire" => $expireAt,
                    "bannedBy" => $sender->getName(),
                    "ip" => $ip,
                    "time" => time()
                ]);
                $this->bans->save();
                $sender->sendMessage("§aJogador §e" . $playerName . " §abanido por §e" . $this->formatTime($seconds) . "§a.");
                return true;

            case "removerbanimento":
                if (!$sender->hasPermission("bansystem.unban")) {
                    $sender->sendMessage("§cVocê não tem permissão.");
                    return true;
                }
                if (count($args) < 1) {
                    $sender->sendMessage("§cUso: /removerbanimento <jogador>");
                    return true;
                }
                $playerName = $args[0];
                if (!$this->bans->exists(strtolower($playerName))) {
                    $sender->sendMessage("§cEste jogador não está banido.");
                    return true;
                }
                $this->bans->remove(strtolower($playerName));
                $this->bans->save();
                $sender->sendMessage("§aBanimento de §e" . $playerName . " §aremovido.");
                return true;

            case "banhelp":
                $sender->sendMessage("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~");
                $sender->sendMessage("");
                $sender->sendMessage("§7 use: (§e/banir§7) (§bPara banir o jogador permanentemente§7)");
                $sender->sendMessage("§7 use: (§e/banirtempo§7) (§bPara banir por timer§7)");
                $sender->sendMessage("§7 use: (§e/removerbanimento§7) (§bPara remover o Banimento.§7)");
                $sender->sendMessage("");
                $sender->sendMessage("§o§7By : heartdeveloper");
                $sender->sendMessage("§o§7Discord: heartdeveloper");
                $sender->sendMessage("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~");
                return true;
        }

        return false;
    }

    public function onPreLogin(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());
        $ip = $player->getAddress();

        $banData = null;

        if ($this->bans->exists($name)) {
            $banData = $this->bans->get($name);
        } else {
            foreach ($this->bans->getAll() as $key => $data) {
                if (isset($data["ip"]) && $data["ip"] !== "" && $data["ip"] === $ip) {
                    $banData = $data;
                    break;
                }
            }
        }

        if ($banData === null) return;

        $expireAt = $banData["expire"];
        $reason = $banData["reason"];

        if ($expireAt !== -1 && time() >= $expireAt) {
            $this->bans->remove($name);
            $this->bans->save();
            return;
        }

        if ($expireAt === -1) {
            $event->setKickMessage("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~\n\n§c Você está banido!\n\n§7Motivo: §f" . $reason);
        } else {
            $remaining = $expireAt - time();
            $event->setKickMessage("§o§7~~~~§f(§l§eBANSYSTEM§r§f)§7~~~~\n\n§c Você está banido!\n\n§7Motivo: §f" . $reason . "\n\n§7Volte aqui em: §e" . $this->formatTime($remaining));
        }

        $event->setCancelled(true);
    }

    private function parseTime($input) {
        preg_match('/^(\d+)([smhd])$/', strtolower($input), $matches);
        if (empty($matches)) return 0;
        $value = (int)$matches[1];
        switch ($matches[2]) {
            case "s": return $value;
            case "m": return $value * 60;
            case "h": return $value * 3600;
            case "d": return $value * 86400;
        }
        return 0;
    }

    private function formatTime($seconds) {
        if ($seconds >= 86400) {
            $d = intdiv($seconds, 86400);
            return $d . " dia" . ($d > 1 ? "s" : "");
        } elseif ($seconds >= 3600) {
            $h = intdiv($seconds, 3600);
            return $h . " hora" . ($h > 1 ? "s" : "");
        } elseif ($seconds >= 60) {
            $m = intdiv($seconds, 60);
            return $m . " minuto" . ($m > 1 ? "s" : "");
        }
        return $seconds . " segundo" . ($seconds > 1 ? "s" : "");
    }
}
