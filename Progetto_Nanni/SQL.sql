-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 09, 2026 alle 19:38
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `palestra`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `abbonamento`
--

CREATE TABLE `abbonamento` (
  `id_abbonamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `codice` varchar(30) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `costo` decimal(8,2) NOT NULL,
  `tipo` enum('mensile','annuale') NOT NULL,
  `stato` enum('attivo','scaduto','sospeso','annullato') NOT NULL DEFAULT 'attivo',
  `descrizione` text DEFAULT NULL,
  `rinnovo_automatico` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_mensile` varchar(100) DEFAULT NULL,
  `bonus_annuale` varchar(100) DEFAULT NULL,
  `pagamento_rateizzato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `abbonamento`
--

INSERT INTO `abbonamento` (`id_abbonamento`, `id_cliente`, `codice`, `data_inizio`, `data_fine`, `costo`, `tipo`, `stato`, `descrizione`, `rinnovo_automatico`, `bonus_mensile`, `bonus_annuale`, `pagamento_rateizzato`) VALUES
(8, 15, 'ABB-69F863D4869EB', '2026-05-04', '2027-05-04', 400.00, 'annuale', 'attivo', '', 0, NULL, 'Borsone', 0),
(9, 16, 'ABB-69F864748CD30', '2026-05-04', '2026-06-04', 50.00, 'mensile', 'attivo', '', 0, 'borraccia', NULL, 0),
(11, 19, 'ABB-69FF4FA2B4C47', '2023-01-10', '2024-01-10', 380.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(12, 19, 'ABB-69FF50BA440CD', '2025-02-12', '2026-02-12', 390.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(13, 19, 'ABB-69FF50FBBF019', '2025-01-15', '2026-01-15', 400.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(14, 19, 'ABB-69FF513478C80', '2026-01-15', '2026-02-15', 50.00, 'mensile', 'scaduto', '', 0, NULL, NULL, 0),
(15, 20, 'ABB-69FF52131EB4E', '2024-01-30', '2024-01-30', 40.00, 'mensile', 'scaduto', '', 0, NULL, NULL, 0),
(16, 20, 'ABB-69FF5256E222D', '2026-05-01', '2027-05-10', 400.00, 'annuale', 'attivo', '', 0, NULL, NULL, 0),
(17, 25, 'ABB-SIM001', '2024-09-01', '2025-09-01', 380.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(18, 25, 'ABB-SIM002', '2025-09-01', '2026-09-01', 400.00, 'annuale', 'attivo', '', 0, NULL, 'Borsa omaggio', 0),
(19, 26, 'ABB-SIM003', '2024-03-15', '2024-04-15', 50.00, 'mensile', 'scaduto', '', 0, NULL, NULL, 0),
(20, 26, 'ABB-SIM004', '2024-04-15', '2024-05-15', 50.00, 'mensile', 'scaduto', '', 0, NULL, NULL, 0),
(21, 26, 'ABB-SIM005', '2024-05-15', '2025-05-15', 390.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(22, 26, 'ABB-SIM006', '2025-05-15', '2026-05-15', 400.00, 'annuale', 'attivo', '', 0, NULL, '2 ingressi ospite', 0),
(23, 27, 'ABB-SIM007', '2025-01-20', '2026-01-20', 390.00, 'annuale', 'scaduto', '', 1, NULL, NULL, 0),
(24, 27, 'ABB-SIM008', '2026-01-20', '2027-01-20', 400.00, 'annuale', 'attivo', 'Rinnovo automatico', 1, NULL, 'Lezione prova gratis', 0),
(25, 28, 'ABB-SIM009', '2023-06-10', '2024-06-10', 360.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(26, 28, 'ABB-SIM010', '2024-06-10', '2025-06-10', 380.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(27, 28, 'ABB-SIM011', '2025-06-10', '2026-06-10', 400.00, 'annuale', 'attivo', '', 0, NULL, NULL, 0),
(28, 29, 'ABB-SIM012', '2022-11-05', '2023-11-05', 350.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(29, 29, 'ABB-SIM013', '2023-11-05', '2024-11-05', 370.00, 'annuale', 'scaduto', '', 0, NULL, NULL, 0),
(30, 30, 'ABB-SIM014', '2026-02-28', '2026-03-28', 50.00, 'mensile', 'scaduto', '', 0, 'Borraccia', NULL, 0),
(31, 30, 'ABB-SIM015', '2026-03-28', '2026-04-28', 50.00, 'mensile', 'scaduto', '', 0, NULL, NULL, 0),
(32, 30, 'ABB-SIM016', '2026-04-28', '2026-05-28', 50.00, 'mensile', 'attivo', '', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `attrezzatura`
--

CREATE TABLE `attrezzatura` (
  `id_attrezzatura` int(11) NOT NULL,
  `cod_inventario` varchar(30) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modello` varchar(50) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `tipologia` varchar(50) DEFAULT NULL,
  `data_acquisto` date DEFAULT NULL,
  `descrizione` text DEFAULT NULL,
  `stato` enum('funzionante','in_riparazione','dismessa') NOT NULL DEFAULT 'funzionante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `attrezzatura`
--

INSERT INTO `attrezzatura` (`id_attrezzatura`, `cod_inventario`, `marca`, `modello`, `nome`, `tipologia`, `data_acquisto`, `descrizione`, `stato`) VALUES
(1, 'att', 'technogym', '3424', 'tapis', '244', '0535-03-05', '', 'funzionante'),
(2, 'rrwef', 'erqrew', 'rewtt', 'tapis', 'rt', '0044-06-05', 'qr', 'funzionante'),
(4, 'ATT-001', 'Technogym', '', 'chest press', 'Pesi', '2025-05-04', '', 'funzionante'),
(5, 'ATT-002', 'Technogym', '', 'Leg Press', 'Pesi', '2025-03-02', '', 'funzionante'),
(6, 'ATT-003', 'Decathlon', '', 'Palla Medica', '', '2026-01-20', '', 'funzionante'),
(7, 'ATT-004', 'Decathlon', '', 'Elastico', '', '2026-01-20', '', 'funzionante'),
(9, 'ATT-005', 'Technogym', 'Bike 700', 'Cyclette', 'Cardio', '2024-01-15', '', 'funzionante'),
(10, 'ATT-006', 'Technogym', 'Run 600', 'Tapis Roulant', 'Cardio', '2024-01-15', '', 'funzionante'),
(11, 'ATT-007', 'Life Fitness', 'E1', 'Ellittica', 'Cardio', '2024-03-20', '', 'funzionante'),
(12, 'ATT-008', 'Salter', 'Pro', 'Bilanciere', 'Pesi', '2023-06-10', '', 'funzionante'),
(13, 'ATT-009', 'Decathlon', 'Essential', 'Manubri set', 'Pesi', '2023-06-10', '', 'funzionante'),
(14, 'ATT-010', 'Decathlon', 'Comfort', 'Tappetino yoga', 'Corsi', '2025-01-05', '', 'funzionante'),
(15, 'ATT-011', 'Balanced Body', 'Studio', 'Reformer Pilates', 'Corsi', '2024-09-01', '', 'funzionante'),
(16, 'ATT-012', 'Kettler', 'Sport', 'Kettlebell set', 'Pesi', '2023-11-20', '', 'in_riparazione');

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente`
--

CREATE TABLE `cliente` (
  `id_persona` int(11) NOT NULL,
  `stato_iscrizione` enum('attivo','sospeso','scaduto') NOT NULL DEFAULT 'attivo',
  `data_iscrizione` date NOT NULL,
  `certificato_medico_scadenza` date DEFAULT NULL,
  `obiettivo` varchar(200) DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato') NOT NULL DEFAULT 'principiante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cliente`
--

INSERT INTO `cliente` (`id_persona`, `stato_iscrizione`, `data_iscrizione`, `certificato_medico_scadenza`, `obiettivo`, `livello`) VALUES
(15, 'attivo', '2026-05-04', '2026-05-06', 'massa muscolare', 'avanzato'),
(16, 'attivo', '2026-05-04', '2026-05-07', 'massa muscolare', 'avanzato'),
(19, 'attivo', '2023-01-10', '2026-05-05', 'dimagrire', 'intermedio'),
(20, 'attivo', '2024-01-30', '2026-12-01', 'tonificare', 'principiante'),
(25, 'attivo', '2025-09-01', '2026-09-01', 'dimagrire', 'principiante'),
(26, 'attivo', '2024-03-15', '2026-11-15', 'massa muscolare', 'avanzato'),
(27, 'attivo', '2025-01-20', '2026-08-20', 'tonificare', 'intermedio'),
(28, 'attivo', '2023-06-10', '2026-06-10', 'resistenza', 'intermedio'),
(29, 'scaduto', '2022-11-05', '2025-11-05', 'dimagrire', 'principiante'),
(30, 'attivo', '2026-02-28', '2027-02-28', 'tonificare', 'principiante');

-- --------------------------------------------------------

--
-- Struttura della tabella `contiene`
--

CREATE TABLE `contiene` (
  `id_sala` int(11) NOT NULL,
  `id_attrezzatura` int(11) NOT NULL,
  `quantita` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `contiene`
--

INSERT INTO `contiene` (`id_sala`, `id_attrezzatura`, `quantita`) VALUES
(4, 4, 2),
(4, 5, 2),
(4, 9, 6),
(4, 10, 4),
(4, 12, 3),
(4, 13, 1),
(4, 16, 1),
(5, 6, 4),
(5, 7, 10),
(5, 11, 2),
(5, 14, 20),
(5, 15, 6),
(6, 14, 20),
(7, 14, 15);

-- --------------------------------------------------------

--
-- Struttura della tabella `corso`
--

CREATE TABLE `corso` (
  `id_corso` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato') NOT NULL,
  `durata_minuti` int(11) NOT NULL,
  `max_partecipanti` int(11) NOT NULL,
  `stato` enum('attivo','sospeso','terminato') NOT NULL DEFAULT 'attivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `corso`
--

INSERT INTO `corso` (`id_corso`, `nome`, `descrizione`, `livello`, `durata_minuti`, `max_partecipanti`, `stato`) VALUES
(5, 'Yoga', '', 'principiante', 30, 20, 'attivo'),
(6, 'Pilates', '', 'principiante', 30, 20, 'attivo'),
(7, 'Funzionale', '', 'intermedio', 45, 25, 'attivo'),
(8, 'Spinning', '', 'avanzato', 60, 15, 'attivo'),
(9, 'Posturale', '', 'principiante', 30, 20, 'attivo'),
(10, 'HIIT', '', 'intermedio', 45, 20, 'attivo');

-- --------------------------------------------------------

--
-- Struttura della tabella `iscrizione`
--

CREATE TABLE `iscrizione` (
  `id_iscrizione` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_corso` int(11) NOT NULL,
  `data_iscrizione` date NOT NULL DEFAULT curdate(),
  `certificato_medico` tinyint(1) NOT NULL DEFAULT 0,
  `scadenza_iscrizione` date DEFAULT NULL,
  `stato` enum('attiva','sospesa','annullata') NOT NULL DEFAULT 'attiva'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `iscrizione`
--

INSERT INTO `iscrizione` (`id_iscrizione`, `id_cliente`, `id_corso`, `data_iscrizione`, `certificato_medico`, `scadenza_iscrizione`, `stato`) VALUES
(1, 15, 5, '2026-05-04', 1, '2027-05-04', 'attiva'),
(2, 15, 7, '2026-05-04', 1, '2027-05-04', 'attiva'),
(3, 16, 6, '2026-05-04', 1, '2027-05-04', 'attiva'),
(4, 16, 8, '2026-05-04', 1, '2027-05-04', 'attiva'),
(5, 19, 7, '2023-01-10', 1, '2024-01-10', 'annullata'),
(6, 19, 9, '2025-02-12', 1, '2026-02-12', 'attiva'),
(7, 20, 5, '2024-01-30', 1, '2025-01-30', 'attiva'),
(8, 20, 6, '2024-01-30', 1, '2025-01-30', 'attiva'),
(9, 25, 5, '2025-09-01', 1, '2026-09-01', 'attiva'),
(10, 25, 9, '2025-09-01', 1, '2026-09-01', 'attiva'),
(11, 26, 7, '2024-03-15', 1, '2026-03-15', 'attiva'),
(12, 26, 8, '2024-03-15', 1, '2026-03-15', 'attiva'),
(13, 26, 10, '2024-03-15', 1, '2026-03-15', 'attiva'),
(14, 27, 6, '2025-01-20', 1, '2026-01-20', 'attiva'),
(15, 27, 9, '2025-01-20', 1, '2026-01-20', 'attiva'),
(16, 28, 7, '2023-06-10', 1, '2026-06-10', 'attiva'),
(17, 28, 10, '2023-06-10', 1, '2026-06-10', 'attiva'),
(18, 29, 5, '2022-11-05', 1, '2024-11-05', 'annullata'),
(19, 30, 5, '2026-02-28', 1, '2027-02-28', 'attiva'),
(20, 30, 6, '2026-02-28', 1, '2027-02-28', 'attiva');

-- --------------------------------------------------------

--
-- Struttura della tabella `istruttore`
--

CREATE TABLE `istruttore` (
  `id_persona` int(11) NOT NULL,
  `tipo_contratto` enum('dipendente','collaboratore','partita_iva') NOT NULL,
  `stipendio` decimal(8,2) DEFAULT NULL,
  `data_assunzione` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `istruttore`
--

INSERT INTO `istruttore` (`id_persona`, `tipo_contratto`, `stipendio`, `data_assunzione`) VALUES
(17, 'dipendente', 1600.00, '2026-05-04'),
(18, 'dipendente', 1600.00, '2026-05-04'),
(21, 'partita_iva', 2200.00, '2024-05-01'),
(23, 'collaboratore', 1100.00, '2025-12-12'),
(24, 'collaboratore', 1100.00, '2026-01-11'),
(31, 'dipendente', 1700.00, '2023-03-01'),
(32, 'collaboratore', 1200.00, '2024-10-15');

-- --------------------------------------------------------

--
-- Struttura della tabella `lezione`
--

CREATE TABLE `lezione` (
  `id_lezione` int(11) NOT NULL,
  `id_corso` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL,
  `tipo_lezione` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `stato` enum('programmata','in_corso','completata','annullata') NOT NULL DEFAULT 'programmata'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `lezione`
--

INSERT INTO `lezione` (`id_lezione`, `id_corso`, `id_sala`, `data`, `ora_inizio`, `ora_fine`, `tipo_lezione`, `note`, `stato`) VALUES
(5, 7, 5, '2026-05-06', '10:00:00', '10:45:00', 'Gruppo', '', 'completata'),
(6, 6, 5, '2026-07-05', '11:00:00', '11:30:00', 'Gruppo', '', 'programmata'),
(7, 5, 5, '2026-04-07', '09:00:00', '09:30:00', 'Gruppo', '', 'completata'),
(8, 6, 5, '2026-04-08', '10:00:00', '10:30:00', 'Gruppo', '', 'completata'),
(9, 7, 6, '2026-04-10', '11:00:00', '11:45:00', 'Gruppo', '', 'completata'),
(10, 8, 6, '2026-04-14', '18:00:00', '19:00:00', 'Gruppo', '', 'completata'),
(11, 9, 5, '2026-04-15', '09:30:00', '10:00:00', 'Gruppo', '', 'completata'),
(12, 10, 6, '2026-04-17', '19:00:00', '19:45:00', 'Gruppo', '', 'completata'),
(13, 5, 5, '2026-04-21', '09:00:00', '09:30:00', 'Gruppo', '', 'completata'),
(14, 6, 5, '2026-04-22', '10:00:00', '10:30:00', 'Gruppo', '', 'completata'),
(15, 7, 6, '2026-04-24', '11:00:00', '11:45:00', 'Gruppo', '', 'completata'),
(16, 8, 6, '2026-04-28', '18:00:00', '19:00:00', 'Gruppo', '', 'completata'),
(17, 5, 5, '2026-05-12', '09:00:00', '09:30:00', 'Gruppo', '', 'programmata'),
(18, 6, 5, '2026-05-13', '10:00:00', '10:30:00', 'Gruppo', '', 'programmata'),
(19, 7, 6, '2026-05-14', '11:00:00', '11:45:00', 'Gruppo', '', 'programmata'),
(20, 8, 6, '2026-05-15', '18:00:00', '19:00:00', 'Gruppo', '', 'programmata'),
(21, 9, 5, '2026-05-16', '09:30:00', '10:00:00', 'Gruppo', '', 'programmata'),
(22, 10, 6, '2026-05-19', '19:00:00', '19:45:00', 'Gruppo', '', 'programmata'),
(23, 5, 5, '2026-05-20', '09:00:00', '09:30:00', 'Gruppo', '', 'programmata'),
(24, 6, 5, '2026-05-21', '10:00:00', '10:30:00', 'Gruppo', '', 'programmata'),
(25, 7, 6, '2026-05-22', '11:00:00', '11:45:00', 'Gruppo', '', 'programmata'),
(26, 8, 6, '2026-05-26', '18:00:00', '19:00:00', 'Gruppo', '', 'programmata'),
(27, 9, 5, '2026-05-27', '09:30:00', '10:00:00', 'Gruppo', '', 'programmata'),
(28, 10, 6, '2026-05-28', '19:00:00', '19:45:00', 'Gruppo', '', 'programmata');

-- --------------------------------------------------------

--
-- Struttura della tabella `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_abbonamento` int(11) NOT NULL,
  `importo` decimal(8,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `metodo_pagamento` enum('contanti','carta','bonifico','paypal') NOT NULL,
  `stato` enum('completato','in_attesa','fallito','rimborsato') NOT NULL DEFAULT 'completato',
  `data_scadenza` date DEFAULT NULL,
  `codice_transazione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `pagamento`
--

INSERT INTO `pagamento` (`id_pagamento`, `id_abbonamento`, `importo`, `data_pagamento`, `metodo_pagamento`, `stato`, `data_scadenza`, `codice_transazione`) VALUES
(2, 9, 50.00, '2026-05-04', 'contanti', 'completato', NULL, 'TR-00001'),
(3, 8, 400.00, '2026-05-04', 'contanti', 'completato', NULL, 'TR-00002'),
(4, 17, 380.00, '2024-09-01', 'carta', 'completato', NULL, 'TXN-SIM001'),
(5, 18, 400.00, '2025-09-01', 'carta', 'completato', NULL, 'TXN-SIM002'),
(6, 19, 50.00, '2024-03-15', 'contanti', 'completato', NULL, NULL),
(7, 20, 50.00, '2024-04-15', 'contanti', 'completato', NULL, NULL),
(8, 21, 390.00, '2024-05-15', 'bonifico', 'completato', NULL, 'TXN-SIM003'),
(9, 22, 400.00, '2025-05-15', 'carta', 'completato', NULL, 'TXN-SIM004'),
(10, 23, 390.00, '2025-01-20', 'paypal', 'completato', NULL, 'TXN-SIM005'),
(11, 24, 400.00, '2026-01-20', 'paypal', 'completato', NULL, 'TXN-SIM006'),
(12, 25, 360.00, '2023-06-10', 'contanti', 'completato', NULL, NULL),
(13, 26, 380.00, '2024-06-10', 'carta', 'completato', NULL, 'TXN-SIM007'),
(14, 27, 400.00, '2025-06-10', 'carta', 'completato', NULL, 'TXN-SIM008'),
(15, 28, 350.00, '2022-11-05', 'contanti', 'completato', NULL, NULL),
(16, 29, 370.00, '2023-11-05', 'bonifico', 'completato', NULL, 'TXN-SIM009'),
(17, 30, 50.00, '2026-02-28', 'contanti', 'completato', NULL, NULL),
(18, 31, 50.00, '2026-03-28', 'contanti', 'completato', NULL, NULL),
(19, 32, 50.00, '2026-04-28', 'carta', 'completato', NULL, 'TXN-SIM010');

-- --------------------------------------------------------

--
-- Struttura della tabella `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `ruolo` enum('admin','cliente','istruttore') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `persona`
--

INSERT INTO `persona` (`id_persona`, `username`, `email`, `password`, `nome`, `cognome`, `telefono`, `ruolo`) VALUES
(1, 'admin', 'admin@palestra.it', '$2y$10$OZsvOr4QC/ycKHoxX6v20urH/VGTGa2ZG4Wvoa7w7l6UAqxYZSLtq', 'Mario', 'Rossi', '3331234567', 'admin'),
(15, 'Federico', 'federico.rennella@gmail.com', '$2y$10$ji42h3sQNwD3CKMvuA01ZO2Dmo7/GhzWC.SS4hwCYnIfLj8wZi5cO', 'Federico', 'Rennella', '3511995572', 'cliente'),
(16, 'Valerio', 'valerio.ottani@gmail.com', '$2y$10$l0h/Jxk.zW4SdJxO/RgLJusM/6Xj86Zhw5leBYxb5W6/1bXO3ghUO', 'Valerio', 'Ottani', '3271023620', 'cliente'),
(17, 'istr_69f864c47d1ea', 'luca.bianchi@gmail.com', '$2y$10$N/qcpabcXK8d0Bxu75REz.9idCfA.g43EcqzX2p91MacA1E6HSoFK', 'Luca', 'Bianchi', '3344578622', 'istruttore'),
(18, 'istr_69f864f2f215a', 'stefano.rosso@gmail.com', '$2y$10$PM8tRO8J3Y30UbRNFLnRA.1/ZF/N9YL3FPoiNsPTVuwivJRD3hE4m', 'Stefano', 'Rosso', '3256538799', 'istruttore'),
(19, 'Matteo', 'matteo.verdi@virgilio.it', '$2y$10$hRgT8Vk.0eDEkyp4pTepD.PCd33/5ucNIRRaf1utAFmnYUrnAdffK', 'Matteo', 'Verdi', '3284365543', 'cliente'),
(20, 'Alice', 'alice.viola@gmail.com', '$2y$10$Ktg8wXrWbJOrMLZ9wEUsdu2jtc.TVnewxh5gCZV9V2jErykEFSQd6', 'Alice', 'Viola', '3564487961', 'cliente'),
(21, 'istr_69ff52ca8ce45', 'roberto.guerra@virgilio.it', '$2y$10$PCLdvOkLAxbeU9fypFn.T.L2kCIPEs.4BxzYF3xkN.GLJ2Dn.oFw.', 'Roberto', 'Guerra', '3456676221', 'istruttore'),
(23, 'istr_69ff533482cea', 'alberto.ferrari@gmail.com', '$2y$10$ZvGldPrviC1idy42PjKT5uBQzhWb.v89ooz7Fsg/hV68lAuU386aK', 'Alberto', 'Ferrari', '3331287664', 'istruttore'),
(24, 'istr_69ff537bb7b48', 'federico.esposito@virgilio.it', '$2y$10$4hVfvAbA3SLel5F/DfUDCODGZoeTngV.zKkVmICaBVFvtCdIENBtu', 'Federico', 'Esposito', '3421567787', 'istruttore'),
(25, 'giulia.ferrari', 'giulia.ferrari@virgilio.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Giulia', 'Ferrari', '3331122334', 'cliente'),
(26, 'marco.ricci', 'marco.ricci@virgilio.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Marco', 'Ricci', '3342233445', 'cliente'),
(27, 'sofia.conti', 'sofia.conti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Sofia', 'Conti', '3353344556', 'cliente'),
(28, 'luca.martini', 'luca.martini@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Luca', 'Martini', '3364455667', 'cliente'),
(29, 'chiara.russo', 'chiara.russo@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Chiara', 'Russo', '3375566778', 'cliente'),
(30, 'andrea.gallo', 'andrea.gallo@virgilio.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Andrea', 'Gallo', '3386677889', 'cliente'),
(31, 'istr_31auto', 'valentina.moro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Valentina', 'Moro', '3397788990', 'istruttore'),
(32, 'istr_32auto', 'davide.fontana@virgilio.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uivHd/Hz2', 'Davide', 'Fontana', '3308899001', 'istruttore');

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazione`
--

CREATE TABLE `prenotazione` (
  `id_prenotazione` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_lezione` int(11) NOT NULL,
  `data_prenotazione` datetime NOT NULL DEFAULT current_timestamp(),
  `stato` enum('confermata','in_attesa','annullata','completata') NOT NULL DEFAULT 'confermata',
  `presenza` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prenotazione`
--

INSERT INTO `prenotazione` (`id_prenotazione`, `id_cliente`, `id_lezione`, `data_prenotazione`, `stato`, `presenza`) VALUES
(9, 15, 5, '2026-05-04 11:51:18', 'confermata', 0),
(10, 16, 5, '2026-05-04 11:52:14', 'confermata', 0),
(11, 15, 6, '2026-05-04 12:33:43', 'confermata', 1),
(12, 16, 6, '2026-05-09 17:17:22', 'confermata', 0),
(13, 25, 7, '2026-04-06 09:00:00', 'completata', 1),
(14, 26, 9, '2026-04-09 10:00:00', 'completata', 1),
(15, 27, 8, '2026-04-07 11:00:00', 'completata', 0),
(16, 28, 9, '2026-04-09 08:00:00', 'completata', 1),
(17, 25, 11, '2026-04-14 09:00:00', 'completata', 1),
(18, 26, 12, '2026-04-16 18:00:00', 'completata', 1),
(19, 27, 13, '2026-04-20 09:00:00', 'completata', 1),
(20, 28, 15, '2026-04-23 10:00:00', 'completata', 0),
(21, 30, 7, '2026-04-06 09:00:00', 'completata', 1),
(22, 15, 7, '2026-04-06 09:30:00', 'completata', 1),
(23, 16, 8, '2026-04-07 10:00:00', 'completata', 1),
(24, 25, 17, '2026-05-09 08:00:00', 'confermata', 0),
(25, 26, 19, '2026-05-09 09:00:00', 'confermata', 0),
(26, 27, 18, '2026-05-09 10:00:00', 'confermata', 0),
(27, 28, 19, '2026-05-09 08:30:00', 'confermata', 0),
(28, 30, 17, '2026-05-09 09:00:00', 'confermata', 0),
(29, 15, 17, '2026-05-09 09:30:00', 'confermata', 0),
(30, 16, 18, '2026-05-09 10:30:00', 'confermata', 0),
(31, 25, 21, '2026-05-09 11:00:00', 'confermata', 0),
(32, 20, 17, '2026-05-09 11:30:00', 'confermata', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `sala`
--

CREATE TABLE `sala` (
  `id_sala` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipologia` varchar(50) DEFAULT NULL,
  `capienza_max` int(11) NOT NULL,
  `stato` enum('disponibile','in_manutenzione','chiusa') NOT NULL DEFAULT 'disponibile',
  `data_ultima_manutenzione` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `sala`
--

INSERT INTO `sala` (`id_sala`, `nome`, `tipologia`, `capienza_max`, `stato`, `data_ultima_manutenzione`) VALUES
(4, 'Sala 1', 'Pesi', 50, 'disponibile', '2025-08-25'),
(5, 'Sala 2', 'Corsi', 25, 'disponibile', '2024-02-02'),
(6, 'Sala 3', 'Corsi', 30, 'disponibile', '2026-05-05'),
(7, 'Sala 4', 'Corsi', 30, 'disponibile', '2025-12-12');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `abbonamento`
--
ALTER TABLE `abbonamento`
  ADD PRIMARY KEY (`id_abbonamento`),
  ADD UNIQUE KEY `uq_codice` (`codice`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indici per le tabelle `attrezzatura`
--
ALTER TABLE `attrezzatura`
  ADD PRIMARY KEY (`id_attrezzatura`),
  ADD UNIQUE KEY `uq_cod_inventario` (`cod_inventario`);

--
-- Indici per le tabelle `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indici per le tabelle `contiene`
--
ALTER TABLE `contiene`
  ADD PRIMARY KEY (`id_sala`,`id_attrezzatura`),
  ADD KEY `id_attrezzatura` (`id_attrezzatura`);

--
-- Indici per le tabelle `corso`
--
ALTER TABLE `corso`
  ADD PRIMARY KEY (`id_corso`);

--
-- Indici per le tabelle `iscrizione`
--
ALTER TABLE `iscrizione`
  ADD PRIMARY KEY (`id_iscrizione`),
  ADD UNIQUE KEY `uq_cliente_corso` (`id_cliente`,`id_corso`),
  ADD KEY `id_corso` (`id_corso`);

--
-- Indici per le tabelle `istruttore`
--
ALTER TABLE `istruttore`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indici per le tabelle `lezione`
--
ALTER TABLE `lezione`
  ADD PRIMARY KEY (`id_lezione`),
  ADD KEY `id_corso` (`id_corso`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Indici per le tabelle `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_abbonamento` (`id_abbonamento`);

--
-- Indici per le tabelle `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indici per le tabelle `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD PRIMARY KEY (`id_prenotazione`),
  ADD UNIQUE KEY `uq_cliente_lezione` (`id_cliente`,`id_lezione`),
  ADD KEY `id_lezione` (`id_lezione`);

--
-- Indici per le tabelle `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `abbonamento`
--
ALTER TABLE `abbonamento`
  MODIFY `id_abbonamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT per la tabella `attrezzatura`
--
ALTER TABLE `attrezzatura`
  MODIFY `id_attrezzatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT per la tabella `corso`
--
ALTER TABLE `corso`
  MODIFY `id_corso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `iscrizione`
--
ALTER TABLE `iscrizione`
  MODIFY `id_iscrizione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `lezione`
--
ALTER TABLE `lezione`
  MODIFY `id_lezione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT per la tabella `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  MODIFY `id_prenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT per la tabella `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `abbonamento`
--
ALTER TABLE `abbonamento`
  ADD CONSTRAINT `abbonamento_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`);

--
-- Limiti per la tabella `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `contiene`
--
ALTER TABLE `contiene`
  ADD CONSTRAINT `contiene_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `contiene_ibfk_2` FOREIGN KEY (`id_attrezzatura`) REFERENCES `attrezzatura` (`id_attrezzatura`) ON DELETE CASCADE;

--
-- Limiti per la tabella `iscrizione`
--
ALTER TABLE `iscrizione`
  ADD CONSTRAINT `iscrizione_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`),
  ADD CONSTRAINT `iscrizione_ibfk_2` FOREIGN KEY (`id_corso`) REFERENCES `corso` (`id_corso`);

--
-- Limiti per la tabella `istruttore`
--
ALTER TABLE `istruttore`
  ADD CONSTRAINT `istruttore_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `lezione`
--
ALTER TABLE `lezione`
  ADD CONSTRAINT `lezione_ibfk_1` FOREIGN KEY (`id_corso`) REFERENCES `corso` (`id_corso`),
  ADD CONSTRAINT `lezione_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`);

--
-- Limiti per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_abbonamento`) REFERENCES `abbonamento` (`id_abbonamento`);

--
-- Limiti per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD CONSTRAINT `prenotazione_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`),
  ADD CONSTRAINT `prenotazione_ibfk_2` FOREIGN KEY (`id_lezione`) REFERENCES `lezione` (`id_lezione`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
