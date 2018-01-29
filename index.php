<?php
include 'klase.php';

$army1=new Army((int)$_GET["army1"]);
$army2=new Army((int)$_GET["army2"]);

echo $army1->battle($army2);

echo $army1->getRoundLog(1);
