<?php

interface Item {
  public function use($human);
  public function isItemUsed();
  public function getItemType();
}

class Rifle implements Item {
  private $accurcy;
  private $damage;
  private $hitsPerRound;
  private $ammo;
  public function use($human) {
    $damage = 0;
    for($i=0;$i<$this->hitsPerRound;$i++){
      $this->ammo--;
      if ($this->ammo!==0){
        if ((($human->getAccurcy()+$this->accurcy) * rand(3, 10)) >= 550) {
          $damage=$damage+$this->damage;
        }
      }
    }
    return $damage;
  }
  public function isItemUsed(){
    return $this->ammo===0;
  }
  public function getItemType(){
    return get_class($this);
  }
}

class Sniper implements Item {
  private $accurcy;
  private $damage;
  private $hitsPerRound;
  private $ammo;
  public function __construct(){
    $this->accurcy=50;
    $this->damage=110;
    $this->hitsPerRound=1;
    $this->sniper=20;
  }
  public function use($human) {
    $damage = 0;
    for($i=0;$i<$this->hitsPerRound;$i++){
      $this->ammo--;
      if ($this->ammo!==0){
        if ((($human->getAccurcy()+$this->accurcy) * rand(3, 10)) >= 550) {
          $damage=$damage+$this->damage;
        }
      }
    }
    return $damage;
  }
  public function isItemUsed(){
    return $this->ammo===0;
  }

  public function getItemType(){
    return get_class($this);
  }
}

class Pistol implements Item {
  private $accurcy;
  private $damage;
  private $hitsPerRound;
  private $ammo;
  public function __construct() {
    $this->accurcy=20;
    $this->damage=60;
    $this->hitsPerRound=4;
    $this->ammo=32;
  }
  public function use($human) {
    $damage = 0;
    for($i=0;$i<$this->hitsPerRound;$i++){
      $this->ammo--;
      if ($this->ammo!==0){
        if ((($human->getAccurcy()+$this->accurcy) * rand(3, 10)) >= 550) {
          $damage=$damage+$this->damage;
        }
      }
    }
    return $damage;
  }
  public function isItemUsed(){
    return $this->ammo===0;
  }

  public function getItemType(){
    return get_class($this);
  }
}

class MachineGun implements Item {
  private $accurcy;
  private $damage;
  private $hitsPerRound;
  private $ammo;
  public function __construct() {
    $this->accurcy=-25;
    $this->damage=20;
    $this->hitsPerRound=20;
    $this->ammo=300;
  }
  public function use($human) {
    $damage = 0;
    for($i=0;$i<$this->hitsPerRound;$i++){
      $this->ammo--;
      if ($this->ammo!==0){
        if ((($human->getAccurcy()+$this->accurcy) * rand(3, 10)) >= 550) {
          $damage=$damage+$this->damage;
        }
      }
    }
    return $damage;
  }
  public function isItemUsed(){
    return $this->ammo===0;
  }

  public function getItemType(){
    return get_class($this);
  }
}

class HealthPack implements Item {
  private $size;
  private $used = false;
  public function use($human){
    $this->used=true;
    return $this->size;
  }
  public function isItemUsed(){
    return $this->used;
  }

  public function getItemSize(){
    $size;
  }

  public function getItemType(){
    return get_class($this);
  }
}

class Human {
  private $health;
  private $accurcy;
  protected $item;
  private $dead;
  public function __construct($health=100, $accurcy=50, $item = null) {
    $this->dead = false;
    $this->health = $health;
    $this->accurcy = $accurcy;
    $this->item = $item;
  }
  public function action(&$battleLog) {
    $battleLog .= get_class($this).' uses '.$this->item->getItemType().'<br>';
    return ($this->item !==null) ? (($this->item->isItemUsed()) ? $this->item->use($this) : $this->dead=true ): 0;
  }
  public function getHealth() {
    return $this->health;
  }

  public function getAccurcy() {
    return $this->accurcy;
  }

  public function isUsed(){
    return ($this->item!== null) ? $this->item->isItemUsed() : false;
  }

  public function reduceHealth($damage) {
    $this->health=$this->health-$damage;
    if($this->health<=0)
    {
      $this->dead = true;
    }
    return $this->dead;
  }
  public function healMe($health) {
    $this->health=$this->health-$health;
  }
}


class Soldier extends Human {
  private $rank;
  public function __construct($rank){
    $this->rank = $rank;
    switch ($this->rank) {
      case 'Sniper':
        parent::__construct(100, 130, new Sniper());
      break;
      case 'LT':
        parent::__construct(160, 160, new Rifle());
      break;
      case 'Captain':
        parent::__construct(160, 160, new Pistol());
      break;
      case 'Med':
        parent::__construct(150, 100, new HealthPack());
      break;
      default:
        parent::__construct(100, 80, new MachineGun());
    }
  }
  public function action(&$battleLog) {
    $damage =($this->item !==null) ? ((!$this->item->isItemUsed()) ? $this->item->use($this) : $this->dead=true ): 0;
    ($this->getRank()!=='Med') ? $battleLog .= $this->getRank().' uses '.$this->item->getItemType().' and inflicts '.$damage.'<br>' : $battleLog .= $this->getRank().' uses '.$this->item->getItemType().' and heals his commrade by '.$this->item->getItemSize().'<br>';
    return $damage;
  }

  public function getRank() {
    return $this->rank;
  }
}

class Army {
  public $soldiers;
  private $lost;
  private $soldiersCount;
  private $roundLogs;

