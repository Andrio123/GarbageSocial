<?php defined('_JEXEC') or die('Restricted access');
echo '<div id="phocamapsprintroute">';
$id		= '';
$map	= new PhocaMapsMap();

echo $map->getIconPrintScreen();

$map->loadAPI();

echo $map->startJScData();
echo $map->addAjaxAPI('maps', '3.x', $this->tmpl['params']);
echo $map->createDirection(1);
echo $map->setDirectionFunction();
echo $map->directionInitializeFunction($this->tmpl['from'], $this->tmpl['to']);
echo $map->endJScData();

echo '<div id="directionsPanel'.$id.'" ></div>';
echo PhocaMapsHelper::getInfo();
echo '</div>';


