<?php

class EmailService
{
    private $host;
    private $port;
    private $user;
    private $pass;
    private $fromName;
    private $fromEmail;
    private $encryption;
    private $socket;
    private $lastError;

    public function __construct()
    {
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->user = SMTP_USER;
        $this->pass = SMTP_PASS;
        $this->fromName = SMTP_FROM_NAME;
        $this->fromEmail = SMTP_FROM_EMAIL;
        $this->encryption = SMTP_ENCRYPTION;
        $this->socket = null;
        $this->lastError = '';
    }

    public function send($to, $subject, $body)
    {
        if (empty($this->host) || empty($this->user) || empty($this->fromEmail)) {
            $this->lastError = 'Configuração SMTP incompleta. Verifique as variáveis SMTP_* no .env';
            $this->logErro($this->lastError);
            return false;
        }

        try {
            $this->connect();

            $this->ehlo();
            if ($this->encryption === 'tls') {
                $this->startTls();
                $this->ehlo();
            }

            $this->authenticate();

            $this->mailFrom($this->fromEmail);
            $this->rcptTo($to);
            $this->data();

            $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $headers .= "To: {$to}\r\n";
            $headers .= "Subject: {$subject}\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "\r\n";

            $this->sendData($headers . $body);
            $this->quit();

            $this->disconnect();
            $this->logSucesso($to, $subject);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logErro("Falha ao enviar email para {$to}: " . $this->lastError);
            $this->disconnect();
            return false;
        }
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    private function connect()
    {
        $errno = 0;
        $errstr = '';
        $this->socket = @fsockopen(
            $this->host,
            $this->port,
            $errno,
            $errstr,
            30
        );

        if (!$this->socket) {
            throw new Exception("Falha ao conectar em {$this->host}:{$this->port} - [{$errno}] {$errstr}");
        }

        stream_set_timeout($this->socket, 30);
        $this->readResponse(220);
    }

    private function ehlo()
    {
        $this->sendCommand("EHLO " . gethostname());
        $this->readResponse(250);
    }

    private function startTls()
    {
        $this->sendCommand("STARTTLS");
        $this->readResponse(220);

        $crypto = stream_socket_enable_crypto($this->socket, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
        if (!$crypto) {
            throw new Exception("Falha ao iniciar criptografia TLS");
        }
    }

    private function authenticate()
    {
        $this->sendCommand("AUTH LOGIN");
        $this->readResponse(334);

        $this->sendCommand(base64_encode($this->user));
        $this->readResponse(334);

        $this->sendCommand(base64_encode($this->pass));
        $this->readResponse(235);
    }

    private function mailFrom($email)
    {
        $this->sendCommand("MAIL FROM:<{$email}>");
        $this->readResponse(250);
    }

    private function rcptTo($email)
    {
        $this->sendCommand("RCPT TO:<{$email}>");
        $this->readResponse(250);
    }

    private function data()
    {
        $this->sendCommand("DATA");
        $this->readResponse(354);
    }

    private function sendData($data)
    {
        $this->sendCommand($data);
        $this->readResponse(250);
    }

    private function quit()
    {
        $this->sendCommand("QUIT");
        $this->readResponse(221);
    }

    private function disconnect()
    {
        if ($this->socket) {
            @fclose($this->socket);
            $this->socket = null;
        }
    }

    private function sendCommand($command)
    {
        if (!$this->socket) {
            throw new Exception("Conexão SMTP não estabelecida");
        }
        @fwrite($this->socket, $command . "\r\n");
    }

    private function readResponse($expectedCode)
    {
        if (!$this->socket) {
            throw new Exception("Conexão SMTP não estabelecida");
        }

        $response = '';
        while (true) {
            $line = @fgets($this->socket, 512);
            if ($line === false) {
                throw new Exception("Falha ao ler resposta do servidor SMTP");
            }
            $response .= $line;
            // Linha de continuação: código + "-" (ex: 250-...)
            if (isset($line[3]) && $line[3] === '-') {
                continue;
            }
            break;
        }

        $responseCode = intval(substr($response, 0, 3));
        if ($responseCode !== $expectedCode) {
            throw new Exception(
                "Resposta SMTP inesperada (esperado {$expectedCode}, recebido {$responseCode}): " . trim($response)
            );
        }

        return $response;
    }

    private function logErro($mensagem)
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $arquivo = $logDir . '/email_errors.log';
        $data = date('Y-m-d H:i:s');
        $linha = "[{$data}] {$mensagem}" . PHP_EOL;

        @file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX);
    }

    private function logSucesso($to, $subject)
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $arquivo = $logDir . '/email_success.log';
        $data = date('Y-m-d H:i:s');
        $linha = "[{$data}] Email enviado com sucesso para {$to} | Assunto: {$subject}" . PHP_EOL;

        @file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX);
    }
}
