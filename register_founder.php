<?php
require 'vendor/autoload.php';
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\AuthenticatorAttestationResponse;

session_start();
include "config.php";

// RP info
$rp = new PublicKeyCredentialRpEntity('KV Mansion', 'kv-mansion.com');

// User info
$user = new PublicKeyCredentialUserEntity('Founder', $_SESSION['founder_id'], 'founder@kvmansion.com');

// Generate registration options
$creationOptions = new PublicKeyCredentialCreationOptions($rp, $user);

// Save challenge in session
$_SESSION['challenge'] = $creationOptions->getChallenge();

// Send options to frontend (JSON)
header('Content-Type: application/json');
echo json_encode($creationOptions);
?>
