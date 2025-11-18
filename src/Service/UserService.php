<?php
// src/Service/MessageGenerator.php
namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class UserService
{
    public function getGold(): string
    {
        $gold = 500;
      

        return "You have " . $gold . " gold coins.";
    }

    public function updateGold(int $amount): string
    {
        return "Your gold has been updated by " . $amount . " coins.";
    }
}