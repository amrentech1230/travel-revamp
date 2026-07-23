<?php
/**
 * Mondee API Integration Class - TravenzoTravel
 * Handles flight search, pricing, booking, and cancellation via Mondee API
 */
class MondeeAPI
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;
    private $agentId;
    private $token;

    public function __construct()
    {
        $this->apiUrl    = MONDEE_API_URL;
        $this->apiKey    = MONDEE_API_KEY;
        $this->apiSecret = MONDEE_API_SECRET;
        $this->agentId   = MONDEE_AGENT_ID;
        $this->token     = null;
    }

    // ─── Authentication ──────────────────────────────────────────────

    /**
     * Authenticate with Mondee API and get session token
     */
    public function authenticate()
    {
        $payload = [
            'apiKey'    => $this->apiKey,
            'apiSecret' => $this->apiSecret,
            'agentId'   => $this->agentId,
        ];

        $response = $this->makeRequest('/auth/token', $payload, 'POST', false);

        if (isset($response['token'])) {
            $this->token = $response['token'];
            return true;
        }

        return false;
    }

    // ─── Flight Search ───────────────────────────────────────────────

    /**
     * Search flights based on parameters
     *
     * @param array $params Search parameters
     * @return array Formatted flight results
     */
    public function searchFlights($params)
    {
        // Ensure authenticated
        if (!$this->token) {
            $this->authenticate();
        }

        // Build search payload per Mondee API spec
        $searchPayload = [
            'tripType'    => $this->mapTripType($params['trip_type']),
            'origin'      => $params['origin'],
            'destination' => $params['destination'],
            'departDate'  => $params['departure_date'],
            'cabinClass'  => $this->mapCabinClass($params['cabin_class']),
            'adults'      => (int)$params['adults'],
            'children'    => (int)$params['children'],
            'infants'     => (int)$params['infants'],
            'currency'    => 'USD',
            'maxResults'  => 50,
        ];

        if (!empty($params['return_date'])) {
            $searchPayload['returnDate'] = $params['return_date'];
        }

        $response = $this->makeRequest('/flights/search', $searchPayload);

        if (isset($response['error'])) {
            // If API fails, return demo data for development
            return $this->getDemoFlights($params);
        }

        return $this->formatSearchResults($response);
    }

    /**
     * Get flight pricing/fare rules
     */
    public function getFlightPricing($flightId, $sessionId = null)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $payload = [
            'flightId'  => $flightId,
            'sessionId' => $sessionId,
        ];

        $response = $this->makeRequest('/flights/price', $payload);

        if (isset($response['error'])) {
            return null;
        }

        return $response;
    }

    // ─── Booking ─────────────────────────────────────────────────────

    /**
     * Create a flight booking
     *
     * @param array $flightData Selected flight details
     * @param array $passengers Passenger details
     * @param array $contact Contact information
     * @return array Booking response with PNR
     */
    public function createBooking($flightData, $passengers, $contact)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $payload = [
            'flightId'   => $flightData['id'],
            'sessionId'  => $flightData['session_id'] ?? null,
            'passengers' => $this->formatPassengers($passengers),
            'contact'    => [
                'email' => $contact['email'],
                'phone' => $contact['phone'],
            ],
            'agentRef'   => generateRef('TRV'),
        ];

        $response = $this->makeRequest('/flights/book', $payload);

        if (isset($response['error'])) {
            return [
                'success' => false,
                'error'   => $response['error'],
            ];
        }

        return [
            'success'    => true,
            'pnr'        => $response['pnr'] ?? null,
            'booking_id' => $response['bookingId'] ?? null,
            'status'     => $response['status'] ?? 'confirmed',
        ];
    }

    /**
     * Get booking details by PNR or Mondee booking ID
     */
    public function getBooking($bookingId)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = $this->makeRequest('/flights/booking/' . $bookingId, [], 'GET');

        return $response;
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking($bookingId, $reason = '')
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $payload = [
            'bookingId' => $bookingId,
            'reason'    => $reason,
        ];

        $response = $this->makeRequest('/flights/cancel', $payload);

        return $response;
    }

    // ─── Helper Methods ──────────────────────────────────────────────

    /**
     * Make HTTP request to Mondee API
     */
    private function makeRequest($endpoint, $data = [], $method = 'POST', $auth = true)
    {
        $url = $this->apiUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($auth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => 'Connection failed: ' . $error];
        }

        if ($httpCode >= 400) {
            return ['error' => 'API error (HTTP ' . $httpCode . ')'];
        }

        return json_decode($response, true) ?? ['error' => 'Invalid response'];
    }

    /**
     * Format search results into standard structure
     */
    private function formatSearchResults($response)
    {
        $flights = [];
        $results = $response['flights'] ?? $response['results'] ?? [];

        foreach ($results as $item) {
            $flights[] = [
                'id'             => $item['id'] ?? uniqid('FL'),
                'session_id'     => $response['sessionId'] ?? null,
                'airline_code'   => $item['airlineCode'] ?? $item['carrier'] ?? 'XX',
                'airline_name'   => $item['airlineName'] ?? $item['carrierName'] ?? 'Airline',
                'flight_number'  => $item['flightNumber'] ?? 'XX000',
                'departure_time' => $item['departureTime'] ?? '00:00',
                'arrival_time'   => $item['arrivalTime'] ?? '00:00',
                'duration'       => $item['duration'] ?? '0h 0m',
                'duration_mins'  => $item['durationMinutes'] ?? 0,
                'stops'          => $item['stops'] ?? 0,
                'stop_cities'    => $item['stopCities'] ?? [],
                'aircraft'       => $item['aircraft'] ?? 'Boeing 737',
                'base_fare'      => (float)($item['baseFare'] ?? $item['fare'] ?? 0),
                'taxes'          => (float)($item['taxes'] ?? 0),
                'service_fee'    => 5.00,
                'total_price'    => (float)($item['totalPrice'] ?? $item['price'] ?? 0),
                'cabin_baggage'  => $item['cabinBaggage'] ?? '7 kg',
                'checkin_baggage' => $item['checkinBaggage'] ?? '15 kg',
                'refundable'     => $item['refundable'] ?? false,
            ];
        }

        return ['flights' => $flights, 'count' => count($flights)];
    }

    /**
     * Format passengers for API request
     */
    private function formatPassengers($passengers)
    {
        $formatted = [];
        foreach ($passengers as $pax) {
            $formatted[] = [
                'type'        => $pax['type'] ?? 'adult',
                'title'       => $pax['title'],
                'firstName'   => $pax['first_name'],
                'lastName'    => $pax['last_name'],
                'dateOfBirth' => $pax['dob'],
                'gender'      => $pax['gender'],
                'passport'    => $pax['passport_no'] ?? null,
                'nationality' => $pax['nationality'] ?? null,
            ];
        }
        return $formatted;
    }

    /**
     * Map trip type to Mondee API format
     */
    private function mapTripType($type)
    {
        $map = [
            'oneway'    => 'ONE_WAY',
            'roundtrip' => 'ROUND_TRIP',
            'multicity' => 'MULTI_CITY',
        ];
        return $map[$type] ?? 'ONE_WAY';
    }

    /**
     * Map cabin class to Mondee API format
     */
    private function mapCabinClass($class)
    {
        $map = [
            'economy'         => 'ECONOMY',
            'premium_economy' => 'PREMIUM_ECONOMY',
            'business'        => 'BUSINESS',
            'first'           => 'FIRST',
        ];
        return $map[$class] ?? 'ECONOMY';
    }

    // ─── Demo Data (for development without API keys) ────────────────

    /**
     * Return demo flight results for testing/development
     */
    private function getDemoFlights($params)
    {
        $airlines = [
            ['code' => 'AI', 'name' => 'Air India'],
            ['code' => '6E', 'name' => 'IndiGo'],
            ['code' => 'SG', 'name' => 'SpiceJet'],
            ['code' => 'UK', 'name' => 'Vistara'],
            ['code' => 'AA', 'name' => 'American Airlines'],
            ['code' => 'UA', 'name' => 'United Airlines'],
            ['code' => 'DL', 'name' => 'Delta Airlines'],
            ['code' => 'EK', 'name' => 'Emirates'],
            ['code' => 'SQ', 'name' => 'Singapore Airlines'],
            ['code' => 'BA', 'name' => 'British Airways'],
        ];

        $flights = [];
        $numResults = rand(8, 15);

        for ($i = 0; $i < $numResults; $i++) {
            $airline = $airlines[array_rand($airlines)];
            $depHour = rand(5, 22);
            $depMin = rand(0, 5) * 10;
            $durationH = rand(1, 8);
            $durationM = rand(0, 5) * 10;
            $arrHour = ($depHour + $durationH) % 24;
            $arrMin = ($depMin + $durationM) % 60;
            $stops = rand(0, 2);
            $baseFare = rand(60, 800);
            $taxes = round($baseFare * 0.18, 2);

            $flights[] = [
                'id'              => 'FL' . strtoupper(substr(md5($i . $params['origin'] . $params['destination']), 0, 10)),
                'session_id'      => 'SES' . time(),
                'airline_code'    => $airline['code'],
                'airline_name'    => $airline['name'],
                'flight_number'   => $airline['code'] . rand(100, 999),
                'departure_time'  => sprintf('%02d:%02d', $depHour, $depMin),
                'arrival_time'    => sprintf('%02d:%02d', $arrHour, $arrMin),
                'duration'        => $durationH . 'h ' . $durationM . 'm',
                'duration_mins'   => ($durationH * 60) + $durationM,
                'stops'           => $stops,
                'stop_cities'     => [],
                'aircraft'        => ['Boeing 737', 'Airbus A320', 'Boeing 777', 'Airbus A380'][rand(0, 3)],
                'base_fare'       => $baseFare,
                'taxes'           => $taxes,
                'service_fee'     => 5.00,
                'total_price'     => $baseFare + $taxes + 5.00,
                'cabin_baggage'   => '7 kg',
                'checkin_baggage'  => $stops === 0 ? '15 kg' : '20 kg',
                'refundable'      => (bool)rand(0, 1),
            ];
        }

        // Sort by price
        usort($flights, fn($a, $b) => $a['total_price'] <=> $b['total_price']);

        return ['flights' => $flights, 'count' => count($flights)];
    }
}
