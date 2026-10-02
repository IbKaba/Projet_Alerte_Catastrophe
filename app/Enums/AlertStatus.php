<?php

namespace App\Enums;

enum AlertStatus: string
{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case REJECTED = 'rejected';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::VALIDATED => 'Validée',
            self::REJECTED => 'Rejetée',
            self::RESOLVED => 'Résolue',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VALIDATED => 'success',
            self::REJECTED => 'danger',
            self::RESOLVED => 'info',
        };
    }

    /**
     * Transitions autorisées par le workflow métier.
     * Une alerte rejetée doit être corrigée par son auteur avant de repasser en attente.
     * Une alerte résolue est terminale.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::VALIDATED, self::REJECTED],
            self::VALIDATED => [self::RESOLVED],
            self::REJECTED, self::RESOLVED => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return $this === self::RESOLVED;
    }
}