  public function __construct($soldiersCount) {
    $this->lost = false;
    $this->soldiers = array();
    $this->soldiersCount = $soldiersCount;
    $this->roundLogs;
  }

  private function setSoldiers(){
    for($i=0;$i<$this->soldiersCount;$i++) {
      if($i%30==0){
        $this->soldiers[] = new Soldier('Captain');
      }
      else if($i%12==0){
        $this->soldiers[] = new Soldier('Med');
      }
      else if($i%8==0){
        $this->soldiers[] = new Soldier('LT');
      }
      else if($i%4==0){
        $this->soldiers[] = new Soldier('Sniper');
      }
      else {
        $this->soldiers[] = new Soldier('Private');
      }
    }
  }

  public function getStatus() {
    return $this->lost;
  }

  public function getSoldiersCount() {
    return count($this->soldiers);
  }

  public function reduceSoldier($index) {
    unset($this->soldiers[$index]);
    $this->soldiers=array_merge($this->soldiers);
    if(count($this->soldiers)<=0) {
      $this->lost=true;
    }
  }

  public function battle(&$army) {
    $this->setSoldiers();
    $army->setSoldiers();
    $battleLog = 'The battle has started!!!<br>';
    $round = 1;
    while (!$this->getStatus() && !$army->getStatus()){
      $roundLog = '';
      $battleLog .= 'Round '.$round.':<br>';
      $solidersLostInRoundA = $army->getSoldiersCount();
      $solidersLostInRoundB = $this->getSoldiersCount();

      $battleLog .='Team 1 has soldiers '.$solidersLostInRoundB.' in round '.$round.'<br>';
      $battleLog .='Team 2 has soldiers '.$solidersLostInRoundA.' in round '.$round.'<br>';

      $higherSoldiersCount = ($this->getSoldiersCount()>=$army->getSoldiersCount()) ? $this->getSoldiersCount() : $army->getSoldiersCount();

      for($i=0;$i<$higherSoldiersCount;$i++){

        $soldierB=rand(0,$this->getSoldiersCount()-1);
        $soldierA=rand(0,$army->getSoldiersCount()-1);

        if($army->getSoldiersCount()!=0 && $this->getSoldiersCount()!=0)
        {
          $roundLog .= 'Team 1: ';
          if($this->soldiers[$soldierB]->getRank()==='Med')
          {
            $soldierC = rand(0,$this->getSoldiersCount()-1);
            $this->soldiers[$soldierC]->healMe($this->soldiers[$soldierB]->action($roundLog));

            ($this->soldiers[$soldierB]->isUsed()) ? $this->reduceSoldier($soldierB) : false;
            $soldierB=rand(0,$this->getSoldiersCount()-1);
          }
          else
          {
            if($this->getSoldiersCount()!=0 && $army->getSoldiersCount()!=0)
            {
              if($army->soldiers[$soldierA]->reduceHealth($this->soldiers[$soldierB]->action($roundLog))) {
                $army->reduceSoldier($soldierB);
                $soldierA=rand(0,$army->getSoldiersCount()-1);
              }
              else{
                ($this->soldiers[$soldierB]->isUsed()) ? $this->reduceSoldier($soldierB) : false;
                $soldierB=rand(0,$this->getSoldiersCount()-1);
              }
            }
          }
        }

        $soldierB=rand(0,$this->getSoldiersCount()-1);
        $soldierA=rand(0,$army->getSoldiersCount()-1);

        if($army->getSoldiersCount()!=0 && $this->getSoldiersCount()!=0) {
          $roundLog .= 'Team 2: ';
          if($army->soldiers[$soldierA]->getRank()==='Med')
          {
            $soldierC = rand(0,$army->getSoldiersCount()-1);
            $army->soldiers[$soldierC]->healMe($army->soldiers[$soldierA]->action($roundLog));

            ($army->soldiers[$soldierA]->isUsed()) ? $army->reduceSoldier($soldierA) : false;
            $soldierB=rand(0,$army->getSoldiersCount()-1);
          }
          else{
            if($this->getSoldiersCount()!=0 && $army->getSoldiersCount()!=0)
            {
              if($this->soldiers[$soldierB]->reduceHealth($army->soldiers[$soldierA]->action($roundLog))) {
                $this->reduceSoldier($soldierB);
                $soldierB=rand(0,$this->getSoldiersCount()-1);
              }
              else{
                ($army->soldiers[$soldierA]->isUsed()) ? $army->reduceSoldier($soldierA) : false;
                $soldierA=rand(0,$army->getSoldiersCount()-1);
              }
            }
          }
        }
        $higherSoldiersCount = ($this->getSoldiersCount()>=$army->getSoldiersCount()) ? $this->getSoldiersCount() : $army->getSoldiersCount();
      }
      $solidersLostInRoundA = $solidersLostInRoundA-$army->getSoldiersCount();
      $solidersLostInRoundB = $solidersLostInRoundB-$this->getSoldiersCount();

      $this->roundLogs[] = $roundLog;
      $battleLog .='Team 1 lost '.$solidersLostInRoundB.' in round '.$round.'<br>';
      $battleLog .='Team 2 lost '.$solidersLostInRoundA.' in round '.$round.'<br>';
      $round++;
    }

    ($this->getSoldiersCount() >= $army->getSoldiersCount()) ? $battleLog.='Team 1 Won<br>': $battleLog.='Team 2 Won<br>';

    return $battleLog;
  }

  public function getRoundLog($round=null) {
    if($round == null)
    {
      $round = count($this->roundLogs);
    }
    return $this->roundLogs[$round-1];
  }
}
