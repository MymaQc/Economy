<?php

declare(strict_types=1);

namespace economy\librairies\simplepackethandler\monitor;

use Closure;
use economy\librairies\simplepackethandler\utils\Utils;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use ReflectionException;

final class PacketMonitorListener implements IPacketMonitor, Listener{

    /**
     * @template TPacket of \pocketmine\network\mcpe\protocol\Packet
     * @template UPacket of TPacket
     * @param Closure $handler
     * @param string $class
     * @return int
     */
	private static function getPidFromHandler(Closure $handler, string $class) : int{
		$classes = Utils::parseClosureSignature($handler, [$class, NetworkSession::class], "void");
		assert(is_a($classes[0], DataPacket::class, true));
		return $classes[0]::NETWORK_ID;
	}

	/** @var (Closure(DataPacketReceiveEvent) : void)|null */
	private ?Closure $incoming_event_handler = null;

	/** @var (Closure(DataPacketSendEvent) : void)|null */
	private ?Closure $outgoing_event_handler = null;

	/** @var array */
	private array $incoming_handlers = [];

	/** @var array */
	private array $outgoing_handlers = [];

	public function __construct(
		private Plugin $register,
		private bool $handleCancelled
	){}

	public function monitorIncoming(Closure $handler) : IPacketMonitor{
		$this->incoming_handlers[self::getPidFromHandler($handler, ServerboundPacket::class)][spl_object_id($handler)] = $handler;

		if($this->incoming_event_handler === null){
            try {
                Server::getInstance()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, $this->incoming_event_handler = function (DataPacketReceiveEvent $event) : void {
                    /** @var DataPacket&ServerboundPacket $packet */
                    $packet = $event->getPacket();
                    if (isset($this->incoming_handlers[$pid = $packet::NETWORK_ID])) {
                        $origin = $event->getOrigin();
                        foreach ($this->incoming_handlers[$pid] as $handler) {
                            $handler($packet, $origin);
                        }
                    }
                }, EventPriority::MONITOR, $this->register, $this->handleCancelled);
            } catch (ReflectionException) {}
        }

		return $this;
	}

	public function monitorOutgoing(Closure $handler) : IPacketMonitor{
		$this->outgoing_handlers[self::getPidFromHandler($handler, ClientboundPacket::class)][spl_object_id($handler)] = $handler;

		if($this->outgoing_event_handler === null){
            try {
                Server::getInstance()->getPluginManager()->registerEvent(DataPacketSendEvent::class, $this->outgoing_event_handler = function (DataPacketSendEvent $event) : void {
                    /** @var DataPacket&ClientboundPacket $packet */
                    foreach ($event->getPackets() as $packet) {
                        if (isset($this->outgoing_handlers[$pid = $packet::NETWORK_ID])) {
                            foreach ($event->getTargets() as $target) {
                                foreach ($this->outgoing_handlers[$pid] as $handler) {
                                    $handler($packet, $target);
                                }
                            }
                        }
                    }
                }, EventPriority::MONITOR, $this->register, $this->handleCancelled);
            } catch (ReflectionException) {}
        }

		return $this;
	}

	public function unregisterIncomingMonitor(Closure $handler) : IPacketMonitor{
		if(isset($this->incoming_handlers[$pid = self::getPidFromHandler($handler, ServerboundPacket::class)][$hid = spl_object_id($handler)])){
			unset($this->incoming_handlers[$pid][$hid]);
			if(count($this->incoming_handlers[$pid]) === 0){
				unset($this->incoming_handlers[$pid]);
				if(count($this->incoming_handlers) === 0){
					Utils::unregisterEventByHandler(DataPacketReceiveEvent::class, $this->incoming_event_handler, EventPriority::MONITOR);
					$this->incoming_event_handler = null;
				}
			}
		}

		return $this;
	}

	public function unregisterOutgoingMonitor(Closure $handler) : IPacketMonitor{
		if(isset($this->outgoing_handlers[$pid = self::getPidFromHandler($handler, ClientboundPacket::class)][$hid = spl_object_id($handler)])){
			unset($this->outgoing_handlers[$pid][$hid]);
			if(count($this->outgoing_handlers[$pid]) === 0){
				unset($this->outgoing_handlers[$pid]);
				if(count($this->outgoing_handlers) === 0){
					Utils::unregisterEventByHandler(DataPacketSendEvent::class, $this->outgoing_event_handler, EventPriority::MONITOR);
					$this->outgoing_event_handler = null;
				}
			}
		}

		return $this;
	}
}
