<?php

namespace FuelioImporter;

interface ICard {
    public function getClass();
    public function getTitle();
    public function getSupporting();
    public function getActions();
    public function getMenu();
}
