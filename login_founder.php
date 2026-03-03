<?php
require 'vendor/autoload.php';
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;

session_start();
include "config.php";

// RP info
$rp = new PublicKeyCredentialRpEntity('KV Mansion', 'kv-mansion.com');

// Get stored credentials for this founder
$stmt = $conn->prepare("SELECT credential_id FROM founder_keys WHERE founder_id = ?");
$stmt->bind_param("i", $_SESSION['founder_id']);
$stmt->execute();
$result = $stmt->get_result();

$allowCredentials = [];
while ($row = $result->fetch_assoc()) {
    $allowCredentials[] = [
        'type' => 'public-key',
        'id' => base64_encode($row['credential_id'])
    ];
}

// Generate authentication options
$requestOptions = new PublicKeyCredentialRequestOptions(
    random_bytes(32), // challenge
    $rp,
    $allowCredentials
);

// Save challenge in session
$_SESSION['auth_challenge'] = base64_encode($requestOptions->getChallenge());

// Send options to frontend (JSON)
header('Content-Type: application/json');
echo json_encode([
    'challenge' => $_SESSION['auth_challenge'],
    'allowCredentials' => $allowCredentials,
    'userVerification' => 'required',
    'rpId' => 'kv-mansion.com'
]);
?>
