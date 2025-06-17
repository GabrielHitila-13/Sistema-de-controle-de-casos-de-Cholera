<?php

namespace App\Services;

use App\Models\Estabelecimento;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    private $googleMapsApiKey;

    public function __construct()
    {
        $this->googleMapsApiKey = config('services.google_maps.api_key');
    }

    public function buscarHospitaisProximos(float $latitude, float $longitude, int $limite = 5): array
    {
        // Buscar estabelecimentos no banco de dados
        $estabelecimentos = Estabelecimento::with('gabinete')
            ->whereIn('categoria', ['geral', 'municipal', 'centro'])
            ->get();

        $hospitaisComDistancia = [];

        foreach ($estabelecimentos as $estabelecimento) {
            // Se o estabelecimento não tem coordenadas, tentar geocodificar
            if (!$estabelecimento->latitude || !$estabelecimento->longitude) {
                $coordenadas = $this->geocodificarEndereco($estabelecimento->endereco);
                if ($coordenadas) {
                    $estabelecimento->update([
                        'latitude' => $coordenadas['lat'],
                        'longitude' => $coordenadas['lng']
                    ]);
                }
            }

            if ($estabelecimento->latitude && $estabelecimento->longitude) {
                $distancia = $this->calcularDistancia(
                    $latitude, 
                    $longitude, 
                    $estabelecimento->latitude, 
                    $estabelecimento->longitude
                );

                $hospitaisComDistancia[] = [
                    'id' => $estabelecimento->id,
                    'nome' => $estabelecimento->nome,
                    'endereco' => $estabelecimento->endereco,
                    'telefone' => $estabelecimento->telefone,
                    'categoria' => $estabelecimento->categoria,
                    'capacidade' => $estabelecimento->capacidade,
                    'gabinete' => $estabelecimento->gabinete->nome ?? null,
                    'latitude' => $estabelecimento->latitude,
                    'longitude' => $estabelecimento->longitude,
                    'distancia' => $distancia,
                    'tempo_estimado' => $this->calcularTempoViagem($distancia),
                ];
            }
        }

        // Ordenar por distância
        usort($hospitaisComDistancia, function($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });

        return array_slice($hospitaisComDistancia, 0, $limite);
    }

    public function geocodificarEndereco(string $endereco): ?array
    {
        if (empty($endereco) || !$this->googleMapsApiKey) {
            return null;
        }

        $cacheKey = 'geocode_' . md5($endereco);
        
        return Cache::remember($cacheKey, 86400, function() use ($endereco) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $endereco . ', Angola',
                    'key' => $this->googleMapsApiKey,
                ]);

                $data = $response->json();

                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    return [
                        'lat' => $location['lat'],
                        'lng' => $location['lng']
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Erro na geocodificação: ' . $e->getMessage());
            }

            return null;
        });
    }

    public function calcularDistancia(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Raio da Terra em km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    private function calcularTempoViagem(float $distanciaKm): string
    {
        // Estimativa baseada em velocidade média urbana (30 km/h)
        $tempoHoras = $distanciaKm / 30;
        $tempoMinutos = $tempoHoras * 60;

        if ($tempoMinutos < 60) {
            return round($tempoMinutos) . ' min';
        } else {
            $horas = floor($tempoMinutos / 60);
            $minutos = round($tempoMinutos % 60);
            return $horas . 'h ' . $minutos . 'min';
        }
    }

    public function obterRota(float $origemLat, float $origemLng, Estabelecimento $destino): ?array
    {
        if (!$this->googleMapsApiKey || !$destino->latitude || !$destino->longitude) {
            return null;
        }

        $cacheKey = 'rota_' . md5("{$origemLat},{$origemLng}_{$destino->latitude},{$destino->longitude}");
        
        return Cache::remember($cacheKey, 3600, function() use ($origemLat, $origemLng, $destino) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
                    'origin' => "{$origemLat},{$origemLng}",
                    'destination' => "{$destino->latitude},{$destino->longitude}",
                    'key' => $this->googleMapsApiKey,
                    'language' => 'pt-BR',
                ]);

                $data = $response->json();

                if ($data['status'] === 'OK' && !empty($data['routes'])) {
                    $route = $data['routes'][0];
                    $leg = $route['legs'][0];

                    return [
                        'distancia' => $leg['distance']['text'],
                        'duracao' => $leg['duration']['text'],
                        'instrucoes' => array_map(function($step) {
                            return strip_tags($step['html_instructions']);
                        }, $leg['steps']),
                        'url_maps' => "https://www.google.com/maps/dir/{$origemLat},{$origemLng}/{$destino->latitude},{$destino->longitude}",
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao obter rota: ' . $e->getMessage());
            }

            return null;
        });
    }
}
