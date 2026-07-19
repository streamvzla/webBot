<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Setting;

/**
 * QueryLimiter - Control de rate limiting para consultas de clientes
 *
 * Implementa límites configurables para evitar abuso del sistema.
 */
class QueryLimiter
{
    protected Client $client;
    protected ?Setting $settings;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->settings = Setting::first();
    }

    /**
     * Verificar si el cliente puede realizar una consulta
     */
    public function canMakeQuery(): bool
    {
        // Verificar si el cliente está activo
        if (!$this->client->is_active) {
            return false;
        }

        // Verificar límite diario
        if ($this->hasReachedDailyLimit()) {
            return false;
        }

        // Verificar tiempo entre consultas
        if (!$this->hasEnoughTimePassed()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si ha pasado el tiempo mínimo entre consultas
     */
    public function hasEnoughTimePassed(): bool
    {
        $minutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);

        if (!$this->client->last_query_at) {
            return true;
        }

        return $this->client->last_query_at->addMinutes($minutes)->isPast();
    }

    /**
     * Verificar si el cliente ha alcanzado el límite diario
     */
    public function hasReachedDailyLimit(): bool
    {
        $maxQueries = $this->client->max_queries_per_day ?? 100;
        return $this->client->query_count >= $maxQueries;
    }

    /**
     * Obtener segundos restantes hasta poder realizar otra consulta
     */
    public function getSecondsUntilNextQuery(): int
    {
        if (!$this->client->last_query_at) {
            return 0;
        }

        $minutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);
        $nextAllowedTime = $this->client->last_query_at->addMinutes($minutes);
        $now = now();

        if ($now >= $nextAllowedTime) {
            return 0;
        }

        return $nextAllowedTime->diffInSeconds($now);
    }

    /**
     * Obtener minutos restantes formateados
     */
    public function getFormattedTimeUntilNextQuery(): string
    {
        $seconds = $this->getSecondsUntilNextQuery();

        if ($seconds <= 0) {
            return 'Ahora';
        }

        if ($seconds < 60) {
            return "{$seconds} segundos";
        }

        $minutes = ceil($seconds / 60);
        return "{$minutes} minutos";
    }

    /**
     * Registrar una consulta exitosa
     */
    public function recordQuery(): void
    {
        $this->client->recordQuery();
    }

    /**
     * Reiniciar contador diario
     */
    public function resetDailyCount(): void
    {
        $this->client->resetDailyQueryCount();
    }

    /**
     * Obtener consultas restantes para hoy
     */
    public function getRemainingQueriesToday(): int
    {
        $maxQueries = $this->client->max_queries_per_day ?? 100;
        return max(0, $maxQueries - $this->client->query_count);
    }

    /**
     * Obtener información del estado del límite
     */
    public function getLimitStatus(): array
    {
        return [
            'can_make_query' => $this->canMakeQuery(),
            'seconds_until_next' => $this->getSecondsUntilNextQuery(),
            'formatted_time' => $this->getFormattedTimeUntilNextQuery(),
            'remaining_today' => $this->getRemainingQueriesToday(),
            'max_daily' => $this->client->max_queries_per_day ?? 100,
            'used_today' => $this->client->query_count,
        ];
    }

    /**
     * Validar que el cliente puede proceder con la consulta
     *
     * @throws \Exception Si no puede realizar la consulta
     */
    public function validateOrFail(): void
    {
        if (!$this->canMakeQuery()) {
            $status = $this->getLimitStatus();

            if (!$status['can_make_query']) {
                if ($status['seconds_until_next'] > 0) {
                    throw new \Exception(
                        "Debes esperar {$status['formatted_time']} antes de realizar otra consulta."
                    );
                }

                if ($status['remaining_today'] <= 0) {
                    throw new \Exception(
                        "Has alcanzado el límite diario de {$status['max_daily']} consultas."
                    );
                }

                throw new \Exception('Tu cuenta está inactiva. Contacta al administrador.');
            }
        }
    }
}
