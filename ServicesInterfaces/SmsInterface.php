<?php

interface SmsInterface
{
	public function send(string $to, string $message, array $extraInformation): array|object;
	public function parseTemplate($message): string;
	public function getCredentials(): array;
}