<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shipment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Terminal49Service
{
    private string $apiKey;

    private string $baseUrl;

    private int $cacheTtl;

    public function __construct()
    {
        $this->apiKey = config('terminal49.api_key');
        $this->baseUrl = config('terminal49.base_url');
        $this->cacheTtl = config('terminal49.cache_ttl', 3600);
    }

    /**
     * Sync shipment tracking data from Terminal49 API
     *
     * @return array<string, mixed>
     */
    public function syncShipment(Shipment $shipment): array
    {
        try {
            // Check if cache is fresh (< cache TTL seconds old)
            if ($shipment->last_synced_at &&
                $shipment->last_synced_at->gt(now()->subSeconds($this->cacheTtl))) {
                return [
                    'success' => true,
                    'data' => $shipment->cached_status,
                    'cached' => true,
                    'synced_at' => $shipment->last_synced_at,
                ];
            }

            // Get tracking number from order
            $trackingNumber = $shipment->order->tracking_number;

            if (! $trackingNumber) {
                return [
                    'success' => false,
                    'error' => 'No tracking number found',
                ];
            }

            // Call Terminal49 API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/v2/shipments", [
                'q' => $trackingNumber,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Update shipment with cached data
                $shipment->update([
                    'cached_status' => $data,
                    'last_synced_at' => now(),
                ]);

                Log::info('Terminal49 sync successful', [
                    'shipment_id' => $shipment->id,
                    'tracking_number' => $trackingNumber,
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'cached' => false,
                    'synced_at' => now(),
                ];
            }

            $errorData = $response->json();

            return [
                'success' => false,
                'error' => $errorData['error'] ?? 'API request failed',
            ];
        } catch (Exception $e) {
            Log::error('Terminal49 sync failed', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Sync failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Format tracking data into a timeline for display
     *
     * @param  array<string, mixed>|null  $cachedStatus
     */
    public function getTrackingTimeline(?array $cachedStatus): string
    {
        if (! $cachedStatus) {
            return '<em>No tracking data available</em>';
        }

        // Extract timeline events from the API response
        $events = $cachedStatus['events'] ?? [];

        if (empty($events)) {
            return '<em>No tracking events found</em>';
        }

        $html = '<div style="border-left: 2px solid #3b82f6; padding-left: 12px;">';

        foreach ($events as $event) {
            $timestamp = $event['timestamp'] ?? null;
            $status = $event['status'] ?? 'Unknown';
            $location = $event['location'] ?? 'Unknown Location';
            $description = $event['description'] ?? '';

            $html .= '<div style="margin-bottom: 16px;">';
            $html .= '<div style="font-weight: 600; color: #111827;">'.htmlspecialchars($status).'</div>';
            if ($timestamp) {
                $html .= '<div style="font-size: 0.875rem; color: #6b7280;">'.htmlspecialchars($timestamp).'</div>';
            }
            $html .= '<div style="font-size: 0.875rem; color: #6b7280;">'.htmlspecialchars($location).'</div>';
            if ($description) {
                $html .= '<div style="font-size: 0.875rem; color: #4b5563; margin-top: 4px;">'.htmlspecialchars($description).'</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Check if Terminal49 API is properly configured
     */
    public function isApiConfigured(): bool
    {
        return ! empty($this->apiKey);
    }
}
