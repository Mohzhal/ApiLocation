<?php
// Memulai output buffering
ob_start();

// Mengatur header CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Menangani preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json');

$csvFile = 'location_data.csv';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mendapatkan data input dari request
    $data = json_decode(file_get_contents('php://input'), true);
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    // Membuka file CSV dalam mode tulis, yang akan menghapus konten yang ada
    $fp = fopen($csvFile, 'w');
    if ($fp === false) {
        echo json_encode(['status' => 'error', 'message' => 'Unable to open file for writing']);
        exit;
    }

    // Menulis latitude dan longitude baru ke file
    fputcsv($fp, [$latitude, $longitude]);
    fclose($fp);

    // Mengembalikan respons sukses
    echo json_encode(['status' => 'success']);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($csvFile)) {
        // Membuka file CSV untuk dibaca
        $file = fopen($csvFile, 'r');
        if ($file === false) {

            echo json_encode(['status' => 'error', 'message' => 'Unable to open file for reading']);
            exit;
        }

        // Membaca data dari file CSV
        $data = fgetcsv($file);
        fclose($file);

        // Mengembalikan latitude dan longitude sebagai JSON
        if ($data !== false) {
            echo json_encode(['latitude' => $data[0], 'longitude' => $data[1]]);
        } else {
            echo json_encode(['error' => 'No data found']);
        }
    } else {
        // Mengembalikan error jika file CSV tidak ada
        echo json_encode(['error' => 'No data found']);
    }
} else {
    // Mengembalikan error untuk metode request yang tidak didukung
    echo json_encode(['error' => 'Invalid request method']);
}

// Mengakhiri output buffering dan mengirim output ke browser
ob_end_flush();
?>