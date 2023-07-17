<?php

declare(strict_types=1);

namespace economy\librairies\simplepackethandler\monitor;

use Closure;
use pocketmine\plugin\Plugin;
use ReflectionException;

final class PacketMonitor implements IPacketMonitor{

    /**
     * @var PacketMonitorListener
     */
	private PacketMonitorListener $listener;

    /**
     * @param Plugin $register
     * @param bool $handle_cancelled
     */
	public function __construct(Plugin $register, bool $handle_cancelled) {
		$this->listener = new PacketMonitorListener($register, $handle_cancelled);
	}

    /**
     * @param Closure $handler
     * @return IPacketMonitor
     * @throws ReflectionException
     */
	public function monitorIncoming(Closure $handler): IPacketMonitor {
		$this->listener->monitorIncoming($handler);
		return $this;
	}

    /**
     * @param Closure $handler
     * @return IPacketMonitor
     * @throws ReflectionException
     */
	public function monitorOutgoing(Closure $handler): IPacketMonitor {
		$this->listener->monitorOutgoing($handler);
		return $this;
	}

    /**
     * @param Closure $handler
     * @return IPacketMonitor
     * @throws ReflectionException
     */
	public function unregisterIncomingMonitor(Closure $handler): IPacketMonitor {
		$this->listener->unregisterIncomingMonitor($handler);
		return $this;
	}

    /**
     * @param Closure $handler
     * @return IPacketMonitor
     * @throws ReflectionException
     */
	public function unregisterOutgoingMonitor(Closure $handler): IPacketMonitor {
		$this->listener->unregisterOutgoingMonitor($handler);
		return $this;
	}

}
