/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `checklist_items_checklist_id_document_type_id_unique` (`checklist_id`,`document_type_id`),
  KEY `checklist_items_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `checklist_items_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_items_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `legal_basis` varchar(255) DEFAULT NULL COMMENT 'ex: Lei 13.019/2014, art. 34',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `checklists_slug_unique` (`slug`),
  KEY `checklists_institution_id_foreign` (`institution_id`),
  CONSTRAINT `checklists_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `sphere` enum('federal','estadual','municipal','interno') NOT NULL DEFAULT 'federal',
  `validity_days` int(10) unsigned DEFAULT NULL COMMENT 'null = sem validade fixa',
  `requires_history` tinyint(1) NOT NULL DEFAULT 0,
  `is_per_person` tinyint(1) NOT NULL DEFAULT 0,
  `instructions` longtext DEFAULT NULL COMMENT 'Markdown: como/onde obter',
  `official_url` varchar(255) DEFAULT NULL,
  `is_public_by_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_types_institution_id_foreign` (`institution_id`),
  CONSTRAINT `document_types_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned NOT NULL,
  `person_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `mime_type` varchar(50) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL COMMENT 'bytes',
  `issued_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `protocol_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `is_current` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'versão vigente do tipo',
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_document_type_id_foreign` (`document_type_id`),
  KEY `documents_person_id_foreign` (`person_id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `documents_institution_id_document_type_id_is_current_index` (`institution_id`,`document_type_id`,`is_current`),
  KEY `documents_expires_at_index` (`expires_at`),
  CONSTRAINT `documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) NOT NULL DEFAULT 'Jaboatão dos Guararapes',
  `state` varchar(2) NOT NULL DEFAULT 'PE',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institutions_cnpj_unique` (`cnpj`),
  UNIQUE KEY `institutions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint(20) unsigned NOT NULL,
  `document_id` bigint(20) unsigned NOT NULL,
  `channel` varchar(255) NOT NULL DEFAULT 'email',
  `notifiable_type` varchar(255) DEFAULT NULL,
  `notifiable_id` bigint(20) unsigned DEFAULT NULL,
  `days_before_expiry` smallint(5) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_logs_institution_id_foreign` (`institution_id`),
  KEY `notification_logs_document_id_channel_days_before_expiry_index` (`document_id`,`channel`,`days_before_expiry`),
  CONSTRAINT `notification_logs_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_logs_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `people` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `type` enum('diretoria','voluntario','colaborador') NOT NULL DEFAULT 'diretoria',
  `mandate_start` date DEFAULT NULL,
  `mandate_end` date DEFAULT NULL,
  `works_with_children` tinyint(1) NOT NULL DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `people_institution_id_foreign` (`institution_id`),
  CONSTRAINT `people_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

mysqldump.exe: Got error: 1049: "Unknown database 'institutions'" when selecting the database
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES (1,'Associação Promessa','00.000.000/0001-00','promessa','Jaboatão dos Guararapes, PE','Jaboatão dos Guararapes','PE',NULL,NULL,NULL,NULL,'Promover a inclusão social e o desenvolvimento humano de crianças, adolescentes e famílias em situação de vulnerabilidade.',1,'2026-06-12 18:53:01','2026-06-12 18:53:01');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (1,1,'Ata de Fundação (registrada em cartório)','juridico','interno',NULL,1,0,'## Como obter\n\nA Ata de Fundação é lavrada no ato de constituição da associação e registrada em **Cartório de Registro de Pessoas Jurídicas**.\n\n**Passos para obter cópia autenticada:**\n1. Compareça ao cartório onde foi feito o registro original.\n2. Solicite certidão ou cópia autenticada da ata.\n3. O prazo de emissão é geralmente imediato ou em até 2 dias úteis.\n4. Custo: conforme tabela de emolumentos do TJPE.\n\n> Este documento não vence, mas editais podem exigir certidão atualizada do cartório.',NULL,0,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(2,1,'Estatuto Social Consolidado','juridico','interno',NULL,1,0,'## Como obter\n\nO Estatuto Social e suas alterações devem estar registrados em **Cartório de Registro de Pessoas Jurídicas**.\n\n**Passos:**\n1. Mantenha sempre a versão consolidada com todas as alterações aprovadas em assembleia.\n2. Cada alteração deve ser aprovada em assembleia geral e registrada em cartório.\n3. Solicite certidão de inteiro teor no cartório para editais.\n\n> Guarde o histórico de todas as versões (exigido pelo MROSC).',NULL,1,1,20,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(3,1,'Ata de Eleição e Posse da Diretoria','juridico','interno',NULL,1,0,'## Como obter\n\nA Ata de Eleição deve ser lavrada na assembleia que elegeu a diretoria e registrada em cartório.\n\n**Passos:**\n1. Realize assembleia geral conforme previsto no estatuto.\n2. Lavrar a ata com qualificação completa de todos os eleitos.\n3. Registrar em Cartório de Registro de Pessoas Jurídicas.\n4. Publicar no Diário Oficial se exigido pelo estatuto ou por lei.\n\n> A validade é o período do mandato definido no estatuto. Cadastre a data de fim do mandato como data de vencimento.',NULL,0,1,30,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(4,1,'Outras Atas de Assembleias','juridico','interno',NULL,1,0,'## Como obter\n\nAtas de assembleias gerais ordinárias (AGO) e extraordinárias (AGE) devem ser lavradas e assinadas pelos presentes.\n\n**Boas práticas:**\n1. Numerar e arquivar todas as atas sequencialmente.\n2. Registrar em cartório quando deliberarem sobre alterações estatutárias ou eleição de diretoria.\n3. Guardar o livro de atas ou as atas avulsas devidamente assinadas.',NULL,0,1,40,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(5,1,'Relação Nominal da Diretoria com Qualificação (MROSC)','juridico','federal',NULL,0,0,'## Como obter\n\nDocumento produzido pela própria ONG, exigido pelo **MROSC (Lei 13.019/2014)**.\n\n**Conteúdo obrigatório:**\n- Nome completo de cada dirigente\n- RG e CPF\n- Endereço residencial\n- Cargo na diretoria\n- Período do mandato\n\n**Passos:**\n1. Elabore o documento com base na ata de eleição vigente.\n2. Assine e carimbe com o CNPJ da entidade.\n3. Atualize sempre que houver mudança na diretoria.',NULL,0,1,50,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(6,1,'Certidão de Existência Jurídica (Cartório de PJ)','juridico','interno',90,0,0,'## Como obter\n\nSolicite no **Cartório de Registro de Pessoas Jurídicas** onde a ONG está registrada.\n\n**Passos:**\n1. Compareça ao cartório com CNPJ e documento do representante legal.\n2. Solicite a \"Certidão de Inteiro Teor\" ou \"Breve Relato\".\n3. O custo segue a tabela de emolumentos do TJPE.\n4. Validade geralmente aceita pelos editais: **90 dias**.\n\n> Renove próximo ao envio de editais.',NULL,0,1,60,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(7,1,'Cartão CNPJ — Comprovante de Inscrição (RFB)','juridico','federal',90,0,0,'## Como obter\n\nEmissão **gratuita e online** no portal da Receita Federal.\n\n**Passos:**\n1. Acesse: https://www.gov.br/receitafederal/pt-br/assuntos/cadastros/cnpj/comprovante-de-inscricao-e-de-situacao-cadastral\n2. Informe o CNPJ.\n3. Resolva o CAPTCHA e clique em \"Consultar\".\n4. Imprima ou salve o PDF.\n\n> Editais costumam exigir emitido há menos de **90 dias**. Emissão instantânea.','https://www.gov.br/receitafederal/pt-br/assuntos/cadastros/cnpj/comprovante-de-inscricao-e-de-situacao-cadastral',1,1,70,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(8,1,'Comprovante de Endereço da Sede','juridico','interno',90,0,0,'## Como obter\n\n**Documentos aceitos:**\n- Conta de água, luz, gás ou telefone em nome da ONG ou do responsável legal (emitida nos últimos 3 meses).\n- Contrato de locação registrado em cartório.\n- Declaração de cessão de uso do imóvel.\n\n**Dica:** Prefira documentos em nome da própria associação para evitar questionamentos.',NULL,0,1,80,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(9,1,'Alvará de Localização e Funcionamento (Prefeitura de Jaboatão)','juridico','municipal',365,0,0,'## Como obter\n\nSolicite na **Prefeitura de Jaboatão dos Guararapes** (Secretaria de Finanças ou Secretaria de Desenvolvimento Econômico).\n\n**Passos:**\n1. Acesse o portal da prefeitura: https://www.jaboatao.pe.gov.br\n2. Verifique se há sistema online de alvará.\n3. Caso contrário, compareça presencialmente com: CNPJ, estatuto, comprovante de endereço.\n4. O alvará normalmente é renovado **anualmente**.\n\n> ONGs podem ter tratamento diferenciado (isenção de taxas). Consulte a secretaria.','https://www.jaboatao.pe.gov.br',0,1,90,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(10,1,'Documentos do Responsável Legal (RG e CPF)','juridico','interno',NULL,0,1,'## Como obter\n\n**RG:** emitido pela Secretaria de Segurança Pública de PE (SSP-PE). Para segunda via, acesse: https://www.sds.pe.gov.br\n\n**CPF:** emitido pela Receita Federal ou Correios. Para impressão: https://www.gov.br/receitafederal/pt-br/assuntos/cadastros/cpf\n\n> Mantenha cópias autenticadas dos documentos do presidente e demais dirigentes.',NULL,0,1,100,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(11,1,'Declarações MROSC art. 39 (não impedimento)','juridico','federal',180,0,0,'## Como obter\n\nAs **Declarações do art. 39 da Lei 13.019/2014 (MROSC)** são autodeclaratórias, elaboradas pela própria ONG.\n\n**Conteúdo exigido (entre outros):**\n- Não ter dirigente condenado por ato de improbidade administrativa\n- Não contratar cônjuge/parente de agente público do órgão parceiro\n- Não ter fins lucrativos\n\n**Modelos:** disponíveis no portal de transferências voluntárias: https://www.gov.br/transferencias-voluntarias/pt-br\n\n> Os modelos variam por edital. Solicite o modelo ao órgão financiador.','https://www.gov.br/transferencias-voluntarias/pt-br',0,1,110,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(12,1,'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)','federal','federal',180,0,0,'## Como obter\n\nEmissão **gratuita e online** pela Receita Federal.\n\n**Passos:**\n1. Acesse: https://solucoes.receita.fazenda.gov.br/Servicos/certidaointernet/PJ/Emitir\n2. Informe o CNPJ.\n3. Clique em \"Emitir Certidão\".\n4. Salve o PDF (com código de autenticação).\n\n**Validade:** 180 dias.\n\n> Em caso de pendências, regularize pelo portal e-CAC: https://cav.receita.fazenda.gov.br','https://solucoes.receita.fazenda.gov.br/Servicos/certidaointernet/PJ/Emitir',1,1,200,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(13,1,'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)','federal','federal',30,0,0,'## Como obter\n\nEmissão **gratuita e online** pela Caixa Econômica Federal.\n\n**Passos:**\n1. Acesse: https://consulta-crf.caixa.gov.br\n2. Informe o CNPJ.\n3. O sistema gera o CRF automaticamente se estiver regular.\n4. Salve o PDF.\n\n**Validade:** 30 dias.\n\n> ONGs sem empregados ainda precisam do CRF. Em caso de irregularidade, acesse a agência da Caixa.','https://consulta-crf.caixa.gov.br',1,1,210,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(14,1,'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)','federal','federal',180,0,0,'## Como obter\n\nEmissão **gratuita e online** pelo Tribunal Superior do Trabalho (TST).\n\n**Passos:**\n1. Acesse: https://www.tst.jus.br/certidao\n2. Informe o CNPJ.\n3. Emita e salve o PDF.\n\n**Validade:** 180 dias.\n\n> Em caso de débitos trabalhistas, consulte a Vara do Trabalho responsável.','https://www.tst.jus.br/certidao',1,1,220,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(15,1,'RAIS Negativa / Recibo eSocial (anual)','federal','federal',365,1,0,'## Como obter\n\nA RAIS foi absorvida pelo **eSocial** (portaria MTE 1.127/2019). ONGs devem declarar anualmente.\n\n**Passos:**\n1. Acesse o eSocial: https://www.gov.br/esocial/pt-br\n2. Para entidades **sem empregados**: transmita o evento S-1000 (dados do empregador) e guarde o recibo.\n3. Editais antigos ainda podem pedir \"RAIS Negativa\" — o recibo do eSocial equivale.\n\n**Prazo:** declaração anual, geralmente até março/abril do ano seguinte.\n\n> Guarde o número de recibo de cada competência.','https://www.gov.br/esocial/pt-br',0,1,230,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(16,1,'Certidão de Antecedentes Criminais — Polícia Federal (dirigentes)','federal','federal',180,0,1,'## Como obter\n\nEmissão **gratuita e online** pela Polícia Federal.\n\n**Passos:**\n1. Acesse: https://www.gov.br/pf/pt-br/assuntos/antecedentes-criminais\n2. Faça login com Gov.br (necessário conta nível Prata ou Ouro).\n3. Selecione \"Emitir certidão de antecedentes criminais\".\n4. Salve o PDF com código de autenticação.\n\n**Validade:** 180 dias (verificar exigência do edital).\n\n> Geralmente exigida para presidente e demais dirigentes.','https://www.gov.br/pf/pt-br/assuntos/antecedentes-criminais',0,1,240,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(17,1,'Certidão CADIN/CAUC (Transferegov — convênios federais)','federal','federal',30,0,0,'## Como obter\n\nConsulta no **Transferegov** (antigo SICONV/SIAFI).\n\n**Passos:**\n1. Acesse: https://transfere.gov.br\n2. Consulte a situação da entidade no CAUC (Caderno de Exigências para Transferência).\n3. O sistema exibe pendências em tempo real.\n\n> Exigido apenas para convênios com o governo federal. Regularize pendências no CAUC antes de candidatar a editais federais.','https://transfere.gov.br',0,1,250,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(18,1,'Certidão de Regularidade Fiscal — Sefaz-PE','estadual','estadual',60,0,0,'## Como obter\n\nEmissão **gratuita e online** pela Secretaria da Fazenda de Pernambuco.\n\n**Passos:**\n1. Acesse: https://www.sefaz.pe.gov.br/certidoes\n2. Informe o CNPJ.\n3. Emita e salve o PDF.\n\n**Validade:** 60 dias.\n\n> Em caso de débitos estaduais, contate a Sefaz-PE para parcelamento ou regularização.','https://www.sefaz.pe.gov.br/certidoes',1,1,300,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(19,1,'Certidão Negativa da Dívida Ativa Estadual — PGE-PE','estadual','estadual',60,0,0,'## Como obter\n\nEmissão online pela **Procuradoria Geral do Estado de Pernambuco (PGE-PE)**.\n\n**Passos:**\n1. Acesse: https://www.pge.pe.gov.br\n2. Localize o serviço de certidões.\n3. Informe o CNPJ e emita.\n\n**Validade:** 60 dias.','https://www.pge.pe.gov.br',1,1,310,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(20,1,'Certidão de Distribuição Cível e Criminal — TJPE','estadual','estadual',90,0,0,'## Como obter\n\nEmissão pelo **Tribunal de Justiça de Pernambuco (TJPE)**.\n\n**Passos:**\n1. Acesse: https://www.tjpe.jus.br/certidoes\n2. Solicite certidão de distribuição de ações cíveis e criminais.\n3. Informe o CNPJ da entidade.\n4. Salve o PDF.\n\n**Validade:** 90 dias (verificar o edital).','https://www.tjpe.jus.br/certidoes',0,1,320,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(21,1,'Antecedentes Criminais — Polícia Civil de PE (voluntários)','estadual','estadual',180,0,1,'## Como obter\n\nEmissão pela **Secretaria de Defesa Social de Pernambuco (SDS-PE)**.\n\n**Passos:**\n1. Acesse: https://www.sds.pe.gov.br\n2. Localize o serviço de antecedentes criminais.\n3. Faça o agendamento ou emita online com CPF do solicitante.\n4. Validade: 180 dias (exigido especialmente para quem trabalha com crianças e adolescentes).\n\n> Obrigatório pelo **ECA** para voluntários em contato com crianças/adolescentes.','https://www.sds.pe.gov.br',0,1,330,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(22,1,'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)','municipal','municipal',60,0,0,'## Como obter\n\nSolicite na **Secretaria de Finanças da Prefeitura de Jaboatão dos Guararapes**.\n\n**Passos:**\n1. Acesse o portal: https://www.jaboatao.pe.gov.br\n2. Verifique se há serviço online de certidões.\n3. Caso contrário, compareça presencialmente com CNPJ e documentos da entidade.\n\n**Validade:** 60 dias.\n\n> ONGs podem ter isenção de IPTU e ISS. Consulte a secretaria para regularizar antes de solicitar a certidão.','https://www.jaboatao.pe.gov.br',1,1,400,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(23,1,'Inscrição Municipal (CIM) de Jaboatão','municipal','municipal',NULL,0,0,'## Como obter\n\nA Inscrição Municipal (CIM) é emitida pela **Prefeitura de Jaboatão dos Guararapes** no ato do cadastro da entidade.\n\n**Passos:**\n1. Acesse: https://www.jaboatao.pe.gov.br\n2. Localize o serviço \"Inscrição Municipal\" ou \"Cadastro de Contribuintes\".\n3. Apresente CNPJ, estatuto e endereço da sede.\n\n> Nem todas as ONGs precisam de CIM. Verifique com a prefeitura se a atividade exige.','https://www.jaboatao.pe.gov.br',0,1,410,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(24,1,'Balanço Patrimonial (assinado por contador CRC)','contabil','interno',365,1,0,'## Como obter\n\nElaborado pelo **contador responsável** (com assinatura e CRC), conforme **ITG 2002 — Entidades sem Finalidade de Lucros**.\n\n**Documentos do conjunto contábil obrigatório (ITG 2002):**\n- Balanço Patrimonial (BP)\n- DRE — Demonstração do Resultado do Exercício\n- DMPL — Demonstração das Mutações do Patrimônio Líquido\n- DFC — Demonstração dos Fluxos de Caixa\n- Notas Explicativas\n\n**Prazo:** elaborar e publicar até **30/04** do ano seguinte ao exercício.\n\n> Guarde todos os anos (histórico exigido por editais e prestações de contas).',NULL,1,1,500,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(25,1,'DRE — Demonstração do Resultado do Exercício','contabil','interno',365,1,0,'## Como obter\n\nFaz parte do conjunto contábil obrigatório (ITG 2002). Elaborada pelo contador CRC.\n\n> Sempre junto com o Balanço Patrimonial e demais demonstrações.',NULL,1,1,510,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(26,1,'Balancete de Verificação','contabil','interno',NULL,1,0,'## Como obter\n\nElaborado mensalmente ou por período pelo contador. Alguns editais exigem o balancete do exercício mais recente.\n\n> Solicite ao contador responsável pela contabilidade da ONG.',NULL,0,1,520,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(27,1,'DMPL, DFC e Notas Explicativas (ITG 2002)','contabil','interno',365,1,0,'## Como obter\n\nDemonstrações complementares exigidas pela **ITG 2002**:\n- **DMPL:** Demonstração das Mutações do Patrimônio Líquido\n- **DFC:** Demonstração dos Fluxos de Caixa\n- **Notas Explicativas:** contexto e metodologias contábeis\n\nElaboradas pelo contador CRC junto com o restante do conjunto contábil anual.',NULL,1,1,530,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(28,1,'Parecer do Conselho Fiscal (anual)','contabil','interno',365,1,0,'## Como obter\n\nO Conselho Fiscal da associação emite o parecer após análise das demonstrações contábeis.\n\n**Passos:**\n1. Apresente o conjunto contábil ao Conselho Fiscal.\n2. O Conselho emite parecer aprovando ou indicando ressalvas.\n3. Assinar e arquivar com as demonstrações contábeis do exercício.\n\n> Previsto no estatuto. Se a ONG não tiver Conselho Fiscal formal, constitua-o.',NULL,0,1,540,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(29,1,'Prestação de Contas Anual / Relatório de Atividades','contabil','interno',365,1,0,'## Como obter\n\nDocumento elaborado pela diretoria e aprovado em assembleia geral ordinária (AGO).\n\n**Conteúdo mínimo:**\n- Atividades realizadas no período\n- Número de beneficiários atendidos\n- Recursos captados e aplicados\n- Parcerias e convênios vigentes\n- Metas x resultados\n\n**Prazo:** até 30 de abril do ano seguinte.\n\n> Exigido pelo MROSC para renovação de parcerias e registro em conselhos.',NULL,1,1,550,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(30,1,'Plano de Trabalho / Plano de Ação Anual','contabil','interno',365,1,0,'## Como obter\n\nElaborado pela diretoria e aprovado em assembleia.\n\n**Conteúdo mínimo:**\n- Objetivos e metas para o período\n- Atividades planejadas\n- Cronograma\n- Recursos previstos\n- Indicadores de acompanhamento\n\n> Essencial para submissão de projetos a editais (MROSC exige plano de trabalho detalhado).',NULL,0,1,560,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(31,1,'Inscrição no CMAS — Conselho Municipal de Assistência Social de Jaboatão','titulacao','municipal',365,1,0,'## Como obter\n\nSolicite ao **Conselho Municipal de Assistência Social de Jaboatão dos Guararapes (CMAS)**.\n\n**Requisitos gerais:**\n- Estatuto registrado com finalidade de assistência social\n- Ata de eleição da diretoria vigente\n- Demonstrações contábeis do último exercício\n- Relatório de atividades\n- Comprovante de endereço\n- CNPJ regular\n\n**Renovação:** geralmente anual ou bienal.\n\n> A inscrição no CMAS é pré-requisito para cadastro no CNEAS e acesso a fundos municipais (FMAS).','https://www.jaboatao.pe.gov.br',0,1,600,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(32,1,'Registro no CMDCA — Conselho Municipal dos Direitos da Criança e do Adolescente','titulacao','municipal',730,1,0,'## Como obter\n\nSolicite ao **CMDCA de Jaboatão dos Guararapes**.\n\n**Requisitos gerais:**\n- Atuar no atendimento de crianças e adolescentes\n- Estatuto com finalidade compatível\n- Documentação completa da entidade\n- Certidões negativas federais, estaduais e municipais\n- Antecedentes criminais dos dirigentes\n\n**Validade:** tipicamente 2 anos.\n\n> Obrigatório para acesso ao FIA (Fundo para a Infância e Adolescência) e participação em editais do CMDCA.','https://www.jaboatao.pe.gov.br',0,1,610,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(33,1,'Registro no Conselho Municipal da Mulher','titulacao','municipal',730,0,0,'## Como obter\n\nSolicite ao **Conselho Municipal da Mulher de Jaboatão dos Guararapes** (se houver).\n\n**Requisitos:** documentação institucional completa + comprovação de atuação na área.\n\n> Verifique se o conselho está ativo no município e quais editais exigem este registro.','https://www.jaboatao.pe.gov.br',0,1,620,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(34,1,'CNEAS — Cadastro Nacional de Entidades de Assistência Social (MDS)','titulacao','federal',730,0,0,'## Como obter\n\nCadastro obrigatório para entidades de assistência social, gerido pelo **Ministério do Desenvolvimento Social (MDS)**.\n\n**Pré-requisito:** Inscrição no CMAS municipal.\n\n**Passos:**\n1. Acesse: https://aplicacoes.mds.gov.br/cneas\n2. Faça login com usuário SUAS Web.\n3. Preencha os dados da entidade e envie a documentação exigida.\n4. Aguarde análise do CMAS e homologação.\n\n> O CNEAS é obrigatório para acesso ao cofinanciamento federal (FNAS) e editais do MDS.','https://aplicacoes.mds.gov.br/cneas',1,1,630,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(35,1,'Título de Utilidade Pública Municipal','titulacao','municipal',NULL,0,0,'## Como obter\n\nSolicite à **Câmara Municipal de Jaboatão dos Guararapes** (processo legislativo).\n\n**Requisitos gerais:**\n- Mínimo de 3 anos de existência comprovada\n- Prestação de serviços relevantes à comunidade local\n- Documentação completa da entidade\n- Sem fins lucrativos comprovado\n\n**Processo:** petição ao vereador ou diretamente à Câmara Municipal.\n\n> O título facilita acesso a editais municipais e pode gerar isenções fiscais.','https://www.jaboatao.pe.gov.br',1,1,640,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(36,1,'CEBAS — Certificação de Entidades Beneficentes de Assistência Social','titulacao','federal',1825,1,0,'## Como obter\n\nCertificação federal concedida pelo **Ministério responsável** conforme a área de atuação (Assistência Social → MDS; Educação → MEC; Saúde → MS).\n\n**Requisitos:**\n- Mínimo 3 anos de existência e funcionamento regular\n- Inscrição no CMAS/CMDCA\n- Prestação gratuita de serviços ao SUS (saúde) ou à rede socioassistencial\n- Demonstrações contábeis dos últimos 3 anos\n\n**Acesso:**\n1. Acesse: https://www.gov.br/mds/pt-br/acoes-e-programas/cebas\n2. Protocole pelo sistema Requerimento CEBAS.\n\n**Validade:** 1 a 5 anos (conforme análise). Renovar com antecedência de 6 meses.\n\n> O CEBAS garante isenção de INSS patronal (cota patronal), COFINS, CSLL e PIS sobre receitas de atividades fins.','https://www.gov.br/mds/pt-br/acoes-e-programas/cebas',1,1,650,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(37,1,'Termo de Adesão de Voluntariado (Lei 9.608/1998)','pessoal','interno',365,0,1,'## Como obter\n\nDocumento elaborado pela própria ONG com base na **Lei 9.608/1998** (Lei do Voluntariado).\n\n**Conteúdo obrigatório:**\n- Identificação da entidade e do voluntário\n- Atividade a ser prestada\n- Condições do serviço voluntário\n- Sem vínculo empregatício\n\n**Modelo:** disponível no planalto: https://www.planalto.gov.br/ccivil_03/leis/L9608.htm\n\n**Validade:** recomenda-se renovar anualmente.\n\n> Sem o termo assinado, o voluntário pode alegar vínculo empregatício.','https://www.planalto.gov.br/ccivil_03/leis/L9608.htm',0,1,700,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(38,1,'Certidão de Antecedentes Criminais — Voluntários com Crianças (Polícia Civil-PE)','pessoal','estadual',180,0,1,'## Como obter\n\nExigida pelo **ECA** para voluntários que atuam diretamente com crianças e adolescentes.\n\n**Passos:**\n1. Acesse: https://www.sds.pe.gov.br (SDS-PE)\n2. Localize o serviço de antecedentes criminais.\n3. O voluntário solicita com seu CPF.\n4. Validade: até 6 meses (verificar exigência do edital/conselho).\n\n> Renove semestralmente para voluntários em contato direto com crianças.','https://www.sds.pe.gov.br',0,1,710,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(39,1,'RG e CPF de Voluntários e Dirigentes','pessoal','interno',NULL,0,1,'## Como obter\n\n**RG:** Secretaria de Defesa Social de PE (SDS-PE): https://www.sds.pe.gov.br\n**CPF:** Receita Federal: https://www.gov.br/receitafederal/pt-br/assuntos/cadastros/cpf\n\n> Solicite cópia autenticada ou apresente o original para autenticação. Armazene com segurança.',NULL,0,1,720,'2026-06-12 18:53:01','2026-06-12 18:53:01');
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `checklists` WRITE;
/*!40000 ALTER TABLE `checklists` DISABLE KEYS */;
INSERT INTO `checklists` VALUES (1,1,'Regularidade Geral da ONG','regularidade-geral','Todas as obrigações documentais da associação — visão completa.',NULL,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(2,1,'MROSC — Parceria com Órgão Público (Lei 13.019/2014, art. 34)','mrosc-parceria','Documentação exigida para celebração de termo de fomento/colaboração conforme MROSC.','Lei 13.019/2014, art. 34',1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(3,1,'Inscrição / Renovação no CMDCA','cmdca','Documentação para inscrição ou renovação no Conselho Municipal dos Direitos da Criança e do Adolescente.','Lei 8.069/1990 (ECA); Resolução CONANDA 137/2010',1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(4,1,'Inscrição no CMAS','cmas','Documentação para inscrição ou renovação no Conselho Municipal de Assistência Social.','Lei 8.742/1993 (LOAS); Resolução CNAS 14/2014',1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(5,1,'Edital de Fundo Municipal (FIA / FMAS)','fundo-municipal','Documentação típica para participação em editais do Fundo para Infância e Adolescência ou Fundo Municipal de Assistência Social.','Lei 8.069/1990 (ECA); Lei 8.742/1993 (LOAS)',1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(6,1,'CEBAS — Certificação de Entidade Beneficente','cebas','Documentação para obtenção ou renovação do CEBAS (Certificação de Entidades Beneficentes de Assistência Social).','Lei 12.101/2009; Decreto 8.242/2014',1,'2026-06-12 18:53:01','2026-06-12 18:53:01');
/*!40000 ALTER TABLE `checklists` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `checklist_items` WRITE;
/*!40000 ALTER TABLE `checklist_items` DISABLE KEYS */;
INSERT INTO `checklist_items` VALUES (1,1,7,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(2,1,12,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(3,1,13,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(4,1,14,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(5,1,18,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(6,1,19,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(7,1,22,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(8,1,2,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(9,1,3,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(10,1,9,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(11,1,24,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(12,1,29,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(13,2,2,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(14,2,3,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(15,2,5,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(16,2,8,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(17,2,7,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(18,2,12,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(19,2,13,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(20,2,14,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(21,2,18,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(22,2,22,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(23,2,24,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(24,2,29,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(25,2,11,1,12,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(26,2,17,1,13,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(27,3,2,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(28,3,3,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(29,3,5,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(30,3,7,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(31,3,12,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(32,3,13,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(33,3,14,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(34,3,18,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(35,3,22,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(36,3,16,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(37,3,38,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(38,3,24,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(39,3,29,1,12,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(40,3,32,1,13,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(41,4,2,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(42,4,3,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(43,4,7,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(44,4,8,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(45,4,12,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(46,4,13,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(47,4,14,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(48,4,18,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(49,4,22,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(50,4,24,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(51,4,29,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(52,4,31,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(53,5,2,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(54,5,3,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(55,5,5,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(56,5,7,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(57,5,12,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(58,5,13,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(59,5,14,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(60,5,18,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(61,5,22,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(62,5,16,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(63,5,24,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(64,5,29,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(65,5,30,1,12,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(66,5,32,1,13,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(67,5,31,1,14,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(68,5,34,1,15,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(69,5,11,1,16,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(70,6,2,1,0,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(71,6,1,1,1,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(72,6,3,1,2,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(73,6,7,1,3,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(74,6,12,1,4,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(75,6,13,1,5,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(76,6,14,1,6,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(77,6,24,1,7,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(78,6,25,1,8,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(79,6,27,1,9,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(80,6,28,1,10,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(81,6,29,1,11,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(82,6,31,1,12,'2026-06-12 18:53:01','2026-06-12 18:53:01'),(83,6,34,1,13,'2026-06-12 18:53:01','2026-06-12 18:53:01');
/*!40000 ALTER TABLE `checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@promessa.org.br',NULL,'$2y$12$ZiDsqMJicePHJgAOSgiEnOEBeKyAdaAguGQJ68sHeum3u2RIXladu',NULL,'2026-06-12 18:54:34','2026-06-12 18:54:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

