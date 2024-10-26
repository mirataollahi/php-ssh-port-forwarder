<?php
/**
 * User: Mirataollahi ( @Mirataollahi124 )
 * Date: 10/26/24  Time: 1:49 PM
 */

namespace App\Tools;

class Reporter
{
    public $items = [];
    public function __construct(array $items = [])
    {
        $this->items = [];
    }

    public function add(string|int $reportId, array $data = []): void
    {
        $this->items [$reportId] = $data;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function update(string|int $reportId,array $data): void
    {
        if (array_key_exists($reportId,$this->items)){
            $currentData = $this->items[$reportId];
            $this->items[$reportId] = array_merge($currentData,$data);
        }
        else {
            $this->items[$reportId] = $data;
        }
    }

    public function find(string|int $reportId,mixed $reportName)
    {
        if (!array_key_exists($reportId,$this->items)){
            return null;
        }
        return $this->items[$reportId][$reportName] ?? null;
    }
}