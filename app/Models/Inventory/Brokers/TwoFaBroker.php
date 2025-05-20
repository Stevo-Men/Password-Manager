<?php

namespace Models\Inventory\Brokers;


use stdClass;
use Zephyrus\Database\DatabaseBroker;

class TwoFaBroker extends DataBaseBroker
{
    public function insertCode(int $userId, string $code, \DateTimeImmutable $expires): void
    {
        $this->rawQuery(
            "INSERT INTO two_factor_codes (user_id, code, expires_at) VALUES (?, ?, ?)",
            [$userId, $code, $expires->format('Y-m-d H:i:s')]
        );
    }

    public function findValidCode(int $userId, string $code): ?stdClass
    {
        return $this->selectSingle(
            "SELECT * FROM two_factor_codes
             WHERE user_id = ? AND code = ? AND used_at IS NULL AND expires_at >= now()",
            [$userId, $code]
        );
    }

    public function markUsed(int $id): void
    {
        $this->selectSingle(
            "UPDATE two_factor_codes SET used_at = now() WHERE id = ?",
            [$id]
        );
    }
}
