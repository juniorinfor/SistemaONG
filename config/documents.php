<?php

return [
    /*
     * Quantos dias antes do vencimento o sistema classifica como "vence em breve"
     * e começa a enviar alertas.
     */
    'warn_days_before_expiry' => 30,

    /*
     * Marcos de alerta por e-mail (dias antes do vencimento).
     */
    'alert_thresholds' => [30, 10, 1],

    /*
     * Tamanho máximo de upload em KB (10 MB padrão).
     */
    'max_upload_kb' => 10240,

    /*
     * Tipos MIME aceitos para upload de documentos.
     */
    'allowed_mimes' => ['application/pdf', 'image/jpeg', 'image/png'],

    /*
     * Disco de armazenamento (storage/app/private para docs confidenciais).
     */
    'disk' => 'local',

    /*
     * Prefixo de pasta dentro do disco.
     */
    'storage_path' => 'documents',
];
