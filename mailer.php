<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

require_once 'vendor/autoload.php';
require_once 'classe-db.php';

session_start();


$clientId = 'id*************.apps.googleusercontent.com';
$clientSecret = 'G*************_E************k';

$email = 'teste@gmail.com';  // Substitua pelo seu endereço de e-mail

$db = new DB();
$refreshToken = $db->get_refresh_token();

if ($refreshToken == false) {
    echo "<script>alert('Token de acesso inexistente entre em contato com o desenvolvedor');</script>";
    exit;
}


$provider = new Google([
    'clientId' => $clientId,
    'clientSecret'  => $clientSecret,
    'redirectUri' => 'http://localhost/oauth-google/get_oauth_token.php', // Substitua pela URI de redirecionamento configurada no Console de Desenvolvedor do Google
    'accessType' => 'offline',  // Indica que você quer um refresh token
    'scope' => ['https://mail.google.com/'],
]);

//if ($db->is_table_empty()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $refreshToken,
    ]);
    $db->update_refresh_token($newAccessToken);
    echo "Token Atualizado com sucesso!";
//}
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;

$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

$mail->SMTPAuth = true;
$mail->AuthType = 'XOAUTH2';
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);

$mail->setFrom($email, 'Desenvolvedor Web');
$mail->addAddress('james@dominio.com.br', 'James Gostlin');
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Subject = "Validação da API OAuth";
$mail->Body = '<b>Sem assunto teste</b>';

if (!$mail->send()) {
    echo "Erro ao enviar E-mail: " . $mail->ErrorInfo;
} else {
    echo "E-mail enviado com SUCESSO!";
}
