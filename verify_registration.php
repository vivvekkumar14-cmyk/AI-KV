<?php
require 'vendor/autoload.php';
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\PublicKeyCredentialRpEntity;

session_start();
include "config.php";

$data = json_decode(file_get_contents('php://input'), true);

// Load credential from POST data
$loader = new PublicKeyCredentialLoader();
$publicKeyCredential = $loader->loadArray($data);

// Get response
$attestationResponse = $publicKeyCredential->getResponse();
if (!$attestationResponse instanceof AuthenticatorAttestationResponse) {
    http_response_code(400);
    echo json_encode(['status'=>'failed', 'error'=>'Invalid response']);
    exit;
}

// Get stored challenge
$challenge = $_SESSION['challenge'] ?? '';
unset($_SESSION['challenge']);

// RP entity for validation
$rp = new PublicKeyCredentialRpEntity('KV Mansion', 'kv-mansion.com');

// Validate attestation
$validator = new AuthenticatorAttestationResponseValidator();
try {
    $publicKeyCredentialSource = $validator->check(
        $attestationResponse,
        $publicKeyCredential,
        $challenge,
        $rp
    );

    // Store credential in database
    $stmt = $conn->prepare("INSERT INTO founder_keys (founder_id, credential_id, public_key, sign_count) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", 
        $_SESSION['founder_id'],
        $publicKeyCredentialSource->getPublicKeyCredentialId(),
        json_encode($publicKeyCredentialSource->getCredentialPublicKey()),
        $publicKeyCredentialSource->getCounter()
    );
    $stmt->execute();

    echo json_encode(['status'=>'ok']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status'=>'failed', 'error'=>$e->getMessage()]);
}
?>
