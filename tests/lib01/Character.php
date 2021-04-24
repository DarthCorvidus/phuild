<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Character
 *
 * @author hm
 */
interface Character {
	function getCount(): int;
	function getCharacter(int $i): string;
}
