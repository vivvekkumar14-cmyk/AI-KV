<?php
require 'vendor/autoload.php';
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;

session_start();
include "config.php";

$data = json_decode(file_get_contents('php://input'), true);

// Load credential from POST data
$loader = new PublicKeyCredentialLoader();
$publicKeyCredential = $loader->loadArray($data);

// Get response
$assertionResponse = $publicKeyCredential->getResponse();
if (!$assertionResponse instanceof AuthenticatorAssertionResponse) {
    http_response_code(400);
    echo json_encode(['status'=>'failed', 'error'=>'Invalid response']);
    exit;
}

// Get stored challenge
$challenge = base64_decode($_SESSION['auth_challenge'] ?? '');
unset($_SESSION['auth_challenge']);

// RP entity for validation
$rp = new PublicKeyCredentialRpEntity('KV Mansion', 'kv-mansion.com');

// Get stored public key for this credential
$stmt = $conn->prepare("SELECT public_key, sign_count FROM founder_keys WHERE credential_id = ?");
$stmt->bind_param("s", $publicKeyCredential->getId());
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(400);
    echo json_encode(['status'=>'failed', 'error'=>'Credential not found']);
    exit;
}

$row = $result->fetch_assoc();
$publicKeyData = json_decode($row['public_key'], true);
$storedSignCount = $row['sign_count'];

// Create PublicKeyCredentialSource from stored data
$credentialSource = new PublicKeyCredentialSource(
    $publicKeyCredential->getId(),
    'public-key',
    [],
    $publicKeyData['alg'],
    $publicKeyData,
    $storedSignCount
);

// Validate assertion
$validator = new AuthenticatorAssertionResponseValidator();
try {
    $validator->check(
        $credentialSource,
        $assertionResponse,
        $requestOptions = null, // Not needed for assertion
        $request = null, // Not needed for assertion
        $challenge,
        $rp,
        null // User not required for login
    );

    // Update sign count
    $newSignCount = $assertionResponse->getAuthenticatorData()->getCounter();
    $updateStmt = $conn->prepare("UPDATE founder_keys SET sign_count = ? WHERE credential_id = ?");
    $updateStmt->bind_param("is", $newSignCount, $publicKeyCredential->getId());
    $updateStmt->execute();

    // Set session as authenticated
    $_SESSION['founder_authenticated'] = true;
    $_SESSION['founder_id'] = $_SESSION['founder_id'];

    echo json_encode(['status'=>'ok']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status'=>'failed', 'error'=>$e->getMessage()]);
}
?>
