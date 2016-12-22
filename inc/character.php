<?php
/* Author: Romain Dal Maso <artefact2@gmail.com>
 *
 * This program is free software. It comes without any warranty, to the
 * extent permitted by applicable law. You can redistribute it and/or
 * modify it under the terms of the Do What The Fuck You Want To Public
 * License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

namespace HeroesOfLegend;

class Character {
	const PC = 0;
	const NPC = 1;

	const CHILD = 0;
	const ADOLESCENT = 1;
	const ADULT = 2;

	private $name;
	private $type;
	private $ageRange;
	
	public $CuMod = 0;
	public $SolMod = 0;
	public $LegitMod = 0;
	public $BiMod = 0;
	public $TiMod = 0;

	public $traits = [
		'L' => 0,
		'D' => 0,
		'N' => 0,
		'R' => 0,
	];
	
	private $rootEntry;
	private $activeEntry;

	public function __construct($name = 'Unnamed character', $type = self::PC, $ageRange = self::CHILD) {
		$this->setName($name);
		$this->setType($type);
		$this->setAgeRange($ageRange);

		$this->rootEntry = new Entry("000", "(Root entry)");
		$this->activeEntry = $this->rootEntry;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function getType(): int {
		return $this->type;
	}

	public function setType(int $type): void {
		$this->type = $type;
	}

	public function getAgeRange(): int {
		return $this->ageRange;
	}

	public function setAgeRange(int $ageRange): void {
		$this->ageRange = $ageRange;
	}

	public function getRootEntry(): Entry {
		return $this->rootEntry;
	}

	public function getActiveEntry(): Entry {
		return $this->activeEntry;
	}

	public function setActiveEntry(Entry $e): void {
		$e2 = $e;		
		while($e2 !== $this->rootEntry) {
			$e2 = $e2->getParent();
			assert($e2 !== null);
		}
		
		$this->activeEntry = $e;
	}

	public function printPlaintextSummary(): void {
		$prefix = '|   ';
		$len = 120;

		$traverse = function(Entry $e, int $level) use($len, $prefix, &$traverse) {
			$prefix = str_repeat($prefix, $level);			
			$lines = $e->getLines();
			if($lines === []) $lines[] = '';
			$first = true;
			foreach($lines as $line) {
				if($first) {
					$first = false;
					$hlen = $len - 6;
					printf(
						"%-".$hlen.".".$hlen."s(%-5.5s)\n",
						$prefix
						.($e->getChildren() === [] ? '' : '* ')
						.$e->getSourceName().': '
						.$line, $e->getSourceID()
					);
				} else {
					printf("%-".$len.".".$len."s\n", $prefix.$line);
				}
			}

			foreach($e->getChildren() as $c) {
				$traverse($c, $level + 1);
			}
		};

		foreach($this->getRootEntry()->getChildren() as $e) {
			$traverse($e, 0);
		}
	}
}
